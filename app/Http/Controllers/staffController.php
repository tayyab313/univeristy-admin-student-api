<?php

namespace App\Http\Controllers;

use App\Http\Requests\sudentCardCreateRequest;
use App\Http\Resources\student\getCardDetailResource;
use App\Http\Resources\student\GetStudentResource;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\StudentCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class staffController extends Controller
{
    //
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'status' => 'required|boolean',
                'date_of_birth' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $hashedPassword = Hash::make($request->password);
            $staff = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => $hashedPassword,
                'c_password' => $hashedPassword,
                'role' => 'staff',
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
            ]);
            // Return a response
            return Response::success(['staff' => $staff], 'Staf created successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while creating the student', 'error' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        try {
            // Fetch the student by ID
            $staff = User::findOrFail($id);

            // Return a response with the staff details
            return Response::success(['staff' => new GetStudentResource($staff)], 'Staff Detail');

        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'Staff not found', 'error' => $e->getMessage()], 404);
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

            // Find the staff by ID
            $staff = User::with('studenCard')->findOrFail($id);

            // Update the staff's details
            $staff->update([
                'name' => $request->name,
                'status' => $request->status,
                'date_of_birth' => $request->date_of_birth,
            ]);

            // Return a response
            return Response::success(['staff' => new GetStudentResource($staff)], 'Staff updated successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while updating the Staff', 'error' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        try {
            // Fetch all jobs from the database
            $staff = User::where('role','staff')->get();
            // Return a success response with the courses data
            return response()->success(GetStudentResource::collection($staff),'Student listing');
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
            return Response::success([], 'Staff deleted successfully');
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'An error occurred while deleting the staff', 'error' => $e->getMessage()], 500);
        }
    }
    public function assignCourses(Request $request, User $staff)
    {
        // Validate the request data
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        // Ensure the user is a staff member
        if ($staff->role !== 'staff') {
            return response()->json(['message' => 'The specified user is not a staff member'], 400);
        }

        // Assign the courses to the staff member
        $staff->courses()->sync($request->course_ids);
        return Response::success([], 'Courses assigned to staff member successfully');
    }
    public function getCourses(User $staff)
    {
        // Ensure the user is a staff member
        if ($staff->role !== 'staff') {
            return response()->json(['message' => 'The specified user is not a staff member'], 400);
        }

        // Get the courses assigned to the staff member
        $courses = $staff->courses()->get();
        return response()->success(['courses'=> $courses],'Assigned Courses list');
    }
    public function getAssignCourses()
    {
        $staff = Auth::user();
        // Ensure the user is a staff member
        if ($staff->role !== 'staff') {
            return response()->json(['message' => 'The specified user is not a staff member'], 400);
        }
        // Get the courses assigned to the staff member
        $courses = $staff->courses()->get();
        return response()->success(['courses'=> $courses],'Assigned Courses list');
    }
    public function getCourseStudent($course)
    {
        $course = Course::with(['students' => function ($q){
            $q->where('is_approved',1)->get();
        }])->find($course);

        return response()->success(['Student'=> $course->students ?? []],'Student List');
    }
    public function markAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'students' => 'required|array',
            'students.*.id' => 'required|exists:users,id',
            'students.*.attendance' => 'required|in:present,absent,leave',
        ]);

        // Custom validation to check if attendance is already marked for the day
        $validator->after(function ($validator) use ($request) {
            $courseId = $request->input('course_id');
            $today = now()->toDateString();
            foreach ($request->input('students') as $student) {
                $studentId = $student['id'];

                $attendanceExists = Attendance::where('student_id', $studentId)
                    ->where('course_id', $courseId)
                    ->where('date', $today)
                    ->exists();

                if ($attendanceExists) {
                    $validator->errors()->add('students.' . $studentId, "Attendance has already been marked for student ID {$studentId} for today.");
                }
            }
        });

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $courseId = $request->input('course_id');
        $students = $request->input('students');
        $today = now()->toDateString();

        foreach ($students as $student) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student['id'],
                    'course_id' => $courseId,
                    'date' => $today,
                ],
                [
                    'attendance' => $student['attendance'],
                ]
            );
        }

        return response()->json(['message' => 'Attendance marked successfully.']);
    }

}
