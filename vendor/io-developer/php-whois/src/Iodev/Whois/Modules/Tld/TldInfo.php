<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\DataObject;
use Iodev\Whois\Helpers\DomainHelper;

/**
 * @property string $parserType
 * @property string $domainName
 * @property string $whoisServer
 * @property string[] $nameServers
 * @property int $creationDate
 * @property int $expirationDate
 * @property string[] $states
 * @property string $owner
 * @property string $registrar
 */
class TldInfo extends DataObject
{
    use TldInfoDeprecated;

    /**
     * @param TldResponse $response
     * @param array $data
     * @param array $extra
     * @throws InvalidArgumentException
     */
    public function __construct(TldResponse $response, $data = [], $extra = [])
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Data must be an array");
        }
        parent::__construct($data);
        $this->response = $response;
        $this->extra = $extra;
    }

    /** @var array */
    protected $dataDefault = [
        "parserType" => "",
        "domainName" => "",
        "whoisServer" => "",
        "nameServers" => [],
        "creationDate" => 0,
        "expirationDate" => 0,
        "states" => [],
        "owner" => "",
        "registrar" => "",
    ];

    /** @var TldResponse */
    protected $response;

    /** @var array */
    protected $extra;

    /**
     * @return TldResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getExtraVal($key, $default = null)
    {
        return $this->extra[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function getDomainNameUnicode()
    {
        return DomainHelper::toUnicode($this->domainName);
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
        $states = $this->states;
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
