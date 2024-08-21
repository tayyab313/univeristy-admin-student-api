<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;



class AttendanceController extends Controller
{
    public function markAttendance(Request $request)
    {

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_id' => 'required|exists:users,id',
        ]);

        // Ensure the user is a student
        $student = User::find($request->student_id);
        if ($student->role != 'student') {

            return Response::error('Only students can mark attendance', 403);
        }

        $course = Course::find($request->course_id);
        if (!$course->students->contains($student->id)) {
            return Response::error('Student not enrolled in this course', 403);
        }

        // Check if attendance already marked for today
        $existingAttendance = Attendance::where('course_id', $request->course_id)
                                        ->where('student_id', $request->student_id)
                                        ->whereDate('created_at', Carbon::today())
                                        ->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'Attendance already marked for today'], 409);
        }

        // Mark attendance
        $attendance = Attendance::create([
            'course_id' => $request->course_id,
            'student_id' => $request->student_id,
        ]);
        return Response::success($attendance, 'Attendance marked successfully');
    }

}
