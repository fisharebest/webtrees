<?php

namespace Iodev\Whois\Punycode;

interface IPunycode
{
    function encode(string $unicode): string;

    function decode(string $ascii): string;
}