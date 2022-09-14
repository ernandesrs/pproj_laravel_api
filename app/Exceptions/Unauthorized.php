<?php

namespace App\Exceptions;

use Exception;

class Unauthorized extends Exception
{
    /**
     * @var string
     */
    protected $message = "You is not authorized for this action.";

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => class_basename($this),
            'message' => $this->message,
        ], 403);
    }
}
