<?php
namespace Aura\Router;

use Psr\Log\AbstractLogger;

class FakeLoggerV3 extends AbstractLogger
{
    public $lines = [];

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $strtr = [];
        foreach ($context as $key => $val) {
            $strtr["{{$key}}"] = $val;
        }
        $message = strtr($message, $strtr);
        $this->lines[] = "$level: $message";
    }
}
