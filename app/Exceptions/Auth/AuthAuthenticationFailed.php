<?php

namespace App\Exceptions\Auth;

use App\Exceptions\MyExceptionTrait;
use Exception;

class AuthAuthenticationFailed extends Exception
{
    use MyExceptionTrait;

    /**
     * @var string
     */
    protected $message = "Email or/and password don\'t match";
}
