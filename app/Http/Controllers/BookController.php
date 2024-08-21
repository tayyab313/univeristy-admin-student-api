<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
}
