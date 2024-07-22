<?php

namespace App\Http\Controllers;

use App\Http\Requests\sudentCardCreateRequest;
use App\Http\Resources\student\getCardDetailResource;
use App\Http\Resources\student\GetStudentResource;
use App\Models\StudentCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

class StudentController extends Controller
{
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'student_id' => 'required|string|unique:users',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'status' => 'required|boolean',
                'date_of_birth' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $hashedPassword = Hash::make($request->password);
            $student = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => $hashedPassword,
                'c_password' => $hashedPassword,
                'role' => 'student',
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
                'student_id' => $request->student_id,
            ]);
            // Return a response
            return Response::success(['student' => $student], 'Student created successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while creating the student', 'error' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        try {
            // Fetch the student by ID
            $student = User::with('studenCard')->findOrFail($id);

            // Return a response with the student details
            return Response::success(['student' => new GetStudentResource($student)], 'Student Detail');

        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'Student not found', 'error' => $e->getMessage()], 404);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'date_of_birth' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Find the student by ID
            $student = User::with('studenCard')->findOrFail($id);

            // Update the student's details
            $student->update([
                'name' => $request->name,
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
            ]);

            // Return a response
            return Response::success(['student' => new GetStudentResource($student)], 'Student updated successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while updating the student', 'error' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        try {
            // Fetch all jobs from the database
            $students = User::with('studenCard')->where('role','student')->get();
            // Return a success response with the courses data
            return response()->success(GetStudentResource::collection($students),'Student listing');
        } catch (\Exception $e) {
            // Handle any exceptions that occur while fetching courses
            return response()->error('Failed to fetch courses', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            // Find the student by ID
            $student = User::findOrFail($id);

            // Delete the student
            $student->delete();

            // Return a response
            return Response::success([], 'Student deleted successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while deleting the student', 'error' => $e->getMessage()], 500);
        }
    }
    public function cardCreate(sudentCardCreateRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Find or create the student card based on student_id
            $studentCard = StudentCard::updateOrCreate(
                ['student_id' => $validatedData['student_id']],
                $validatedData
            );

            // Return success response
            return Response::success($studentCard, 'Student card created or updated successfully!');
        } catch (\Exception $e) {
            // Return error response
            return Response::error('Failed to create or update student card', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function cardDetail($id)
    {
        try {
            $getCard = StudentCard::where('student_id', $id)->first();

            if (!$getCard) {
                return Response::error('Student card not found', 404);
            }

            return new GetCardDetailResource($getCard);
        } catch (\Exception $e) {
            // Return error response
            return Response::error('Failed to retrieve student card details', 500, ['exception' => $e->getMessage()]);
        }
    }
}
