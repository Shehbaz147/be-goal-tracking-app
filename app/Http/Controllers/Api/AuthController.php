<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where('email', $request->email)->first();

        if(\Auth::attempt($request->only(['email', 'password']))){
            $token = $user->createToken('Goals API Token')->plainTextToken;
            $user = array_merge($user->toArray(), [
                'access_token' => $token
            ]);

            return response()->json([
                'user' => $user
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'message' => 'Invalid Credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        $response = [
            'user' => $user,
            'access_token' => $token
        ];

        return response($response, 200);
    }

    public function logout()
    {
        \Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
