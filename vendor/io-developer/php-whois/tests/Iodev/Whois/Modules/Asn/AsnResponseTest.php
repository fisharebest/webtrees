<?php

namespace Iodev\Whois\Modules\Asn;

use PHPUnit\Framework\TestCase;

class AsnResponseTest extends TestCase
{
    /** @var AsnResponse */
    private $resp;

    public function setUp(): void
    {
        $this->resp = new AsnResponse([
            "asn" => "AS32934",
            "host" => "whois.host.abc",
            "query" => "-i origin AS32934",
            "text" => "Test content",
        ]);
    }

    public function testGetAsn()
    {
        self::assertEquals("AS32934", $this->resp->asn);
    }

    public function testGetQuery()
    {
        self::assertEquals("-i origin AS32934", $this->resp->query);
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