<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\createRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    public function store(createRequest $request)
    {
        try {
            // The validated data will be available here
            $validatedData = $request->validated();

            // Create a new course
            $create_course = Course::create($validatedData);

            // Return success response using macro
            return Response::success($create_course, 'Course Added successfully!');
        } catch (\Exception $e) {
            // Return error response using macro
            return Response::error('Failed to create course', 500, ['exception' => $e->getMessage()]);
        }
    }

    public function index()
    {
        try {
            // Fetch all jobs from the database
            $courses = Course::paginate(50);
            // Return a success response with the courses data
            return response()->success($courses,'Courses listing');
        } catch (\Exception $e) {
            // Handle any exceptions that occur while fetching courses
            return response()->error('Failed to fetch courses', 500, ['exception' => $e->getMessage()]);
        }
    }
}
