<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/login', [AuthorController::class, 'login']);
Route::post('/registration', [AuthorController::class, 'register']);
Route::get('/authors', [AuthorController::class, 'index']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/category', [CategoryController::class, 'store']);
Route::post('/categories', [CategoryController::class, 'storeMany']);

Route::prefix('articles')->group(function () {
    // Отримання всіх статей
    Route::get('/', [ArticleController::class, 'index']);

    // Отримання конкретної статті
    Route::get('/{article}', [ArticleController::class, 'show']);

    // Створення нової статті
    Route::post('/', [ArticleController::class, 'store']);

    // Оновлення інформації про статтю
    Route::put('/{article}', [ArticleController::class, 'update']);

    // Видалення статті
    Route::delete('/{article}', [ArticleController::class, 'destroy']);
});










// Route::resource('posts', 'PostController');



