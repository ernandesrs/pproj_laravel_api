<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dash(): JsonResponse
    {
        return response()->json([
            "users" => [
                "total" => User::all()->count(),
                "verified" => User::whereNotNull("email_verified_at")->count(),
                "unverified" => User::whereNull("email_verified_at")->count(),
            ]
        ]);
    }
}
