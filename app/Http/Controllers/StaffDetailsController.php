<?php

namespace App\Http\Controllers;

use App\Models\StaffDetails;
use Illuminate\Http\Request;
use App\Models\Files;
use App\Traits\Upload;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
        $staffDetails = StaffDetails::all();
        return response()->json($staffDetails, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'fullname'           => 'required|string|max:255',
    //         // 'email'              => 'required|string|email|unique:staff_details,email',
    //         'email'              => 'required|string|email',
    //         'dob'                => 'required|date',
    //         'phone'              => 'required|string|max:20',
    //         'id_type'            => 'required|string',
    //         // 'id_no'              => 'required|string|unique:staff_details,id_no',
    //         'id_no'              => 'required|string',
    //         'employment_status'  => 'required|string',
    //         'address'            => 'required|string',
    //         'documents.*'        => 'file|mimes:pdf,doc,docx|max:2048', // Multiple document uploads
    //     ]);
    //     // dd($request->all());
    //     $staffDetail = new StaffDetails($request->except('documents'));

    //     if ($request->hasfile('documents')) {
    //         // return response()->json(['message' => 'Staff detail not found'], 201); // 201 Created
    //         $documents = [];
    //         foreach ($request->file('documents') as $file) {
    //             $path = $file->store('staff_documents', 'public');
    //             $documents[] = $path;
    //         }
    //         $staffDetail->documents = json_encode($documents);
    //     }

    //     $staffDetail->save();

    //     return response()->json($staffDetail, 201); // 201 Created
    // }

//     public function store(Request $request)
// {
//     // Validate input
//     $request->validate([
//         'fullname'           => 'required|string|max:255',
//         'email'              => 'required|string|email',
//         'dob'                => 'required|date',
//         'phone'              => 'required|string|max:20',
//         'id_type'            => 'required|string',
//         'id_no'              => 'required|string',
//         'employment_status'  => 'required|string',
//         'address'            => 'required|string',
//         'documents.*'        => 'file|mimes:pdf,doc,docx|max:2048',
//     ]);

//     // Debug output
//     // dd($request->all(), $request->file('documents')); // For debugging the input data and files

//     // Create the StaffDetails instance
//     $staffDetail = new StaffDetails($request->except('documents'));

//     // Handle file uploads
//     if ($request->hasFile('documents')) {
//         $documents = [];
//         foreach ($request->file('documents') as $file) {
//             $path = $file->store('staff_documents', 'public'); // Store in 'public' disk
//             $documents[] = $path;
//         }
//         $staffDetail->documents = json_encode($documents);
//     } else {
//         // No files found
//         return response()->json(['message' => 'No documents uploaded'], 400); // HTTP 400 - Bad Request
//     }

//     // Save the staff details
//     $staffDetail->save();

//     // Return a success response
//     return response()->json($staffDetail, 201); // HTTP 201 - Created
// }

public function store(Request $request)
{

    $file_details = [];

    //check if request has files
    if ($request->hasFile('files')) {


        foreach ($request->file('files') as $key => $file) {
            //Upload to Storage
            $path = $this->UploadFile($file, 'Products');

            //reformat the file details
            array_push($file_details, [
                'path' => $path,
            ]);
        }

        //add each file details to database
        foreach ($file_details as $key => $value) {
            Files::create($value);
        }
        return response()->json([
            'path' => "path"], 200);
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
        $staffDetail = StaffDetails::find($id);

        if (!$staffDetail) {
            return response()->json(['message' => 'Staff detail not found'], 404);
        }

        return response()->json($staffDetail);
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
        $staffDetail = StaffDetails::find($id);

        if (!$staffDetail) {
            return response()->json(['message' => 'Staff detail not found'], 404);
        }

        $staffDetail->update($request->all());

        return response()->json($staffDetail, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staffDetail = StaffDetails::find($id);

        if (!$staffDetail) {
            return response()->json(['message' => 'Staff detail not found'], 404);
        }

        $staffDetail->delete();

        return response()->json(['message' => 'Staff detail deleted'], 200);
    }
}
