<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\FeesUploadRequest;
use App\Http\Requests\File\GradesUploadRequest;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadGrades(GradesUploadRequest $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validated();

            // Handle file upload
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/grades', $fileName, 'local');

            // Store file details in database
            $upload = new FileUpload();
            $upload->file_name = $fileName;
            $upload->file_path = $filePath;
            $upload->file_type = 'grades';
            $upload->upload_by_id = auth()->user()->id;
            $upload->student_id = $request['student_id'];
            $upload->save();

            // Return success response
            return response()->json(['message' => 'File uploaded successfully', 'file' => $upload]);

        } catch (\Exception $e) {
            // Return error response
            return Response::error('Failed to upload grades file', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function uploadFees(FeesUploadRequest $request)
    {
        try {
            // Validate incoming request
            $validatedData = $request->validated();

            // Handle file upload
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/fees', $fileName, 'local');

            // Store file details in database
            $upload = new FileUpload();
            $upload->file_name = $fileName;
            $upload->file_path = $filePath;
            $upload->file_type = 'fees';
            $upload->upload_by_id = auth()->user()->id;
            $upload->save();

            // Return success response
            return response()->json(['message' => 'File uploaded successfully', 'file' => $upload]);

        } catch (\Exception $e) {
            // Return error response
            return Response::error('Failed to upload fees file', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function fetchFees($file_type)
    {
        try {
            // Find the file record in the database
            $fileRecord = FileUpload::where('file_type', $file_type)->first();

            if (!$fileRecord) {
                return response()->json(['message' => 'File not found.'], 404);
            }

            // Get the file path from the database record
            $filePath = $fileRecord->file_path;

            // Check if the file exists
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json(['message' => 'File not found on server.'], 404);
            }

            // Return the file as a response
            return response()->file(storage_path('app/' . $filePath));

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch file', 'error' => $e->getMessage()], 500);
        }
    }
    public function fetchGradebyStudent($user)
    {
        try {
            // Find the file record in the database
            $fileRecord = FileUpload::where('student_id', $user)->orderBy('updated_at','desc')->first();

            if (!$fileRecord) {
                return response()->json(['message' => 'File not found.'], 404);
            }

            // Get the file path from the database record
            $filePath = $fileRecord->file_path;

            // Check if the file exists
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json(['message' => 'File not found on server.'], 404);
            }

            // Return the file as a response
            return response()->file(storage_path('app/' . $filePath));

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch file', 'error' => $e->getMessage()], 500);
        }
    }

}
