<?php

namespace App\Http\Controllers;

use App\Models\Category;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
        ]);

        return response()->json($category, 201);
    }

    public function storeMany(Request $request)
    {
        $requestData = $request->all();

        // Перевірка, чи дані - це масив
        if (is_array($requestData)) {
            // Якщо масив, обробляємо кожен об'єкт окремо
            $categories = collect($requestData)->map(function ($item) {
                return $this->createCategory($item);
            });
            return response()->json($categories, 201);
        }
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->input('name'),
   ]);

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    private function createCategory($data)
    {
        // Логіка створення категорії
        // ...
        return Category::create([
            'name' => $data['name'],
        ]);
    }
}
