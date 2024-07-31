<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FilesInternal;
use App\Traits\Upload;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{

    use Upload;

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploaded_files', 'public');

        $fileRecord = FilesInternal::create([
            'path' => $path,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully.',
            'data'    => $fileRecord,
        ], 201);
    }

    public function show($id)
    {
        $fileRecord = FilesInternal::findOrFail($id);

        return response()->json([
            'data' => $fileRecord,
        ]);
    }

    public function index()
    {
        $fileRecords = FilesInternal::all();

        return response()->json([
            'data' => $fileRecords,
        ]);
    }

    public function update(Request $request, $id)
    {
        $fileRecord = FilesInternal::findOrFail($id);

        $request->validate([
            'description' => 'sometimes|string|max:255',
        ]);

        $fileRecord->update($request->all());

        return response()->json([
            'message' => 'File record updated successfully.',
            'data'    => $fileRecord,
        ]);
    }
    public function destroy($id)
    {
        $fileRecord = FilesInternal::findOrFail($id);

        $this->deleteFile($fileRecord->file_path);

        $fileRecord->delete();

        return response()->json([
            'message' => 'File record deleted successfully.',
        ]);
    }
}
