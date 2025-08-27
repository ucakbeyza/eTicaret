<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    public function register(RegisterRequest $request){

        //User oluşturma
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)//Şifre plain text saklanmaz(hashlenir)
        ]);
        //Başarılı cevap döner
        return ResponseBuilder::success($user, "User registered successfully", 201);
    }
    public function login(LoginRequest $request){

        //Kullanıcıyı mail ile bulma
        $user = User::where('email', $request->email)->first();
        //Kullanıcı yoksa veya şifre uyuşmuyorsa hata döner
        if(!$user || !Hash::check($request->password, $user->password)){
            return ResponseBuilder::error(
                [],
                "INVALID_CREDENTIALS",
                401
            );
        }

        //Token oluşturma
        $token = $user->createToken('auth_token')->plainTextToken;

        //Başarılı cevap döner
        return ResponseBuilder::success([
            'user' => $user,
            'token' => $token
        ], "Login successful", 200);
    }
    
}
