<?php

namespace App\Exceptions\Auth;

use App\Exceptions\MyExceptionTrait;
use Exception;

class AuthUserNotFound extends Exception
{
    use MyExceptionTrait;

    /**
     * @var string
     */
    protected $message = "Required user not found.";
}
