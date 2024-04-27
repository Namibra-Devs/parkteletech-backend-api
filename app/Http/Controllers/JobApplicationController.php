<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class JobApplicationController extends Controller {
    public function index() {
        try {
            $applications = JobApplication::all();
            return response()->json( [ 'data' => $applications ], Response::HTTP_OK );
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error fetching job applications.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function store( Request $request ) {
        try {
            // Validate the request
            $validated = $request->validate( [
                'job_id'         => 'required|exists:job_postings,id',
                'applicant_name' => 'required|string|max:255',
                'status'         => 'required|string|max:255',
            ] );

            // Create the job application
            $application = JobApplication::create( $validated );

            // Return a successful response
            return response()->json( [
                'message' => 'Job application created successfully.',
                'data'    => $application,
            ], Response::HTTP_CREATED );
            // HTTP 201 Created

        } catch ( ValidationException $e ) {
            // Custom response for validation errors
            return response()->json( [
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY );
            // HTTP 422 Unprocessable Entity

        } catch ( Exception $e ) {
            // General error handling
            return response()->json( [
                'message' => 'An error occurred while creating the job application.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
            // HTTP 500 Internal Server Error
        }
    }

    public function show( $id ) {
        try {
            $application = JobApplication::findOrFail( $id );
            return response()->json( [ 'data' => $application ], Response::HTTP_OK );
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Job application not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error fetching job application.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function update( Request $request, $id ) {
        try {
            // Validation with custom error handling
            $validated = $request->validate( [
                'job_id'          => 'required|exists:job_postings,id',
                'applicant_name'  => 'required|string|max:255',
                'status'          => 'required|string|max:255',
                'date_applied'    => 'sometimes|date',
            ] );

            $application = JobApplication::findOrFail( $id );
            $application->update( $validated );

            return response()->json( [
                'message' => 'Job application updated successfully.',
                'data'    => $application,
            ], Response::HTTP_OK );
            // HTTP 200 OK

        } catch ( ValidationException $e ) {
            // Corrected syntax for catching validation exceptions
            return response()->json( [
                'message' => 'Validation failed.',
                'errors'  => $e->errors(), // Specific validation errors
            ], Response::HTTP_UNPROCESSABLE_ENTITY );
            // HTTP 422 Unprocessable Entity

        } catch ( ModelNotFoundException $e ) {
            // Corrected syntax for catching model not found exceptions
            return response()->json( [
                'message' => 'Job application not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
            // HTTP 404 Not Found

        } catch ( Exception $e ) {
            // Corrected syntax for catching general exceptions
            return response()->json( [
                'message' => 'Error updating job application.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
            // HTTP 500 Internal Server Error
        }
    }

    public function destroy( $id ) {
        try {
            $application = JobApplication::findOrFail( $id );
            $application->delete();
            return response()->json( [
                'message' => 'Job application deleted successfully.',
            ], Response::HTTP_OK );
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Job application not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error deleting job application.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }
}
