<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class FolderControllerNew extends Controller
{

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
            $perPage = $request->get('per_page', 10); // Default to 10 items per page
            $folders = $query->paginate($perPage)->appends($request->except('page'));

            return response()->json($folders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving folders.'. $e], 500);
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
                // 'parent_id' => 'nullable|exists:folders,id',
            ]);

            $folder = Folder::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json($folder, 201);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the folder. '. $e->getMessage()], 500);
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
        try {
            // Validate the request to ensure 'ids' is an array of integers
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:folders,id'
            ]);

            // Delete the folders with the given IDs
            Folder::whereIn('id', $request->ids)->delete();

            return response()->json(['message' => 'Folders deleted successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting folders.'], 500);
        }
    }

}
