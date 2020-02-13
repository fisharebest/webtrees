<?php

namespace Iodev\Whois\Modules\Asn;

use Iodev\Whois\Config;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Loaders\ILoader;
use Iodev\Whois\Modules\Module;
use Iodev\Whois\Modules\ModuleType;

class AsnModule extends Module
{
    /**
     * @param ILoader $loader
     * @param AsnServer[] $servers
     * @return self
     */
    public static function create(ILoader $loader = null, $servers = null)
    {
        $m = new self($loader);
        $m->setServers($servers ?: AsnServer::fromDataList(Config::load("module.asn.servers")));
        return $m;
    }

    /**
     * @param ILoader $loader
     */
    public function __construct(ILoader $loader)
    {
        parent::__construct(ModuleType::ASN, $loader);
    }

    /** @var AsnServer[] */
    private $servers = [];

    /**
     * @return AsnServer[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @param AsnServer[] $servers
     * @return $this
     */
    public function addServers($servers)
    {
        return $this->setServers(array_merge($this->servers, $servers));
    }

    /**
     * @param AsnServer[] $servers
     * @return $this
     */
    public function setServers($servers)
    {
        $this->servers = $servers;
        return $this;
    }

    /**
     * @param string $asn
     * @param AsnServer $server
     * @return AsnResponse
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function lookupAsn($asn, AsnServer $server = null)
    {
        if ($server) {
            return $this->loadResponse($asn, $server);
        }
        list ($resp, ) = $this->loadData($asn);
        return $resp;
    }

    /**
     * @param $asn
     * @param AsnServer $server
     * @return AsnInfo
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadAsnInfo($asn, AsnServer $server = null)
    {
        if ($server) {
            $resp = $this->loadResponse($asn, $server);
            return $server->getParser()->parseResponse($resp);
        }
        list (, $info) = $this->loadData($asn);
        return $info;
    }

    /**
     * @param string $asn
     * @return array
     * @throws ConnectionException
     * @throws WhoisException
     */
    private function loadData($asn)
    {
        $response = null;
        $info = null;
        $error = null;
        foreach ($this->servers as $s) {
            try {
                $response = $this->loadResponse($asn, $s);
                $info = $s->getParser()->parseResponse($response);
                if ($info) {
                    break;
                }
            } catch (ConnectionException $e) {
                $error = $e;
            }
        }
        if (!$response && $error) {
            throw $error;
        }
        return [$response, $info];
    }

    /**
     * @param string $asn
     * @param AsnServer $server
     * @return AsnResponse
     * @throws ConnectionException
     * @throws WhoisException
     */
    private function loadResponse($asn, AsnServer $server)
    {
        $host = $server->getHost();
        $query = $server->buildQuery($asn);
        $text = $this->getLoader()->loadText($host, $query);
        return new AsnResponse($asn, $query, $text, $host);
    }
}
