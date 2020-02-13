<?php

namespace Iodev\Whois\Modules\Tld;

use InvalidArgumentException;
use Iodev\Whois\Helpers\DomainHelper;

/**
 * Immutable data object
 */
class TldServer
{
    /** @var int */
    private static $counter = 0;

    /**
     * @param array $data
     * @param TldParser $defaultParser
     * @return TldServer
     */
    public static function fromData($data, TldParser $defaultParser = null)
    {
        $opts = isset($data['parserOptions']) ? $data['parserOptions'] : [];

        /* @var $parser TldParser */
        $parser = $defaultParser;
        if (isset($data['parserClass'])) {
            $parser = TldParser::createByClass(
                $data['parserClass'],
                isset($data['parserType']) ? $data['parserType'] : null
            )->setOptions($opts);
        } elseif (isset($data['parserType'])) {
            $parser = TldParser::create($data['parserType'])->setOptions($opts);
        }
        $parser = $parser ?: TldParser::create()->setOptions($opts);

        return new TldServer(
            isset($data['zone']) ? $data['zone'] : '',
            isset($data['host']) ? $data['host'] : '',
            !empty($data['centralized']),
            $parser,
            isset($data['queryFormat']) ? $data['queryFormat'] : null
        );
    }

    /**
     * @param array $dataList
     * @param TldParser $defaultParser
     * @return TldServer[]
     */
    public static function fromDataList($dataList, TldParser $defaultParser = null)
    {
        $defaultParser = $defaultParser ? $defaultParser : TldParser::create();
        $servers = [];
        foreach ($dataList as $data) {
            $servers[] = self::fromData($data, $defaultParser);
        }
        return $servers;
    }

    /**
     * @param string $zone Must starts from '.'
     * @param string $host
     * @param bool $centralized
     * @param TldParser $parser
     * @param string $queryFormat
     * @param bool $inversedDateMMDD
     */
    public function __construct($zone, $host, $centralized, TldParser $parser, $queryFormat = null)
    {
        $this->uid = ++self::$counter;
        $this->zone = strval($zone);
        if (empty($this->zone)) {
            throw new InvalidArgumentException("Zone must be specified");
        }
        $this->zone = ($this->zone[0] == '.') ? $this->zone : ".{$this->zone}";
        $this->inverseZoneParts = array_reverse(explode('.', $this->zone));
        array_pop($this->inverseZoneParts);

        $this->host = strval($host);
        if (empty($this->host)) {
            throw new InvalidArgumentException("Host must be specified");
        }
        $this->centralized = (bool)$centralized;
        $this->parser = $parser;
        $this->queryFormat = !empty($queryFormat) ? strval($queryFormat) : "%s\r\n";
    }

    /** @var string */
    private $uid;

    /** @var string */
    private $zone;

    /** @var string[] */
    private $inverseZoneParts;

    /** @var bool */
    private $centralized;

    /** @var string */
    private $host;
    
    /** @var TldParser */
    private $parser;

    /** @var string */
    private $queryFormat;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * @return bool
     */
    public function isCentralized()
    {
        return (bool)$this->centralized;
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function isDomainZone($domain)
    {
        return $this->matchDomainZone($domain) > 0;
    }

    /**
     * @param string $domain
     * @return int
     */
    public function matchDomainZone($domain)
    {
        $domainParts = explode('.', $domain);
        if ($this->zone === '.' && count($domainParts) === 1) {
            return 1;
        }
        array_shift($domainParts);
        $domainCount = count($domainParts);
        $zoneCount = count($this->inverseZoneParts);
        if (count($domainParts) < $zoneCount) {
            return 0;
        }
        $i = -1;
        while (++$i < $zoneCount) {
            $zonePart = $this->inverseZoneParts[$i];
            $domainPart = $domainParts[$domainCount - $i - 1];
            if ($zonePart != $domainPart && $zonePart != '*') {
                return 0;
            }
        }
        return $zoneCount;
    }

    /**
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return TldParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return string
     */
    public function getQueryFormat()
    {
        return $this->queryFormat;
    }

    /**
     * @param string $domain
     * @param bool $strict
     * @return string
     */
    public function buildDomainQuery($domain, $strict = false)
    {
        $query = sprintf($this->queryFormat, $domain);
        return $strict ? "=$query" : $query;
    }
}
