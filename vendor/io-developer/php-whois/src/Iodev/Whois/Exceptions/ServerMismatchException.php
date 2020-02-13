<?php

namespace Iodev\Whois\Exceptions;

use Throwable;

class ServerMismatchException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
