<?php

namespace Iodev\Whois\Modules\Tld;

use PHPUnit\Framework\TestCase;

class TldResponseTest extends TestCase
{
    /** @var TldResponse */
    private $resp;

    public function setUp(): void
    {
        $this->resp = new TldResponse([
            "domain" => "domain.some",
            "host" => "whois.host.abc",
            "query" => "domain.some",
            "text" => "Test content",
        ]);
    }

    public function testGetDomain()
    {
        self::assertEquals("domain.some", $this->resp->domain);
    }

    public function testGetQuery()
    {
        self::assertEquals("domain.some", $this->resp->query);
    }

    public function testGetText()
    {
        self::assertEquals("Test content", $this->resp->text);
    }

    public function testGetHost()
    {
        self::assertEquals("whois.host.abc", $this->resp->host);
    }
}