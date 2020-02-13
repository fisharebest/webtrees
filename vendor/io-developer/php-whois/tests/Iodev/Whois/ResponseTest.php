<?php

namespace Iodev\Whois;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Response */
    private $resp;

    public function setUp()
    {
        $this->resp = new Response("domain.some", "Test content", "whois.host.abc");
    }


    public function testGetQuery()
    {
        self::assertEquals("domain.some", $this->resp->getQuery());
    }

    public function testGetText()
    {
        self::assertEquals("Test content", $this->resp->getText());
    }

    public function testGetHost()
    {
        self::assertEquals("whois.host.abc", $this->resp->getHost());
    }
}