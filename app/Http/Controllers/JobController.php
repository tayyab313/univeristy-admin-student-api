<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobCreateRequest;
use App\Models\Job;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function save(JobCreateRequest $request)
    {
        try {
            // The validated data will be available here
            $validatedData = $request->validated();

            // Create a new job
            $create_job = Job::create($validatedData);

            // Return success response using macro
            return Response::success($create_job, 'Job created successfully!');
        } catch (\Exception $e) {
            // Return error response using macro
            return Response::error('Failed to create job', 500, ['exception' => $e->getMessage()]);
        }
    }
    public function index()
    {
        try {
            // Fetch all jobs from the database
            $jobs = Job::paginate(50);
            // Return a success response with the jobs data
            return response()->success($jobs,'Job listing');
        } catch (\Exception $e) {
            // Handle any exceptions that occur while fetching jobs
            return response()->error('Failed to fetch jobs', 500, ['exception' => $e->getMessage()]);
        }
    }
}
