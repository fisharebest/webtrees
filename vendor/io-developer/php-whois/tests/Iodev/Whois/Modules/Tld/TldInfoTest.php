<?php

namespace Iodev\Whois\Modules\Tld;

use PHPUnit\Framework\TestCase;

class TldInfoTest extends TestCase
{
    private static function createInfo($data = []): TldInfo
    {
        return new TldInfo(self::getResponse(), $data);
    }

    private static function getResponse()
    {
        return new TldResponse([
            "domain" => "domain.com",
            "query" => "domain.com",
            "text" => "Hello world",
        ]);
    }


    public function testConstructEmptyData()
    {
        $instance = new TldInfo(self::getResponse(), []);
        $this->assertInstanceOf(TldInfo::class, $instance);
    }

    public function testGetResponse()
    {
        $r = self::getResponse();
        $i = new TldInfo($r, []);
        self::assertSame($r, $i->getResponse());
    }


    public function testGetDomainName()
    {
        $i = self::createInfo([ "domainName" => "foo.bar" ]);
        self::assertEquals("foo.bar", $i->domainName);
    }

    public function testGetDomainNameDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->domainName);
    }


    public function testGetDomainNameUnicode()
    {
        $i = self::createInfo([ "domainName" => "foo.bar" ]);
        self::assertEquals("foo.bar", $i->getDomainNameUnicode());
    }

    public function testGetDomainNameUnicodePunnycode()
    {
        $i = self::createInfo([ "domainName" => "xn--d1acufc.xn--p1ai" ]);
        self::assertEquals("домен.рф", $i->getDomainNameUnicode());
    }

    public function testGetDomainNameUnicodeDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->getDomainNameUnicode());
    }


    public function testGetWhoisServer()
    {
        $i = self::createInfo([ "whoisServer" => "whois.bar" ]);
        self::assertEquals("whois.bar", $i->whoisServer);
    }

    public function testGetWhoisServerDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->whoisServer);
    }


    public function testGetNameServers()
    {
        $i = self::createInfo([ "nameServers" => [ "a.bar", "b.baz" ] ]);
        self::assertEquals([ "a.bar", "b.baz" ], $i->nameServers);
    }

    public function testGetNameServersDefault()
    {
        $i = self::createInfo();
        self::assertSame([], $i->nameServers);
    }


    public function testGetCreationDate()
    {
        $i = self::createInfo([ "creationDate" => 123456789 ]);
        self::assertEquals(123456789, $i->creationDate);
    }

    public function testGetCreationDateDefault()
    {
        $i = self::createInfo();
        self::assertSame(0, $i->creationDate);
    }


    public function testGetExpirationDate()
    {
        $i = self::createInfo([ "expirationDate" => 123456789 ]);
        self::assertEquals(123456789, $i->expirationDate);
    }

    public function testGetExpirationDateDefault()
    {
        $i = self::createInfo();
        self::assertSame(0, $i->expirationDate);
    }


    public function testGetStates()
    {
        $i = self::createInfo([ "states" => [ "abc", "def", "ghi" ] ]);
        self::assertEquals([ "abc", "def", "ghi" ], $i->states);
    }

    public function testGetStatesDefault()
    {
        $i = self::createInfo();
        self::assertSame([], $i->states);
    }


    public function testGetOwner()
    {
        $i = self::createInfo([ "owner" => "Some Company" ]);
        self::assertEquals("Some Company", $i->owner);
    }

    public function testGetOwnerDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->owner);
    }


    public function testGetRegistrar()
    {
        $i = self::createInfo([ "registrar" => "Some Registrar" ]);
        self::assertEquals("Some Registrar", $i->registrar);
    }

    public function testGetRegistrarDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->registrar);
    }
}
