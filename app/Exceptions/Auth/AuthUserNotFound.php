<?php

namespace App\Exceptions\Auth;

use Exception;

class AuthUserNotFound extends Exception
{
    /**
     * @var string
     */
    protected $message = "Required user not found.";

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->message,
        ], 404);
    }
}
