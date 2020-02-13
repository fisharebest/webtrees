<?php

namespace Iodev\Whois\Modules\Tld\Parsers;

use Iodev\Whois\Modules\Tld\DomainInfo;
use Iodev\Whois\Modules\Tld\DomainResponse;
use Iodev\Whois\Modules\Tld\TldParser;

class AutoParser extends TldParser
{
    public function __construct()
    {
        $this->parsers = [
            TldParser::create(TldParser::COMMON),
            TldParser::create(TldParser::COMMON_FLAT),
            TldParser::create(TldParser::BLOCK),
            TldParser::create(TldParser::INDENT_AUTOFIX),
            TldParser::create(TldParser::INDENT),
        ];
    }

    /** @var TldParser[] */
    private $parsers = [];

    /**
     * @return string
     */
    public function getType()
    {
        return TldParser::AUTO;
    }

    /**
     * @param array $cfg
     * @return $this
     */
    public function setConfig($cfg)
    {
        return $this;
    }

    /**
     * @param DomainResponse $response
     * @return DomainInfo
     */
    public function parseResponse(DomainResponse $response)
    {
        $bestInfo = null;
        $bestVal = 0;
        foreach ($this->parsers as $parser) {
            $info = $parser->setOptions($this->options)->parseResponse($response);
            if (!$info) {
                continue;
            }
            $val = $info->calcValuation();
            if ($val > $bestVal) {
                $bestVal = $val;
                $bestInfo = $info;
            }
        }
        return $bestInfo;
    }
}
