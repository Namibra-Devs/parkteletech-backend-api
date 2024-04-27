<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\Upload;

class FolderController extends Controller
{
    use Upload;
    public function store(Request $request)
    {
        // return $request;
        try {
            $validated = $request->validate([
                'folder_name'  => 'required|string|max:255',
                'vendor_name'  => 'required|string|max:255',
                'offer_date'   => 'required|string',
                'offer_link'   => 'required|string|max:255',
                'status'       => 'required|string|max:255',
                // 'file'         => 'required|file|mimes:pdf,doc,docx|max:2048', // File upload field
            ]);

            // Handle file upload
            $file = $request->file('file');
            $file_path = $this->UploadFile($file, 'folder_documents'); // Store in public storage

            $validated['file_path'] = $file_path; // Include file path in validated data

            // Create the folder record
            $folder = Folder::create($validated);

            return response()->json([
                'message' => 'Folder created successfully.',
                'data'    => $folder,
            ], Response::HTTP_CREATED); // HTTP 201 Created

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // HTTP 422 Unprocessable Entity

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the folder.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }

    public function index()
    {
        try {
            $folders = Folder::all();

            return response()->json($folders, Response::HTTP_OK); // HTTP 200 OK

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching folders.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }


    public function update(Request $request, $id)
{
    try {
        // Validate the incoming data
        $validated = $request->validate([
            'folder_name'  => 'sometimes|string|max:255',
            'vendor_name'  => 'sometimes|string|max:255',
            'offer_date'   => 'sometimes|date|nullable',
            'offer_link'   => 'sometimes|string|max:255',
            'status'       => 'sometimes|string|max:255',
            // 'file'         => 'sometimes|file|mimes:pdf,doc,docx|max:2048', // File upload
        ]);

        // Find the folder by ID
        $folder = Folder::findOrFail($id);

        // Check if there's a new file and delete the existing one before updating
        if ($request->hasFile('file')) {
            // Delete the existing file from storage
            if ($folder->file_path) {
                $this->deleteFile($folder->file_path);
            }

            // Store the new file and get its path
            $file = $request->file('file');
            $file_path = $this->UploadFile($file, 'folder_documents'); // Store in public storage
            $validated['file_path'] = $file_path; // Add new file path to validated data
        }

        // Update the folder with the validated data
        $folder->update($validated);

        return response()->json([
            'message' => 'Folder updated successfully.',
            'data'    => $folder,
        ], Response::HTTP_OK); // HTTP 200 OK

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed.',
            'errors'  => $e->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY); // HTTP 422 Unprocessable Entity

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Folder not found.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_NOT_FOUND); // HTTP 404 Not Found

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Error updating folder.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
    }
}

    public function show($id)
{
    try {
        $folder = Folder::findOrFail($id);

        return response()->json($folder, Response::HTTP_OK);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Folder not found.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_NOT_FOUND);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while retrieving the folder.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function destroy($id)
{
    try {
        $folder = Folder::findOrFail($id);

        // Delete the associated file from storage
        if ($folder->file_path) {
            $this->deleteFile($folder->file_path);
        }

        // Delete the folder from the database
        $folder->delete();

        return response()->json([
            'message' => 'Folder deleted successfully.',
        ], Response::HTTP_OK); // HTTP 200 OK

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Folder not found.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_NOT_FOUND); // HTTP 404 Not Found

    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while deleting the folder.',
            'error'   => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
    }
}


}
