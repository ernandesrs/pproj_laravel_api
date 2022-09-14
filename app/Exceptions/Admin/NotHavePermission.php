<?php

namespace App\Exceptions\Admin;

use Exception;

class NotHavePermission extends Exception
{
    /**
     * @var string
     */
    protected $message = "No permission for this action type.";

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
