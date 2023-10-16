<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\AuthenticateUserRequest;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = strtolower($request->input('email'));
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'message' => 'User successfully registered',
        ], 201);
    }

    public function login(AuthenticateUserRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            $user = $request->user();
            $user->tokens()->delete();
            $token = $user->createToken('auth_token');

            return response()->json([
                'message' => 'Successfully authenticated',
                'token' => $token->plainTextToken,
            ], 200);
        }

        return response()->json([
            'message' => 'Unauthenticated',
        ], 401);
    }
}
