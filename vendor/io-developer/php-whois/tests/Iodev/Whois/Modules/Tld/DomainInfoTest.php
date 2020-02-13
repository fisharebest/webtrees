<?php

namespace Iodev\Whois\Modules\Tld;

class DomainInfoTest extends \PHPUnit_Framework_TestCase
{
    private static function createInfo($data = [])
    {
        return new DomainInfo(self::getResponse(), $data);
    }

    private static function getResponse()
    {
        return new DomainResponse("domain.com", "domain.com", "Hello world");
    }


    public function testConstructEmptyData()
    {
        new DomainInfo(self::getResponse(), []);
    }

    public function testGetResponse()
    {
        $r = self::getResponse();
        $i = new DomainInfo($r, []);
        self::assertSame($r, $i->getResponse());
    }


    public function testGetDomainName()
    {
        $i = self::createInfo([ "domainName" => "foo.bar" ]);
        self::assertEquals("foo.bar", $i->getDomainName());
    }

    public function testGetDomainNameDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->getDomainName());
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
        self::assertEquals("whois.bar", $i->getWhoisServer());
    }

    public function testGetWhoisServerDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->getWhoisServer());
    }


    public function testGetNameServers()
    {
        $i = self::createInfo([ "nameServers" => [ "a.bar", "b.baz" ] ]);
        self::assertEquals([ "a.bar", "b.baz" ], $i->getNameServers());
    }

    public function testGetNameServersDefault()
    {
        $i = self::createInfo();
        self::assertSame([], $i->getNameServers());
    }


    public function testGetCreationDate()
    {
        $i = self::createInfo([ "creationDate" => 123456789 ]);
        self::assertEquals(123456789, $i->getCreationDate());
    }

    public function testGetCreationDateDefault()
    {
        $i = self::createInfo();
        self::assertSame(0, $i->getCreationDate());
    }


    public function testGetExpirationDate()
    {
        $i = self::createInfo([ "expirationDate" => 123456789 ]);
        self::assertEquals(123456789, $i->getExpirationDate());
    }

    public function testGetExpirationDateDefault()
    {
        $i = self::createInfo();
        self::assertSame(0, $i->getExpirationDate());
    }


    public function testGetStates()
    {
        $i = self::createInfo([ "states" => [ "abc", "def", "ghi" ] ]);
        self::assertEquals([ "abc", "def", "ghi" ], $i->getStates());
    }

    public function testGetStatesDefault()
    {
        $i = self::createInfo();
        self::assertSame([], $i->getStates());
    }


    public function testGetOwner()
    {
        $i = self::createInfo([ "owner" => "Some Company" ]);
        self::assertEquals("Some Company", $i->getOwner());
    }

    public function testGetOwnerDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->getOwner());
    }


    public function testGetRegistrar()
    {
        $i = self::createInfo([ "registrar" => "Some Registrar" ]);
        self::assertEquals("Some Registrar", $i->getRegistrar());
    }

    public function testGetRegistrarDefault()
    {
        $i = self::createInfo();
        self::assertSame("", $i->getRegistrar());
    }
}
