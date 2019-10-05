<?php
namespace Aura\Router;

use Psr\Log\AbstractLogger;

class FakeLogger extends AbstractLogger
{
    public $lines = [];

    public function log($level, $message, array $context = [])
    {
        $strtr = [];
        foreach ($context as $key => $val) {
            $strtr["{{$key}}"] = $val;
        }
        $message = strtr($message, $strtr);
        $this->lines[] = "$level: $message";
    }
}
