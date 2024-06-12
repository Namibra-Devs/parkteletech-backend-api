<?php

namespace App\Http\Controllers;

use App\Models\StaffDetails;
use Illuminate\Http\Request;
use App\Models\Files;
use App\Traits\Upload;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class StaffDetailsController extends Controller
{
    use Upload;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $staffDetails = StaffDetails::all();

            return response()->json($staffDetails, 200); // HTTP 200 - OK
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching staff details.',
                'error'   => $e->getMessage(),
            ], 500); // HTTP 500 - Internal Server Error
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */





     public function store(Request $request)
     {
         try {
             $request->validate([
                 'fullname'           => 'required|string|max:255',
                 'email'              => 'required|string|email',
                 'dob'                => 'required|date',
                 'phone'              => 'required|string|max:20',
                 'id_type'            => 'required|string',
                 'id_no'              => 'required|string',
                 'employment_status'  => 'required|string',
                 'address'            => 'required|string',
                 'department'         => 'required|string',
             ]);

             $staffDetail = new StaffDetails([
                 'fullname'          => $request->input('fullname'),
                 'email'             => $request->input('email'),
                 'dob'               => $request->input('dob'),
                 'phone'             => $request->input('phone'),
                 'id_type'           => $request->input('id_type'),
                 'id_no'             => $request->input('id_no'),
                 'employment_status'=> $request->input('employment_status'),
                 'address'           => $request->input('address'),
                 'department'           => $request->input('department'),
             ]);

             $file_details = [];

             if ($request->hasFile('files')) {
                 foreach ($request->file('files') as $file) {
                     $path = $this->UploadFile($file, 'staff_documents');
                     $file_details[] = ['path' => $path];
                 }
             }

             $staffDetail->save();
             $names = ['cv', 'cert', 'hse_cert'];
             $count = 0;

             foreach ($file_details as $file_detail) {
                 Files::create([
                     'staff_detail_id' => $staffDetail->id,
                     'name'            => $names[$count],
                     'path'            => $file_detail['path'],
                 ]);
                 $count++;
             }

             return response()->json([
                 'message' => 'Staff detail and files saved successfully.',
                 'data'    => $staffDetail,
                 'files'   => $file_details,
             ], 201); // HTTP 201 - Created

         } catch (ValidationException $e) {
             return response()->json([
                 'message' => 'Validation error.',
                 'errors'  => $e->errors(),
             ], 422); // HTTP 422 - Unprocessable Entity

         } catch (Exception $e) {
             return response()->json([
                 'message' => 'An error occurred while processing your request.',
                 'error'   => $e->getMessage(),
             ], 500); // HTTP 500 - Internal Server Error
         }
     }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $staffDetail = StaffDetails::findOrFail($id); // Throws ModelNotFoundException if not found
            $files = Files::where('staff_detail_id', $id)->get(); // Retrieve associated files

            return response()->json([
                'data'  => $staffDetail,
                'files' => $files,
            ], 200); // HTTP 200 - OK

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Staff detail not found.',
                'error'   => $e->getMessage(),
            ], 404); // HTTP 404 - Not Found

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching staff details.',
                'error'   => $e->getMessage(),
            ], 500); // HTTP 500 - Internal Server Error
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'fullname'          => 'sometimes|string|max:255',
                'email'             => 'sometimes|string|email',
                'dob'               => 'sometimes|date',
                'phone'             => 'sometimes|string|max:20',
                'id_type'           => 'sometimes|string',
                'id_no'             => 'sometimes|string',
                'employment_status'=> 'sometimes|string',
                'address'           => 'sometimes|string',
                'department'         => 'required|string',
            ]);

            $staffDetail = StaffDetails::findOrFail($id);

            $files = Files::where('staff_detail_id', $id)->get();

            // Delete existing files from storage and the database
            foreach ($files as $file) {
                $this->deleteFile($file->path);
                $file->delete(); // Delete the record from the database
            }

            // Update the staff details
            $staffDetail->update($request->except('files'));

            $names = ['cv', 'cert', 'hse_cert'];
            $count = 0;


            // Add new files if provided
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $this->uploadFile($file, 'staff_documents');

                    Files::create([
                        'staff_detail_id' => $staffDetail->id,
                        'name'            => $names[$count],
                        'path'            => $path,
                    ]);

                    $count++;


            }
        }

            $updatedFiles = Files::where('staff_detail_id', $staffDetail->id)->get();

            return response()->json([
                'message' => 'Staff details updated successfully.',
                'data'    => $staffDetail,
                'files'   => $updatedFiles,
            ], 200); // HTTP 200 - OK

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Staff detail not found.',
                'error'   => $e->getMessage(),
            ], 404); // HTTP 404 - Not Found

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors'  => $e->errors(),
            ], 422); // HTTP 422 - Unprocessable Entity

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating staff details.',
                'error'   => $e->getMessage(),
            ], 500); // HTTP 500 - Internal Server Error
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Check if the staff detail exists
            $staffDetail = StaffDetails::findOrFail($id);

            // Get all files associated with the staff detail
            $files = Files::where('staff_detail_id', $staffDetail->id)->get();

            foreach ($files as $file) {
                $this->deleteFile($file->path); // Delete from storage
                $file->delete(); // Delete the record from the database
            }

            $staffDetail->delete(); // Delete the staff detail

            return response()->json([
                'message' => 'Staff detail and associated files deleted successfully.',
            ], 200); // HTTP 200 - OK

        } catch (ModelNotFoundException $e) {
            // If the staff detail is not found
            return response()->json([
                'message' => 'Staff detail not found.',
                'error'   => $e->getMessage(),
            ], 404); // HTTP 404 - Not Found

        } catch (Exception $e) {
            // For other unexpected errors
            return response()->json([
                'message' => 'An error occurred while deleting the staff detail.',
                'error'   => $e->getMessage(),
            ], 500); // HTTP 500 - Internal Server Error
        }
    }
}
