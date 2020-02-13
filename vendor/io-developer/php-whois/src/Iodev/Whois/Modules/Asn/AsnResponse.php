<?php

namespace Iodev\Whois\Modules\Asn;

use Iodev\Whois\Response;

/**
 * Immutable data object
 */
class AsnResponse extends Response
{
    /**
     * @param string $asn
     * @param string $query
     * @param string $text
     * @param string $host
     */
    public function __construct($asn, $query = "", $text = "", $host = "")
    {
        parent::__construct($query, $text, $host);
        $this->asn = strval($asn);
    }

    /** @var string */
    private $asn;

    /**
     * @return string
     */
    public function getAsn()
    {
        return $this->asn;
    }
}
