<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => [
                'required',
                'string',
                'min:10',               // أقوى من min:8
                'confirmed',
                'regex:/[a-z]/',         // حرف صغير
                'regex:/[A-Z]/',         // حرف كبير
                'regex:/[0-9]/',         // رقم
                'regex:/[@$!%*#?&]/',    // رمز خاص
            ],
            'address'   => 'required|string|min:10', 
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'address'  => $request->address,   
            'role'     => 'patient',           
        ]);

        return response()->json([
            'message' => 'User Registered Successfully',
            'user'    => $user
        ], 201);
    }

   
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $user  = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'user'    => $user,
            'token'   => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logout Successfully']);
    }

    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }
}