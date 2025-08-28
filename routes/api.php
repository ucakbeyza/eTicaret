<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//me endpoint to get the authenticated user details
Route::middleware('auth:sanctum')->get('/me',[AuthController::class,'me']);