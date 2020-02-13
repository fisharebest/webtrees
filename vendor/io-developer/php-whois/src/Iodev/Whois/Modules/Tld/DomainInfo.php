<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\Helpers\DomainHelper;

/**
 * Immutable data object
 */
class DomainInfo
{
    /**
     * @param DomainResponse $response
     * @param array $data
     * @param string $parserType
     * @throws InvalidArgumentException
     */
    public function __construct(DomainResponse $response, $data = [], $parserType = '')
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
        $this->response = $response;
        $this->data = $data;
        $this->parserType = $parserType;
    }

    /** @var DomainResponse */
    private $response;

    /** @var array */
    private $data;

    /** @var string */
    private $parserType;

    /**
     * @return DomainResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getParserType()
    {
        return $this->parserType;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->getval("domainName", "");
    }

    /**
     * @return string
     */
    public function getDomainNameUnicode()
    {
        return DomainHelper::toUnicode($this->getDomainName());
    }

    /**
     * @return string
     */
    public function getWhoisServer()
    {
        return $this->getval("whoisServer", "");
    }

    /**
     * @return string[]
     */
    public function getNameServers()
    {
        return $this->getval("nameServers", []);
    }

    /**
     * @return int
     */
    public function getCreationDate()
    {
        return $this->getval("creationDate", 0);
    }

    /**
     * @return int
     */
    public function getExpirationDate()
    {
        return $this->getval("expirationDate", 0);
    }

    /**
     * @return string[]
     */
    public function getStates()
    {
        return $this->getval("states", []);
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->getval("owner", "");
    }

    /**
     * @return string
     */
    public function getRegistrar()
    {
        return $this->getval("registrar", "");
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

    /**
     * @param array|null $keys
     * @return bool
     */
    public function isEmpty($keys = null)
    {
        $empty = true;
        $keys = $keys ? $keys : array_keys($this->data);
        foreach ($keys as $key) {
            $empty = $empty && empty($this->data[$key]);
        }
        return $empty;
    }

    /**
     * @param array $badFirstStatesDict
     * @return bool
     */
    public function isValuable($badFirstStatesDict = [])
    {
        $states = $this->getStates();
        $firstState = empty($states) ? '' : reset($states);
        $firstState = mb_strtolower(trim($firstState));
        if (!empty($badFirstStatesDict[$firstState])) {
            return false;
        }
        $primaryKeys = ['domainName'];
        $secondaryKeys = [
            "states",
            "nameServers",
            "owner",
            "creationDate",
            "expirationDate",
            "registrar",
        ];
        return !$this->isEmpty($primaryKeys) && !$this->isEmpty($secondaryKeys);
    }

    /**
     * @return int
     */
    public function calcValuation()
    {
        $weights = [
            'domainName' => 100,
            'nameServers' => 20,
            'creationDate' => 6,
            'expirationDate' => 6,
            'states' => 4,
            'owner' => 4,
            'registrar' => 3,
            'whoisServer' => 2,
        ];
        $sum = 0;
        foreach ($this->data as $k => $v) {
            if (!empty($v) && !empty($weights[$k])) {
                $w = $weights[$k];
                $sum += is_array($v) ? $w * count($v) : $w;
            }
        }
        return $sum;
    }
}
