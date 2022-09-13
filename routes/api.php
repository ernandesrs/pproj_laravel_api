<?php

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeController;
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

    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post("/login", [AuthController::class, "login"])->name("auth.login");
        Route::post("/logout", [AuthController::class, "logout"])->name("auth.logout");
        Route::post("/register", [AuthController::class, "register"])->name("auth.register");
        Route::post("/register-confirm", [AuthController::class, "verify"])->name("auth.verify");
        Route::post("/forgot-password", [AuthController::class, "forgotPassword"])->name("auth.forgotPassword");
        Route::post("/reset-password", [AuthController::class, "resetPassword"])->name("auth.resetPassword");
    });

    Route::group([
        "prefix" => "me",
        "middleware" => "me"
    ], function () {
        Route::get("/", [MeController::class, "me"])->name("me.me");
    });

    Route::group([
        'prefix' => 'admin',
        'middleware' => 'admin',
    ], function () {
        Route::get("/users", [AdminUserController::class, "index"])->name("admin.users.index");
        Route::post("/user/store", [AdminUserController::class, "store"])->name("admin.users.store");
        Route::post("/user/update/{user}", [AdminUserController::class, "update"])->name("admin.users.update");
        Route::post("/user/destroy/{user}", [AdminUserController::class, "destroy"])->name("admin.users.destroy");
        Route::post("/user/photo-store/{user}", [AdminUserController::class, "photoStore"])->name("admin.users.photoStore");
        Route::post("/user/photo-delete/{user}", [AdminUserController::class, "photoDelete"])->name("admin.users.photoDelete");
    });
});
