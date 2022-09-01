<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'v1'
], function () {
    Route::post("test", function () {
        return response()->json([
            'Hello World :D!'
        ]);
    });

    Route::post("/register", [AuthController::class, "register"])->name("auth.register");
    Route::post("/register-confirm", [AuthController::class, "verify"])->name("auth.verify");
    Route::post("/login", [AuthController::class, "login"])->name("auth.login");
    Route::post("/forgot-password", [AuthController::class, "forgotPassword"])->name("auth.forgotPassword");
    Route::post("/reset-password", [AuthController::class, "resetPassword"])->name("auth.resetPassword");
});
