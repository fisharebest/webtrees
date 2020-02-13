<?php

namespace Iodev\Whois\Modules\Asn;

use InvalidArgumentException;

/**
 * Immutable data object
 */
class AsnRouteInfo
{
    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct($data = [])
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
        $this->data = $data;
    }

    /** @var array */
    private $data;

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->getval("route", "");
    }

    /**
     * @return string
     */
    public function getRoute6()
    {
        return $this->getval("route6", "");
    }

    /**
     * @return string
     */
    public function getDescr()
    {
        return $this->getval("descr", "");
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->getval("origin", "");
    }

    /**
     * @return string
     */
    public function getMntBy()
    {
        return $this->getval("mnt-by", "");
    }

    /**
     * @return string
     */
    public function getChanged()
    {
        return $this->getval("changed", "");
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getval("source", "");
    }

    /**
     * @param $key
     * @param mixed $default
     * @return mixed
     */
    private function getval($key, $default = "")
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}