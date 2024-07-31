<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
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
            $files = $query->paginate($perPage)->appends($request->except('page'));

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
                // 'file' => 'required|file|max:10240', // Max file size 10MB
                'folder_id' => 'nullable|exists:folders,id',
                'user_id' => 'required|exists:users,id'
            ]);

            // $path = $request->file('file')->store('files');

            $file_details = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $this->UploadFile($file, 'external_files');
                    $name = $file->getClientOriginalName();
                    $file_details[] = ['path' => $path, 'name' => $name];
                }
            }

            foreach ($file_details as $file_detail) {
                $file = File::create([
                    'user_id' => $request->user_id,
                    'folder_id' => $request->folder_id,
                    'name' => $file_detail['name'],
                    'path'            => $file_detail['path'],
                ]);
            }


            return response()->json($file_details, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading the file.'], 500);
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

    public function delete(Request $request){
    try {
        // Validate the request to ensure 'ids' is an array of integers
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:files,id'
        ]);

        // return $request->ids;

        // Retrieve the files that need to be deleted
        $files = File::whereIn('id', $request->ids)->get();

        // Delete the files from the storage
        foreach ($files as $file) {
            $this->deleteFile($file->path);
            $file->delete(); // Delete the record from the database
        }

        // Delete the file records from the database
        File::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Files deleted successfully.'], 200);
    } catch (ValidationException $e) {
        return response()->json(['error' => $e->errors()], 422);
    } catch (Exception $e) {
        return response()->json(['error' => 'An error occurred while deleting files.'], 500);
    }
}
}
