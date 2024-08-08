<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\User;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Traits\Upload;
use Exception;

class FileControllerNew extends Controller
{
    use Upload;

    public function index(Request $request)
    {
        try {
            $query = File::query();

            // Filter by user_id
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Search
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by folder
            if ($request->has('folder_id')) {
                $query->where('folder_id', $request->folder_id);
            }

            // Sorting
            if ($request->has('sort_by') && $request->has('sort_order')) {
                $query->orderBy($request->sort_by, $request->sort_order);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 10); // Default to 10 items per page
            // $files = $query->paginate($perPage)->appends($request->except('page'));
            $files = $query->get();

            return response()->json($files, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving files.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $file = File::findOrFail($id);
            return response()->json($file, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'File not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving the file.'], 500);
        }
    }

    public function getAllFiles(Request $request)
    {
        try {
            $query = File::query();

            // Filter by user_id if provided
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $files = $query->get();
            return response()->json($files, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving files.'], 500);
        }
    }

    public function getAllFilesWithoutUserId()
    {
        try {
            $files = File::all();
            return response()->json($files, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving files.'], 500);
        }
    }



    public function upload(Request $request)
    {
        try {
            $request->validate([
                'files.*' => 'required|file|max:1024000', // Max file size 10MB per file
                'folder_id' => 'nullable|exists:folders,id',
                'user_id' => 'required|exists:users,id'
            ]);

            // Get user and check quota
            $user = User::findOrFail($request->user_id);
            $usedSize = File::where('user_id', $user->id)->sum('size');
            $config = DB::table('config')->where('name', 'quota_limit')->first();
            $quotaLimit = $config ? $config->value : 0;

            // Calculate total size of files being uploaded
            $totalUploadSize = 0;
            $fileDetails = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $totalUploadSize += $file->getSize();
                    $fileDetails[] = [
                        'path' => $this->uploadFile($file, 'external_files'),
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize()
                    ];
                }
            }

            // Check if the user has enough quota left
            if (($usedSize + $totalUploadSize) > $quotaLimit) {
                return response()->json(['error' => 'Quota limit exceeded.'], 400);
            }

            // Store file records
            foreach ($fileDetails as $fileDetail) {
                $file = File::create([
                    'user_id' => $user->id,
                    'folder_id' => $request->folder_id,
                    'name' => $fileDetail['name'],
                    'path' => $fileDetail['path'],
                    'size' => $fileDetail['size'],
                ]);
            }

            // Update user quota
            $user->used_quota += $totalUploadSize;
            $user->save();

            // Update folder sizes if applicable
            if ($request->has('folder_id')) {
                $this->updateFolderSize($request->folder_id);
            }
            // return $fileDetails;
            return response()->json($fileDetails, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading the files.'], 500);
        }
    }




    public function rename(Request $request, $id)
    {
        try {
            $file = File::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $file->name = $request->name;
            $file->save();

            return response()->json($file, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'File not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while renaming the file.'], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'ids.*' => 'exists:files,id',
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $ids = $request->input('ids'); // Expecting an array of IDs

            if (is_array($ids)) {
                // Bulk delete
                $files = File::whereIn('id', $ids)->where('user_id', $request->user_id)->get();
                $responseMessages = [];

                foreach ($files as $file) {
                    if ($file->delete()) {
                        // Storage::delete($file->path); // Optionally delete file from storage
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' successfully moved to trash.'];
                    } else {
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' could not be moved to trash.'];
                    }
                }

                return response()->json(['messages' => $responseMessages], 200);
            } else {
                // Single delete
                $file = File::where('user_id', $request->user_id)->findOrFail($ids);
                if ($file->delete()) {
                    return response()->json(['message' => 'File with ID ' . $file->id . ' successfully moved to trash.'], 200);
                } else {
                    return response()->json(['message' => 'File with ID ' . $file->id . ' could not be moved to trash.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the file(s).' . $e], 500);
        }
    }




    public function restore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:files,id',
        ]);

        try {
            $ids = $request->input('ids'); // Expecting an array of IDs

            if (is_array($ids)) {
                // Bulk restore
                $files = File::onlyTrashed()->whereIn('id', $ids)->where('user_id', $request->user_id)->get();
                $responseMessages = [];

                foreach ($files as $file) {
                    if ($file->restore()) {
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' successfully restored from trash.'];
                    } else {
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' could not be restored.'];
                    }
                }

                return response()->json(['messages' => $responseMessages], 200);
            } else {
                // Single restore
                $file = File::onlyTrashed()->where('user_id', $request->user_id)->findOrFail($ids);
                if ($file->restore()) {
                    return response()->json(['message' => 'File with ID ' . $file->id . ' successfully restored from trash.'], 200);
                } else {
                    return response()->json(['message' => 'File with ID ' . $file->id . ' could not be restored.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while restoring the file(s).'], 500);
        }
    }




    public function permanentlyDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'ids.*' => 'exists:files,id',
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $ids = $request->input('ids'); // Expecting an array of IDs
            $user = User::findOrFail($request->user_id); // Fetch the user

            if (is_array($ids)) {
                // Bulk permanently delete
                $files = File::onlyTrashed()->whereIn('id', $ids)->where('user_id', $user->id)->get();
                $responseMessages = [];
                $totalSize = 0;

                foreach ($files as $file) {
                    $this->deleteFile($file->path); // Delete file from storage
                    $totalSize += $file->size; // Accumulate total size of deleted files
                    if ($file->forceDelete()) {
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' permanently deleted.'];
                    } else {
                        $responseMessages[] = ['id' => $file->id, 'message' => 'File with ID ' . $file->id . ' could not be permanently deleted.'];
                    }
                }

                // Update user quota
                $user->used_quota -= $totalSize;
                $user->save();

                // Update folder sizes
                foreach ($files->pluck('folder_id')->unique() as $folderId) {
                    $this->updateFolderSize($folderId);
                }

                return response()->json(['messages' => $responseMessages], 200);
            } else {
                // Single permanently delete
                $file = File::onlyTrashed()->where('user_id', $user->id)->findOrFail($ids);
                $this->deleteFile($file->path); // Delete file from storage

                if ($file->forceDelete()) {
                    // Update user quota
                    $user->used_quota -= $file->size;
                    $user->save();

                    // Update folder size
                    if ($file->folder_id) {
                        $this->updateFolderSize($file->folder_id);
                    }

                    return response()->json(['message' => 'File with ID ' . $file->id . ' permanently deleted.'], 200);
                } else {
                    return response()->json(['message' => 'File with ID ' . $file->id . ' could not be permanently deleted.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while permanently deleting the file(s).'], 500);
        }
    }



    public function trash(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        try {
            $query = File::onlyTrashed()->where('user_id', $request->user_id);

            // Search
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Pagination
            // $perPage = $request->get('per_page', 10);
            // $files = $query->paginate($perPage)->appends($request->except('page'));
            $files = $query->get();

            if ($files->isEmpty()) {
                return response()->json(['message' => 'No trashed files found.'], 200);
            }

            return response()->json([
                'message' => 'Trashed files retrieved successfully.',
                'data' => $files
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving trashed files.'], 500);
        }
    }

    protected function updateFolderSize($folderId)
    {
        if ($folderId) {
            $folder = Folder::find($folderId);
            if ($folder) {
                $folder->size = File::where('folder_id', $folderId)->sum('size')
                    + Folder::where('parent_id', $folder->id)->sum('size');
                $folder->save();
                // Update parent folder size
                $this->updateFolderSize($folder->parent_id);
            }


            // if ($folder) {
            //     // Calculate the size of all child folders and files
            //     $folder->size = Folder::where('parent_id', $folder->id)->sum('size')
            //                   + File::where('folder_id', $folder->id)->sum('size');
            //     $folder->save();
            //     // Update parent folder size
            //     $this->updateFolderSize(Folder::find($folder->parent_id));
            // }
        }
    }
}
