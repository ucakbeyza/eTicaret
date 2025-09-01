<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CategoryController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//me endpoint to get the authenticated user details
Route::middleware('auth:sanctum')->get('/me',[AuthController::class,'me']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/address/create', [AddressController::class, 'create']);
    Route::post('/address/update', [AddressController::class, 'update']);
    Route::post('/address/delete', [AddressController::class, 'delete']);
    Route::get('/address/list', [AddressController::class, 'list']);
});

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::post('/categories/create', [CategoryController::class, 'create']);

Route::post('/categories/update', [CategoryController::class, 'update']);

Route::post('/categories/delete', [CategoryController::class, 'delete']);

