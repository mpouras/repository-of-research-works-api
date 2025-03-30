<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['logout']),
        ];
    }

    public function login(LoginUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['identifier'])->orWhere('username', $validatedData['identifier'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'The provided credentials are incorrect.'
                ],
            ], 401);
        }

        $token = $user->createToken('auth-token');

        return response()->json([
            'message' => 'Logged in successfully!',
            'token' => $token->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }

    public function register(RegisterUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'user',
        ]);

        $token = $user->createToken('auth-token');

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
            'user' => $request->user()->username,
        ]);
    }
}
