<?php

namespace Iodev\Whois;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Loaders\ILoader;
use Iodev\Whois\Loaders\SocketLoader;
use Iodev\Whois\Modules\Asn\AsnInfo;
use Iodev\Whois\Modules\Asn\AsnModule;
use Iodev\Whois\Modules\Tld\DomainInfo;
use Iodev\Whois\Modules\Tld\DomainResponse;
use Iodev\Whois\Modules\Tld\TldModule;

class Whois
{
    /**
     * @param ILoader $loader
     * @return Whois
     */
    public static function create(ILoader $loader = null)
    {
        return new Whois($loader ?: new SocketLoader());
    }

    /**
     * @param ILoader $loader
     */
    public function __construct(ILoader $loader)
    {
        $this->loader = $loader;
    }

    /** @var ILoader */
    private $loader;

    /** @var TldModule */
    private $tldModule;

    /** @var AsnModule */
    private $asnModule;

    /**
     * @return ILoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return TldModule
     */
    public function getTldModule()
    {
        $this->tldModule = $this->tldModule ?: TldModule::create($this->loader);
        return $this->tldModule;
    }

    /**
     * @return AsnModule
     */
    public function getAsnModule()
    {
        $this->asnModule = $this->asnModule ?: AsnModule::create($this->loader);
        return $this->asnModule;
    }

    /**
     * @param string $domain
     * @return bool
     * @throws ServerMismatchException
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function isDomainAvailable($domain)
    {
        return $this->getTldModule()->isDomainAvailable($domain);
    }

    /**
     * @param string $domain
     * @return DomainResponse
     * @throws ServerMismatchException
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function lookupDomain($domain)
    {
        return $this->getTldModule()->lookupDomain($domain);
    }

    /**
     * @param string $domain
     * @return DomainInfo
     * @throws ServerMismatchException
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadDomainInfo($domain)
    {
        return $this->getTldModule()->loadDomainInfo($domain);
    }

    /**
     * @param string $asn
     * @return Response
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function lookupAsn($asn)
    {
        return $this->getAsnModule()->lookupAsn($asn);
    }

    /**
     * @param string $asn
     * @return AsnInfo
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadAsnInfo($asn)
    {
        return $this->getAsnModule()->loadAsnInfo($asn);
    }
}
