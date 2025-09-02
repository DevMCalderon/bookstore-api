<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::where('user_id', auth()->id())->paginate(10);
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create(array_merge(
            $request->validated(), 
            ['user_id' => auth()->id()]
        ));
        
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);
        
        if ($book->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, string $id)
    {
        $book = Book::findOrFail($id);
        
        if ($book->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $book->update($request->validated());
        
        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        
        if ($book->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $book->delete();
        
        return response()->json(null, 204);
    }
}