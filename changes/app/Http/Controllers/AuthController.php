<?php

namespace App\Http\Controllers;

use App\Http\Resources\student\GetStudentResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,staff,student'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== $request->role) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->role . '-token')->plainTextToken;

        return Response::success(['access_token' => $token,
             'token_type' => 'Bearer','user-detail'=>$user], 'Login successfully!');

    }
    public function updatePassword(Request $request)
    {
        $messages = [
            'password.required' => 'The new password is required.',
            'password.string' => 'The new password must be a string.',
            'password.min' => 'The new password must be at least 8 characters long.',
            'c_password.required' => 'The confirmation password is required.',
            'c_password.string' => 'The confirmation password must be a string.',
            'c_password.same' => 'The confirmation password must match the new password.',
        ];

        // Validate the input
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'c_password' => 'required|string|same:password',
        ], $messages);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Get the authenticated user
        $user = Auth::user();
        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->c_password = Hash::make($request->password);
        $user->save();
        return Response::success('Password updated successfully');
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    public function getProfileDetail($id)
    {
        try {
            // Fetch the student by ID
            $user = User::with('studenCard')->findOrFail($id);

            // Return a response with the student details
            return Response::success(['user' => new GetStudentResource($user)], 'User Detail');

        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'Student not found', 'error' => $e->getMessage()], 404);
        }

    }
    public function updateProfileDetail($id,Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'date_of_birth' => 'required|date',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($id), // Ignore the current user's email
                ],
                'student_id' => [
                    'required',
                    'string',
                    Rule::unique('users')->ignore($id), // Ignore the current user's email
                ],
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
                'email' => $request->email,
                'student_id' => $request->student_id,
            ]);

            // Return a response with the student details
            return Response::success(['user' => new GetStudentResource($student)], 'User Detail');

        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['message' => 'Student not found', 'error' => $e->getMessage()], 404);
        }

    }
}
