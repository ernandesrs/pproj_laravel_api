<?php

namespace App\Exceptions\Auth;

use Exception;

class AuthInvalidVerificationToken extends Exception
{
    /**
     * @var string
     */
    protected $message = "The verification token is invalid.";

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->message,
        ], 400);
    }
}
