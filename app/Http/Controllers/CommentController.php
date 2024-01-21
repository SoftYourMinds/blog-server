<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    // public function index(Request $request)
    // {
    //     $comments = Comment::where('article_id', $request->input('article_id'))
    //         ->with('replies') // Завантажте вкладені коментарі
    //         ->whereNull('parent_id') // Отримайте тільки кореневі коментарі
    //         ->get();    

    //     return response()->json($comments);
    // }
    public function index(Request $request)
    {
        $articleId = $request->input('article_id');

    // Get all comments for the specified article, including replies
        $comments = Comment::where('article_id', $articleId)
            ->with('replies') // Eager load replies recursively
            ->whereNull('parent_id') // Only retrieve root-level comments
            ->get();

        return response()->json($comments);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
        ]);

        $commentData = [
            'article_id' => $request->input('article_id'),
            'name' => $request->input('name'),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),  
        ];

        $comment = Comment::create($commentData);

        return response()->json($comment, 201);
    }



}
