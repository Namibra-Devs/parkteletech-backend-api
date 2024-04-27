<?php

namespace App\Http\Controllers;

use App\Models\PhotoReport;
use App\Traits\Upload;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PhotoReportController extends Controller
{
    use Upload;

    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'completion_date' => 'required|date',
                'description' => 'required|string',
                'status' => 'required|string',
            ]);

            $file_paths = [];

            if ($request->hasFile('files')) {
                // Check if 'files' is an array or a single file
                if (is_array($request->file('files'))) {
                    // Multiple file upload handling
                    foreach ($request->file('files') as $file) {
                        $file_path = $this->UploadFile($file, 'photo_reports'); // Store in public storage
                        $file_paths[] = $file_path; // Add to the array of file paths
                    }
                } else {
                    // Single file upload handling
                    $file = $request->file('files');
                    $file_path = $this->UploadFile($file, 'photo_reports');
                    $file_paths[] = $file_path;
                }
            }

            // Store the file paths as a JSON-encoded array in the database
            $validated['file_paths'] = json_encode($file_paths);

            // Create the photo report with the validated data
            $photo_report = PhotoReport::create($validated);

            return response()->json([
                'message' => 'Photo report created successfully.',
                'data' => $photo_report,
            ], Response::HTTP_CREATED); // HTTP 201 Created

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // HTTP 422 Unprocessable Entity

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating photo report.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'completion_date' => 'required|date',
                'description' => 'sometimes|string',
                'status' => 'required|string',
                // 'files'             => 'sometimes', // Can be a single file or an array of files
                // 'files.*'           => 'file|mimes:jpg,jpeg,png|max:2048', // Validate each file
            ]);

            $photo_report = PhotoReport::findOrFail($id);

            $file_paths = [];

            if ($request->hasFile('files')) {
                if (is_array($request->file('files'))) {
                    // Delete existing files before updating
                    if ($photo_report->file_paths) {
                        $existing_paths = json_decode($photo_report->file_paths, true);
                        foreach ($existing_paths as $path) {
                            $this->deleteFile($path);
                        }
                    }

                    // Multiple file upload handling
                    foreach ($request->file('files') as $file) {
                        $file_path = $this->UploadFile($file, 'photo_reports');
                        $file_paths[] = $file_path; // Add to the array of file paths
                    }
                } else {
                    // Single file upload handling
                    $file = $request->file('files');
                    if ($photo_report->file_paths) {
                        $this->deleteFile($photo_report->file_paths); // Delete the existing file
                    }
                    $file_path = $this->UploadFile($file, 'photo_reports');
                    $file_paths[] = $file_path; // Add the new file path
                }

                $validated['file_paths'] = json_encode($file_paths); // Store file paths as a JSON array
            }

            // Update the photo report with the validated data
            $photo_report->update($validated);

            return response()->json([
                'message' => 'Photo report updated successfully.',
                'data' => $photo_report,
            ], Response::HTTP_OK); // HTTP 200 OK

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // HTTP 422 Unprocessable Entity

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Photo report not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND); // HTTP 404 Not Found

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error updating photo report.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }

    public function index()
    {
        try {
            $photo_reports = PhotoReport::all();

            return response()->json([
                'message' => 'Photo reports retrieved successfully.',
                'data' => $photo_reports,
            ], Response::HTTP_OK); // HTTP 200 OK

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching photo reports.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }

    public function show($id)
    {
        try {
            $photo_report = PhotoReport::findOrFail($id);

            return response()->json([
                'message' => 'Photo report retrieved successfully.',
                'data' => $photo_report,
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Photo report not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND); // HTTP 404 Not Found

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the photo report.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }

    public function destroy($id)
    {
        try {
            $photo_report = PhotoReport::findOrFail($id);

            // Delete the associated files from storage
            if ($photo_report->file_paths) {
                $file_paths = json_decode($photo_report->file_paths, true);
                foreach ($file_paths as $path) {
                    $this->deleteFile($path);
                }
            }

            $photo_report->delete();

            return response()->json([
                'message' => 'Photo report deleted successfully.',
            ], Response::HTTP_OK); // HTTP 200 OK

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Photo report not found.',
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND); // HTTP 404 Not Found

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting photo report.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // HTTP 500 Internal Server Error
        }
    }
}
