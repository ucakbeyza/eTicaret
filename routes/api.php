<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//me endpoint to get the authenticated user details
Route::middleware('auth:sanctum')->get('/me',[AuthController::class,'me']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/address/create', [AddressController::class, 'create']);
    Route::post('/address/update', [AddressController::class, 'update']);
    Route::post('/address/delete', [AddressController::class, 'delete']);
    Route::get('/address/list', [AddressController::class, 'list']);
    Route::post('/categories/create', [CategoryController::class, 'create']);
        
    Route::post('/categories/update', [CategoryController::class, 'update']);

    Route::post('/categories/delete', [CategoryController::class, 'delete']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products/create', [ProductController::class, 'create']);
    Route::post('/products/update', [ProductController::class, 'update']);
    Route::post('/products/delete', [ProductController::class, 'delete']);

    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::post('/cart/delete', [CartController::class, 'delete']);
    Route::get('/cart/list', [CartController::class, 'list']);
    Route::post('/cart/clear', [CartController::class, 'clear']);
    Route::post('/checkout',[CartController::class, 'checkout']);
});

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/categories/{id}', [CategoryController::class, 'show']);



