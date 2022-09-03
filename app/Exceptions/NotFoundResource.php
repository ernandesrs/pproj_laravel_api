<?php

namespace App\Exceptions;

use Exception;

class NotFoundResource extends Exception
{
    /**
     * @var string
     */
    protected $message = "The required resource not found.";

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
