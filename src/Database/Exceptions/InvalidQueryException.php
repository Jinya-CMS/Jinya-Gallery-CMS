<?php

namespace App\Database\Exceptions;

use Exception;
use Throwable;

class InvalidQueryException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null, public array $errorInfo = [])
    {
        parent::__construct($message, $code, $previous);
    }

}