<?php

namespace Iodev\Whois;

/**
 * Immutable data object
 */
class Response
{
    /**
     * @param string $query
     * @param string $text
     * @param string $host
     */
    public function __construct($query = "", $text = "", $host = "")
    {
        $this->query = strval($query);
        $this->text = strval($text);
        $this->host = strval($host);
    }

    /** @var string */
    private $query;
    
    /** @var string */
    private $text;

    /** @var string */
    private $host;

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
}
