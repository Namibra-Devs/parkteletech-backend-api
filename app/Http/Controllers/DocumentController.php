<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\Upload;
use Exception;

class DocumentController extends Controller {

    use Upload;

    public function store( Request $request ) {
        try {
            $request->validate( [
                // 'file'       => 'required|file|mimes:pdf,doc,docx|max:2048', // Validate file type and size
                'filename'   => 'required|string|max:255', // Validate filename
                'file_type'  => 'required|string|max:255', // Validate file type
            ] );

            $file = $request->file( 'file' );
            $file_path = $this->UploadFile( $file, 'contract_documents' );
            // Store the file

            $filename = $request->input( 'filename' );
            // Extract filename
            $file_type = $request->input( 'file_type' );
            // Extract file type

            $document = Document::create( [
                'filename'  => $filename,
                'file_type' => $file_type,
                'file_path' => $file_path,
            ] );

            return response()->json( [
                'message' => 'Document uploaded successfully.',
                'data'    => $document,
            ], 201 );
            // HTTP 201 - Created
        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'An error occurred while uploading the document.',
                'error'   => $e->getMessage(),
            ], 500 );
            // HTTP 500 - Internal Server Error
        }
    }

    public function update( Request $request, $id ) {
        try {
            $document = Document::findOrFail( $id );

            $request->validate( [
                'file_type' => 'sometimes|string|max:255',
                'filename'  => 'sometimes|string|max:255',
            ] );

            if ( $request->hasFile( 'file' ) ) {
                $this->deleteFile( $document->file_path );

                $file = $request->file( 'file' );
                $file_path = $this->UploadFile( $file, 'contract_documents' );

                $document->update( [
                    'file_path' => $file_path,
                ] );
            }

            $document->update( $request->except( 'file' ) );

            return response()->json( [
                'message' => 'Document updated successfully.',
                'data'    => $document,
            ], 200 );

        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Document not found.',
                'error'   => $e->getMessage(),
            ], 404 );

        } catch ( Exception $e ) {
            return response()->json( [
                'message' => 'An error occurred while updating the document.',
                'error'   => $e->getMessage(),
            ], 500 );
        }
    }

    public function show( $id ) {
        try {
            $document = Document::findOrFail( $id );

            return response()->json( [
                'data' => $document,
            ], 200 );
            // HTTP 200 - OK
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Document not found.',
                'error'   => $e->getMessage(),
            ], 404 );
            // HTTP 404 - Not Found
        }
    }

    public function index()
    {
        try {
            $documents = Document::all();

            return response()->json([
                'data' => $documents,
            ], 200); // HTTP 200 - OK

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the documents.',
                'error'   => $e->getMessage(),
            ], 500); // HTTP 500 - Internal Server Error
        }
    }
    public function destroy( $id ) {
        try {
            $document = Document::findOrFail( $id );

            // Delete the file from storage
            $this->deleteFile( $document->file_path );

            // Delete the document record from the database
            $document->delete();

            return response()->json( [
                'message' => 'Document deleted successfully.',
            ], 200 );
            // HTTP 200 - OK
        } catch ( ModelNotFoundException $e ) {
            return response()->json( [
                'message' => 'Document not found.',
                'error'   => $e->getMessage(),
            ], 404 );
            // HTTP 404 - Not Found
        }
    }
}
