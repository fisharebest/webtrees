<?php

namespace Iodev\Whois;

use Iodev\Whois\Loaders\ILoader;
use Iodev\Whois\Modules\Asn\AsnModule;
use Iodev\Whois\Modules\Tld\TldModule;

interface IFactory
{
    /**
     * @return ILoader
     */
    function createLoader(): ILoader;

    /**
     * @param Whois $ehois
     * @return AsnModule
     */
    function createAsnModule(Whois $ehois): AsnModule;

    /**
     * @param Whois $ehois
     * @return TldModule
     */
    function createTldModule(Whois $ehois): TldModule;
}
