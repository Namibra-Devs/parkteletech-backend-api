<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller {
    public function index() {
        try {
            $projects = Project::all();
            return response()->json( $projects, Response::HTTP_OK );
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error fetching projects.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function store( Request $request ) {
        try {
            $validated = $request->validate( [
                'project_name'     => 'required|string|max:255',
                'project_location' => 'required|string|max:255',
                'project_code'     => 'required|string|max:255',
                'offer_date'       => 'required|string|max:255',
                'end_date'         => 'required|string|max:255',
                'status'           => 'required|string|max:255',
            ] );

            $project = Project::create( $validated );

            return response()->json( [
                'message' => 'Project created successfully.',
                'data'    => $project,
            ], Response::HTTP_CREATED );
        } catch ( ValidationException $e ) {
            return response()->json( [
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY );
            // HTTP 422
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error creating project.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
            // HTTP 500
        }
    }

    public function show( $id ) {
        try {
            $project = Project::findOrFail( $id );
            return response()->json( $project, Response::HTTP_OK );
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Project not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
            // HTTP 404
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error fetching project.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function update( Request $request, $id ) {
        try {
            $validated = $request->validate( [
                'project_name'     => 'required|string|max:255',
                'project_location' => 'required|string|max:255',
                'project_code'     => 'required|string|max:255',
                'offer_date'       => 'required|string|max:255',
                'end_date'         => 'required|string|max:255',
                'status'           => 'required|string|max:255',
            ] );

            $project = Project::findOrFail( $id );
            $project->update( $validated );

            return response()->json( [
                'message' => 'Project updated successfully.',
                'data'    => $project,
            ], Response::HTTP_OK );
        } catch ( ValidationException $e ) {
            return response()->json( [
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY );
            // HTTP 422
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Project not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
            // HTTP 404
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error updating project.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function destroy( $id ) {
        try {
            $project = Project::findOrFail( $id );
            $project->delete();

            return response()->json( [
                'message' => 'Project deleted successfully.',
            ], Response::HTTP_OK );
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Project not found.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND );
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'Error deleting project.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }
}
