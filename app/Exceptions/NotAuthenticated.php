<?php

namespace App\Exceptions;

use Exception;

class NotAuthenticated extends Exception
{
    /**
     * @var string
     */
    protected $message = "Your are not authenticated!";

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
