<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::all();
        return response()->json($authors);
    }

    public function show(Author $author)
    {
        return response()->json($author);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:authors,email',
            'password' => 'required|string|min:6',
        ]);

        $author = Author::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return response()->json($author, 201);
    }

        public function update(Request $request, Author $author)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:authors,email,' . $author->id,
                'password' => 'nullable|string|min:6',
            ]);

            $author->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->has('password') ? bcrypt($request->input('password')) : $author->password,
            ]);

            return response()->json($author);
        }

    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json(['message' => 'Author deleted successfully']);
    }

    public function login(Request $request)
    {
        // $credentials = $request->only('email', 'password');

        $author = Author::where('email',$request->input('email'))->first();    

        
        if(password_verify($request->input('password'), $author->password)) {
            return response()->json($author, 200);
        }
        
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
