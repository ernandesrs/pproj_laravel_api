<?php

namespace App\Exceptions\Auth;

use Exception;

class AuthInvalidResetToken extends Exception
{
    /**
     * @var string
     */
    protected $message = "The reset token is invalid.";

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
