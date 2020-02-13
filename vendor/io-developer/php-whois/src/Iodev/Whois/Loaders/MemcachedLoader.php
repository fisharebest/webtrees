<?php

namespace Iodev\Whois\Loaders;

use Memcached;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\WhoisException;

class MemcachedLoader implements ILoader
{
    public function __construct(ILoader $l, Memcached $m, $keyPrefix = "", $ttl = 3600)
    {
        $this->loader = $l;
        $this->memcached = $m;
        $this->keyPrefix = $keyPrefix;
        $this->ttl = $ttl;
    }

    /** @var ILoader */
    private $loader;

    /** @var Memcached */
    private $memcached;

    /** @var string */
    private $keyPrefix;

    /** @var int */
    private $ttl;

    /**
     * @param string $whoisHost
     * @param string $query
     * @return string
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadText($whoisHost, $query)
    {
        $key = $this->keyPrefix . md5(serialize([$whoisHost, $query]));
        $val = $this->memcached->get($key);
        if ($val) {
            return unserialize($val);
        }
        $val = $this->loader->loadText($whoisHost, $query);
        $this->memcached->set($key, serialize($val), $this->ttl);
        return $val;
    }
}