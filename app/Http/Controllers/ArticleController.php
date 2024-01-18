<?php

namespace App\Http\Controllers;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    public function show(Article $article)
    {
        if ($article->exists) {
            return response()->json($article);
        } else {
            return response()->json(['error' => 'Article not found.'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Перевірка на тип та розмір зображення
            'tags' => 'array', // Перевірка, що теги є масивом
        ]);
    
        // return $request->input('tags');
        $articleData = $request->all();
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('article_images', 'public');
            $articleData['image_path'] = $imagePath;
        }
    
        $article = Article::create($articleData);
    
     // Додаємо теги до статті

            $tags = $request->input('tags');

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
            }
            
            $article->tags = $tags;
            $article->save();
            //=========

            return response()->json($article, 201);
    }
    
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'array', // Перевірка, що теги є масивом
        ]);
    
        $articleData = $request->all();
    
        if ($request->hasFile('image')) {
            // Видаляємо попереднє зображення, якщо воно є
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
    
            // Завантажуємо нове зображення
            $imagePath = $request->file('image')->store('article_images', 'public');
            $articleData['image_path'] = $imagePath;
        }
    
        // Отримання масиву ідентифікаторів тегів із запиту
        $tags = $request->input('tags');

            foreach ($tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
            }
            
            $article->tags = $tags;
            $article->save();
    
        $article->update($articleData);
    
        return response()->json($article);
    }



    public function destroy(Article $article)
    {
        // Видаляємо зображення, якщо воно існує
        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

    public function getArticlesByCategory($category_id)
    {
        try {
            // Валідація існування категорії
            Category::findOrFail($category_id);

            // Отримання статей за заданою категорією
            $articles = Article::where('category_id', $category_id)->get();

            return response()->json($articles);
        } catch (ModelNotFoundException $e) {
            // Повернення 404 помилки, якщо категорія не знайдена
            return response()->json(['error' => 'Not Found'], 404);
        }
    }

    public function findByNameAndTags(Request $request)
    {
        $name = $request->input('name');
        $tags = $request->input('tags');
    
        $query = Article::query();
    
        // Пошук за ім'ям
        if ($name) {
            $query->where('title', 'like', '%' . $name . '%');
        }
    
        // Пошук за тегами
        if ($tags) {
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }
    
        $articles = $query->get();
    
        return response()->json($articles);
    }
    
    


}
