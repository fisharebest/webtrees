<?php

namespace Iodev\Whois\Punycode;

use TrueBV\Punycode;

class TruePunycode implements IPunycode
{
    public function encode(string $unicode): string
    {
        return empty($unicode) ? '' : (new Punycode())->encode($unicode);
    }

    public function decode(string $ascii): string
    {
        return empty($ascii) ? '' : (new Punycode())->decode($ascii);
    }
}
