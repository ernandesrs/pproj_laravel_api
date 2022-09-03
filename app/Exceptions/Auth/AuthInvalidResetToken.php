<?php

namespace App\Exceptions\Auth;

use App\Exceptions\MyExceptionTrait;
use Exception;

class AuthInvalidResetToken extends Exception
{
    use MyExceptionTrait;

    /**
     * @var string
     */
    protected $message = "The reset token is invalid.";
}
