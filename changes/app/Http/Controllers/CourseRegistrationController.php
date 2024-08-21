<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CourseRegistrationController extends Controller
{
    public function store(Request $request, Course $course_id)
    {

        try {
            // The validated data will be available here
            $registration = CourseRegistration::create([
                'course_id' => $course_id->id,
                'student_id' => auth()->id(),
            ]);

            // Return success response using macro
            return Response::success([], 'You Registered in course successfully just wait for Admin approved!');
        } catch (\Exception $e) {
            // Return error response using macro
            return Response::error('Failed to registered in course', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function approve(Request $request, Course $course)
    {
        try {
            $registration = CourseRegistration::where('course_id', $course->id)
                ->where('student_id', $request->student_id)
                ->first();

            if ($registration) {

                $registration->is_approved = true;
                $registration->save();
            }

            return Response::success([], 'Course update successfully!');
        } catch (\Exception $e) {
            // Return error response using macro
            return Response::error('Failed to approved in course', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function pendingApprovals()
    {
        // Check if the user is an admin
        // if (auth()->user()->role !== 'admin') {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Get the list of pending course registrations

        try {
            // The validated data will be available here
            $pendingApprovals = CourseRegistration::where('is_approved', false)
                ->with(['course', 'student'])
                ->get();
            return Response::success($pendingApprovals, 'pending approval course list');
        } catch (\Exception $e) {
            // Return error response using macro
            return Response::error('Failed to registered in course', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function approvedCourses($student_id)
    {
        // Get the list of approved course registrations for the authenticated student
        $approvedCourses = CourseRegistration::where('student_id', $student_id)
                            ->where('is_approved', true)
                            ->with('course')
                            ->get();

        return response()->json($approvedCourses);
    }
}
