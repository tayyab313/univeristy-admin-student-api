<?php

namespace App\Http\Controllers;

use App\Http\Resources\chat\messagesResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        return Response::success($message, 'Message Sent Successfully!');
    }

    public function getMessages(User $user)
    {
        $messages = Message::with('receiver','sender')->where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return Response::success(messagesResource::collection($messages), 'Messages!');

        return response()->json($messages);
    }
    public function getAdmin()
    {
        $admin = User::select('id','name','role')->where(['role' => 'Admin'])->first();
        return Response::success($admin, 'Admin Data!');
    }
    public function listStudents()
    {
        $students = User::where(['role' => 'student','status'=>1])->get();
        return Response::success($students, 'Student List!');
    }
}
