<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobPostings = JobPosting::all();
        return response()->json($jobPostings, 200); // 200 OK
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'department'    => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'description'   => 'required|string',
            'salary'        => 'required|numeric',
        ]);

        $jobPosting = JobPosting::create($request->all());

        return response()->json($jobPosting, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jobPosting = JobPosting::find($id);

        if (!$jobPosting) {
            return response()->json(['message' => 'Job Posting not found'], 404); // 404 Not Found
        }

        return response()->json($jobPosting, 200); // 200 OK
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
        $request->validate([
            'title'         => 'string|max:255',
            'department'    => 'string|max:255',
            'qualification' => 'string|max:255',
            'description'   => 'string',
            'salary'        => 'numeric',
        ]);

        $jobPosting = JobPosting::find($id);

        if (!$jobPosting) {
            return response()->json(['message' => 'Job Posting not found'], 404); // 404 Not Found
        }

        $jobPosting->update($request->all());

        return response()->json($jobPosting, 200); // 200 OK
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jobPosting = JobPosting::find($id);

        if (!$jobPosting) {
            return response()->json(['message' => 'Job Posting not found'], 404); // 404 Not Found
        }

        $jobPosting->delete();

        return response()->json(['message' => 'Job Posting deleted'], 200); // 200 OK
    }

    /**
     * Search for job postings based on title.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $jobPostings = JobPosting::where('title', 'LIKE', '%' . $query . '%')->get();

        return response()->json($jobPostings, 200); // 200 OK
    }
}
