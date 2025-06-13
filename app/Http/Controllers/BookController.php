<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->header('Authorization');

        if ($auth) {
            $data = Book::where('Authorization', $auth)->get();
        } else {
            $data = Book::all();
        }

        return response()->json($data, status: 200);
    }



    public function store(Request $request)
    {
        Log::info('store book ', $request->all());
        $auth = $request->header('Authorization');
        Log::info('auth', ['auth' => $auth]);
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',
        ]);

        Log::info('aaaaaa', ['title' => $request->title]); // ✅ Fix logging

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images');
        }

        $book = new Book();
        $book->Authorization = $auth;
        $book->title = $request->title;
        $book->author = $request->author;
        $book->publisher = $request->publisher;
        $book->year = $request->year;
        $book->image = $path;
        $book->save();

        return response()->json(['status' => 'success', 'message' => 'data berhasil ditambahkan']);
    }


    public function show(Book $book)
    {
        $book = Book::find($book->id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        return response()->json($book, 200);
    }


    public function update(Request $request, $id)
    {
        $book = Book::find($id);


        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images');
            $book->image = $path;
        }

        $book->Authorization = $request->header('Authorization');
        $book->title = $request->title;
        $book->author = $request->author;
        $book->publisher = $request->publisher;
        $book->year = $request->year;
        $book->save();


        return response()->json(
            [
                'status' => 'success',
                'massage' => 'Book updated successfully'
            ],
            200
        );
    }


    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Book deleted successfully'
        ], 200);
    }
}
