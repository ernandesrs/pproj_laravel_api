<?php

namespace App\Exceptions\Auth;

use App\Exceptions\MyExceptionTrait;
use Exception;

class AuthInvalidVerificationToken extends Exception
{
    use MyExceptionTrait;

    /**
     * @var string
     */
    protected $message = "The verification token is invalid.";
}
