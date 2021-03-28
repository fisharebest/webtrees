<?php

namespace Iodev\Whois;

use Iodev\Whois\Loaders\ILoader;

trait WhoisDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @param ILoader $loader
     * @return Whois
     */
    public static function create(ILoader $loader = null)
    {
        return Factory::get()->createWhois($loader);
    }
}
