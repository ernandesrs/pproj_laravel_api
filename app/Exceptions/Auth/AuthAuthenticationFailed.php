<?php

namespace App\Exceptions\Auth;

use Exception;

class AuthAuthenticationFailed extends Exception
{
    /**
     * @var string
     */
    protected $message = "Email or/and password don\'t match";

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->message,
        ], 401);
    }
}
