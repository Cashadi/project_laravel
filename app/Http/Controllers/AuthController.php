<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            "email"=> "required|string|email",
            "password"=> "required|string",
        ]);

        $credentials = $request->only("email","password");

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                "status"=> "error",
                "message"=> "username/password wrong",
            ], 401);
        }

        return response()->json([
            "status"=> "success",
            "token" => $token,
        ]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'=> 'required|string',
        ]);

        $data = [
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'role'=> $request->role,
        ];

        $user = User::create($data);

        return response()->json([
            'message' => 'registration sucessfully',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        return response()->json([
            'status' => 'succes',
            'message'=> 'sucessfulyy log out',
        ]);
    }
}