<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BookController extends Controller
{
    // Get all books
    public function index()
    {
        return Response::success(['book' => Book::all()], 'Book List');
    }

    // Get a single book by ID
    public function show($id)
    {
        $book = Book::find($id);
        if ($book) {
        return Response::success(['book' => $book], 'Book Detail');
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    // Create a new book
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'book_name' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'published_year' => 'required|integer|digits:4',
        ]);

        $book = Book::create($validatedData);
        return Response::success(['book' => $book], 'Book added successfully');

    }

    // Update an existing book
    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $validatedData = $request->validate([
            'book_name' => 'sometimes|required|string|max:255',
            'author_name' => 'sometimes|required|string|max:255',
            'published_year' => 'sometimes|required|integer|digits:4',
        ]);

        $book->update($validatedData);
        return Response::success(['book' => $book], 'Book updated Successfully');
    }

    // Delete a book
    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
    public function requestBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'from_date' => 'required|date_format:d-m-Y',
            'to_date' => 'required|date_format:d-m-Y|after:from_date',
        ]);

        $bookRequest = new BookRequest();
        $bookRequest->user_id = auth()->id();
        $bookRequest->book_id = $request->book_id;
        $bookRequest->from_date = Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
        $bookRequest->to_date = Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
        $bookRequest->status = 'pending';
        $bookRequest->save();

        return Response::success(['message'=>'request submitted successfully.']);
    }
    public function viewRequests()
    {
        $requests = BookRequest::where('status', 'pending')->with('book', 'user')->get();

        return Response::success(['book_request'=>$requests],'Pending Request.');
    }
    public function updateRequestStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $bookRequest = BookRequest::findOrFail($id);

        if ($request->status == 'approved') {
            // Check if the book is available during the requested date range
            $existingRequest = BookRequest::where('book_id', $bookRequest->book_id)
                                          ->where('status', 'approved')
                                          ->where(function ($query) use ($bookRequest) {
                                              $query->whereBetween('from_date', [$bookRequest->from_date, $bookRequest->to_date])
                                                    ->orWhereBetween('to_date', [$bookRequest->from_date, $bookRequest->to_date])
                                                    ->orWhere(function ($subQuery) use ($bookRequest) {
                                                        $subQuery->where('from_date', '<=', $bookRequest->from_date)
                                                                 ->where('to_date', '>=', $bookRequest->to_date);
                                                    });
                                          })->exists();

            if ($existingRequest) {
                return Response::error('Book is already issued to another student during the requested date range.', 400);
            }
        }

        // If no conflicts, update the request status
        $bookRequest->status = $request->status;
        $bookRequest->save();
        return Response::success('Book request status updated successfully.');
    }
    public function myRequests()
    {
        // Retrieve the authenticated student's book requests
        $bookRequests = BookRequest::where('user_id', auth()->id())
                                ->with('book') // Eager load the book details
                                ->get();

        // Return the list of requests
        return Response::success(['request_list'=>$bookRequests],'Your book issue request list.');
    }

}
