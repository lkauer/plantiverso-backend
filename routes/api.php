<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ForumController;
use App\Http\Controllers\API\ForumPostController;
use App\Http\Controllers\API\CatalogController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\ChatPostController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/checkingAuthenticated', function (){
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });

    Route::post('logout', [AuthController::class, 'logout']);

    //Category
    Route::get('view-category', [CategoryController::class, 'index']);
    Route::post('store-category', [CategoryController::class, 'store']);
    Route::get('edit-category/{id}', [CategoryController::class, 'edit']);
    Route::put('update-category/{id}', [CategoryController::class, 'update']);
    Route::get('all-categories', [CategoryController::class, 'getAllCategoires']);
    Route::delete('delete-category/{id}', [CategoryController::class, 'destroy']);

    //Catalog
    Route::get('view-catalog', [CatalogController::class, 'index']);
    Route::post('store-catalog', [CatalogController::class, 'store']);
    Route::get('edit-catalog/{id}', [CatalogController::class, 'edit']);
    Route::post('update-catalog/{id}', [CatalogController::class, 'update']);
    Route::get('all-catalog-itens', [CatalogController::class, 'getAllCatalogItens']);
    Route::get('general-search/{searchcontent}', [CatalogController::class, 'generalSearch']);
    Route::delete('delete-catalog/{id}', [CategoryController::class, 'destroy']);

    //Forum
    Route::get('view-forum', [ForumController::class, 'index']);
    Route::get('view-general-forum', [ForumController::class, 'general']);
    Route::post('store-forum', [ForumController::class, 'store']);
    Route::get('edit-forum/{id}', [ForumController::class, 'edit']);
    Route::put('update-forum/{id}', [ForumController::class, 'update']);
    Route::delete('delete-forum/{id}', [ForumController::class, 'destroy']);

    //Forum post
    Route::get('open-forum/{id}', [ForumPostController::class, 'view']);
    Route::post('store-forum-post', [ForumPostController::class, 'store']);

    //Chat
    Route::get('all-chat-conversations', [ChatController::class, 'index']);
    Route::post('define-user-chat', [ChatController::class, 'store']);
    Route::get('open-chat/{id}', [ChatController::class, 'view']);
    Route::post('store-chat-post', [ChatPostController::class, 'store']);
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
