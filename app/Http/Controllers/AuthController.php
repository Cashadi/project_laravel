<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|string|email",
            "password" => "required|string",
        ]);

        $credentials = $request->only("email", "password");

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                "status" => "error",
                "message" => "username/password wrong",
            ], 401);
        }

        return response()->json([
            "status" => "success",
            "token" => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ];

        $user = User::create($data);

        return response()->json([
            'message' => 'registration sucessfully',
        ]);
    }

    public function addSeller(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $validated['role'] = 'penjual';

        $admin = auth()->user()->role;

        if ($admin == 'penjual') {
            
            User::create($validated);

            return response()->json([
                'message' => 'Register seller succesfully'
            ], 201);

        } else if ($admin == 'pembeli') {

            return response()->json([
                'message' => 'You are not a MLM',
            ], 404);

        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'status' => 'succes',
            'message' => 'sucessfulyy log out',
        ]);
    }
}
