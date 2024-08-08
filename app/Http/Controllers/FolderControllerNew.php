<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Traits\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\File;
use Exception;

class FolderControllerNew extends Controller
{

    use Upload;

    public function index(Request $request)
    {
        try {
            $query = Folder::query();

            // Filter by user_id
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Search
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by parent folder
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }

            // Sorting
            if ($request->has('sort_by') && $request->has('sort_order')) {
                $query->orderBy($request->sort_by, $request->sort_order);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination
            // $perPage = $request->get('per_page', 10); // Default to 10 items per page
            // $folders = $query->paginate($perPage)->appends($request->except('page'));
            $folders = $query->get();

            return response()->json($folders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving folders.' . $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $folder = Folder::findOrFail($id);
            return response()->json($folder, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Folder not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving the folder.'], 500);
        }
    }

    public function getAllFolders(Request $request)
    {
        try {
            $query = Folder::query();

            // Filter by user_id if provided
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Fetch folders
            $folders = $query->get();

            // Check if folders are found
            if ($folders->isEmpty()) {
                return response()->json([
                    'message' => 'No folders found.',
                    'suggestion' => 'Please ensure the user ID is correct, or try creating a folder if you have none.',
                ], 404);
            }

            return response()->json($folders, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving folders. Please try again later.',
            ], 500);
        }
    }


    public function getAllFoldersWithoutUserId()
    {
        try {
            $folders = Folder::all();
            return response()->json($folders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving folders.'], 500);
        }
    }


    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
                'parent_id' => 'nullable|exists:folders,id', // Parent folder is optional
            ]);

            $userId = $request->user_id;
            $usedSize = Folder::where('user_id', $userId)->sum('size');
            $config = DB::table('config')->where('name', 'quota_limit')->first();
            $quotaLimit = $config ? $config->value : 0;

            if ($usedSize >= $quotaLimit) {
                return response()->json(['error' => 'Quota limit exceeded.'], 400);
            }

            // Create the folder
            $folder = Folder::create([
                'user_id' => $userId,
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'size' => 0, // Initialize size
            ]);

            // Update sizes of affected folders
            $this->updateFolderSize($folder);

            // Update user quota
            $this->updateUserQuota($userId);

            return response()->json($folder, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the folder. ' . $e->getMessage()], 500);
        }
    }

    public function rename(Request $request, $id)
    {
        try {
            $folder = Folder::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $folder->name = $request->name;
            $folder->save();

            return response()->json($folder, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Folder not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while renaming the folder.'], 500);
        }
    }


    public function delete(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'ids.*' => 'exists:folders,id',
        ]);

        try {
            $ids = $request->input('ids'); // Expecting an array of IDs

            if (is_array($ids)) {
                // Bulk delete
                $folders = Folder::whereIn('id', $ids)->where('user_id', $request->user_id)->get();

                $responseMessages = [];

                foreach ($folders as $folder) {
                    if ($folder->delete()) {
                        $responseMessages[] = ['id' => $folder->id, 'message' => 'Folder with ID ' . $folder->id . ' successfully moved to trash.'];
                    } else {
                        $responseMessages[] = ['id' => $folder->id, 'message' => 'Folder with ID ' . $folder->id . ' could not be moved to trash.'];
                    }
                }
                // return $folders;
                return response()->json(['messages' => $responseMessages], 200);
            } else {
                // Single delete
                $folder = Folder::where('user_id', $request->user_id)->findOrFail($ids);
                if ($folder->delete()) {
                    return response()->json(['message' => 'Folder with ID ' . $folder->id . ' successfully moved to trash.'], 200);
                } else {
                    return response()->json(['message' => 'Folder with ID ' . $folder->id . ' could not be moved to trash.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the folder(s).'], 500);
        }
    }


    public function restore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:folders,id',
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $ids = $request->input('ids'); // Expecting an array of IDs

            if (is_array($ids)) {
                // Bulk restore
                $folders = Folder::onlyTrashed()->whereIn('id', $ids)->where('user_id', $request->user_id)->get();
                $responseMessages = [];

                foreach ($folders as $folder) {
                    if ($folder->restore()) {
                        $responseMessages[] = ['id' => $folder->id, 'message' => 'Folder with ID ' . $folder->id . ' successfully restored from trash.'];
                    } else {
                        $responseMessages[] = ['id' => $folder->id, 'message' => 'Folder with ID ' . $folder->id . ' could not be restored.'];
                    }
                }

                return response()->json(['messages' => $responseMessages], 200);
            } else {
                // Single restore
                $folder = Folder::onlyTrashed()->where('user_id', $request->user_id)->findOrFail($ids);
                if ($folder->restore()) {
                    return response()->json(['message' => 'Folder with ID ' . $folder->id . ' successfully restored from trash.'], 200);
                } else {
                    return response()->json(['message' => 'Folder with ID ' . $folder->id . ' could not be restored.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while restoring the folder(s).'], 500);
        }
    }





    public function permanentlyDelete(Request $request)
    {

        $request->validate([
            'ids' => 'required',
            'ids.*' => 'exists:folders,id',
            'user_id' => 'required|exists:users,id'

        ]);

        try {
            $ids = $request->input('ids');
            $userId = $request->user_id;

            if (is_array($ids)) {
                $folders = Folder::onlyTrashed()->whereIn('id', $ids)->where('user_id', $userId)->get();
                $responseMessages = [];
                $deletedCount = 0;
                // return $folders;

                foreach ($folders as $folder) {
                    // return $folder->id;
                    $this->deleteFolderAndContents($folder, $userId);
                    // return $folders;
                    // return $folder->id;
                    $deletedCount++;
                    $responseMessages[] = [
                        'id' => $folder->id,
                        'message' => 'Folder with ID ' . $folder->id . ' and its contents permanently deleted.'
                    ];
                }
                // return $folders;

                if ($deletedCount > 0) {
                    return response()->json(['messages' => $responseMessages], 200);
                }
            } else {
                // Single permanently delete
                $folder = Folder::onlyTrashed()->where('user_id', $userId)->findOrFail($ids);
                $this->deleteFolderAndContents($folder, $userId);
                return response()->json(['message' => 'Folder with ID ' . $folder->id . ' and its contents permanently deleted.'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while permanently deleting the folder(s).' . $e], 500);
        }
    }


    public function trash(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $query = Folder::onlyTrashed()->where('user_id', $request->user_id);

            // Search
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Pagination
            // $perPage = $request->get('per_page', 10);
            $folders = $query->get();

            if ($folders->isEmpty()) {
                return response()->json(['message' => 'No trashed folders found.'], 200);
            }

            return response()->json([
                'message' => 'Trashed folders retrieved successfully.',
                'data' => $folders
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving trashed folders.'], 500);
        }
    }

    protected function deleteFolderAndContents(Folder $folder, $id)
    {
        // Delete all files in the folder
        $files = File::where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            $this->deleteFile($file->path);
            $file->forceDelete(); // Permanently delete the file
            // Update user quota
            $user = User::findOrFail($id);
            // $user = $id;
            $user->used_quota = $user->used_quota - $file->size;
            $user->save();
        }

        // Recursively delete all subfolders
        $subfolders = Folder::where('parent_id', $folder->id)->get();
        foreach ($subfolders as $subfolder) {
            $this->deleteFolderAndContents($subfolder, $id);
        }

        // Delete the folder itself
        // Update parent folder size
        $this->updateFolderSize($folder);
        $folder->forceDelete();
    }


    protected function updateParentFolderSize($parentId)
    {
        if ($parentId) {
            $parentFolder = Folder::find($parentId);
            if ($parentFolder) {
                $this->updateFolderSize($parentFolder);
            }
        }
    }

    protected function updateFolderSize($folder)
    {
        if ($folder) {
            // Calculate the size of all child folders and files
            $folder->size = Folder::where('parent_id', $folder->id)->sum('size')
                + File::where('folder_id', $folder->id)->sum('size');
            $folder->save();
            // Update parent folder size
            $this->updateFolderSize(Folder::find($folder->parent_id));
        }
    }

    protected function updateUserQuota($userId)
    {
        // Calculate total size for the user
        $totalSize = Folder::where('user_id', $userId)->sum('size')
            + File::where('user_id', $userId)->sum('size');

        $user = User::findOrFail($userId);
        $user->used_quota = $totalSize;
        $user->save();
    }
}
