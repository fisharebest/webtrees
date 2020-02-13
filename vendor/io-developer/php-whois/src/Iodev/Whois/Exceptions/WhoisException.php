<?php

namespace Iodev\Whois\Exceptions;

use Throwable;

class WhoisException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
