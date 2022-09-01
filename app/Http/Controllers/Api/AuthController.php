<?php

namespace App\Http\Controllers\Api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @param AuthRegisterRequest $request
     * @return JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'confirmation_token' => Str::random(25),
            'gender' => $validated['gender'] ?? User::GENDER_NONE,
            'password' => Hash::make($validated['password']),
        ]);

        event(new UserRegistered($user));

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where("email", $validated["email"])->first();
        if (empty($user)) {
            return response()->json([
                'error' => 'UserNotFound'
            ], 404);
        }

        $token = Auth::attempt($validated);
        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'user' => new UserResource($user),
            'access' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config("jwt.ttl"),
            ],
        ]);
    }
}
