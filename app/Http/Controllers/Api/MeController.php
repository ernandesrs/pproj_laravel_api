<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * @return UserResource
     */
    public function index()
    {
        return new UserResource(auth()->user());
    }
}
