<?php

namespace App\Http\Controllers\Api;

use App\Events\ForgotPassword;
use App\Events\UserRegistered;
use App\Exceptions\Auth\AuthAuthenticationFailed;
use App\Exceptions\Auth\AuthInvalidResetToken;
use App\Exceptions\Auth\AuthInvalidVerificationToken;
use App\Exceptions\Auth\AuthUserNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthForgotPasswordRequest;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Http\Requests\Auth\AuthResetPasswordRequest;
use App\Http\Requests\Auth\AuthVerificationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\PasswordReset;
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
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @param AuthVerificationRequest $request
     * @return JsonResponse
     * @throws AuthInvalidVerificationToken
     */
    public function verify(AuthVerificationRequest $request)
    {
        $validated = $request->validated();
        $tokenDecoded = base64_decode($validated["token"]);

        $user = User::where('confirmation_token', $tokenDecoded)->first();
        if (empty($user)) {
            throw new AuthInvalidVerificationToken();
        }

        $user->confirmation_token = null;
        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     * @throws AuthUserNotFound|AuthAuthenticationFailed
     */
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where("email", $validated["email"])->first();
        if (empty($user)) {
            throw new AuthUserNotFound();
        }

        $token = Auth::attempt($validated);
        if (!$token) {
            throw new AuthAuthenticationFailed();
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

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        if (auth()->user())
            auth()->logout();
        else
            return response()->json([
                'notlogged'
            ]);

        return response()->json([]);
    }

    /**
     * @param AuthForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(AuthForgotPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = User::where("email", $validated["email"])->first();
        if (empty($user)) {
            throw new AuthUserNotFound();
        }

        $token = Str::random(50);
        PasswordReset::create([
            'email' => $user->email,
            'token' => $token
        ]);

        event(new ForgotPassword($user, $token));

        return response()->json([]);
    }

    /**
     * @param AuthResetPasswordRequest $request
     * @return JsonResponse
     * @throws AuthInvalidResetToken|AuthUserNotFound
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $reset = PasswordReset::where("email", $validated["email"])->where("token", $validated["token"])->first();
        if (empty($reset)) {
            throw new AuthInvalidResetToken();
        }

        $user = User::where("email", $reset->email)->first();
        if (empty($user)) {
            throw new AuthUserNotFound();
        }

        $user->password = Hash::make($validated["password"]);
        $user->save();

        PasswordReset::where("email", $reset->email)->delete();

        return response()->json([]);
    }
}
