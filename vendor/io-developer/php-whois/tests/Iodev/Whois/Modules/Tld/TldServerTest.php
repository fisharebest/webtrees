<?php

namespace Iodev\Whois\Modules\Tld;

class TldServerTest extends \PHPUnit_Framework_TestCase
{
    private static function getServerClass()
    {
        return '\Iodev\Whois\Modules\Tld\TldServer';
    }

    private static function getParser()
    {
        return TldParser::createByClass(self::getParserClass());
    }

    private static function getParserClass()
    {
        return '\Iodev\Whois\Modules\Tld\Parsers\TestCommonParser';
    }


    public function testConstructValid()
    {
        new TldServer(".abc", "some.host.com", false, self::getParser());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructEmptyZone()
    {
        new TldServer("", "some.host.com", false, self::getParser());
    }

    /**
    * @expectedException \InvalidArgumentException
    */
    public function testConstructEmptyHost()
    {
        new TldServer(".abc", "", false, self::getParser());
    }

    public function testGetZone()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertEquals(".abc", $s->getZone());
    }

    public function testGetHost()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertEquals("some.host.com", $s->getHost());
    }

    public function testIsCentralizedTrue()
    {
        $s = new TldServer(".abc", "some.host.com", true, self::getParser());
        self::assertTrue($s->isCentralized());

        $s = new TldServer(".abc", "some.host.com", 1, self::getParser());
        self::assertTrue($s->isCentralized());
    }

    public function testIsCentralizedFalse()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertFalse($s->isCentralized());

        $s = new TldServer(".abc", "some.host.com", 0, self::getParser());
        self::assertFalse($s->isCentralized());
    }

    public function testGetParserViaInstance()
    {
        $p = self::getParser();
        $s = new TldServer(".abc", "some.host.com", false, $p);
        self::assertSame($p, $s->getParser());
    }

    public function testIsDomainZoneValid()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertTrue($s->isDomainZone("some.abc"));
    }

    public function testIsDomainZoneValidComplex()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertTrue($s->isDomainZone("some.foo.bar.abc"));
    }

    public function testIsDomainZoneInvalid()
    {
        $s = new TldServer(".abc", "some.host.com", false, self::getParser());
        self::assertFalse($s->isDomainZone("some.com"));
    }

    public function testIsDomainZoneInvalidEnd()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser());
        self::assertFalse($s->isDomainZone("some.bar"));
    }

    public function testBuildDomainQueryDefault()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser());
        self::assertEquals("domain.com\r\n", $s->buildDomainQuery("domain.com"));
    }

    public function testBuildDomainQueryNull()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser(), null);
        self::assertEquals("site.com\r\n", $s->buildDomainQuery("site.com"));
    }

    public function testBuildDomainQueryEmpty()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser(), "");
        self::assertEquals("some.com\r\n", $s->buildDomainQuery("some.com"));
    }

    public function testBuildDomainQueryCustom()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser(), "prefix %s suffix\r\n");
        self::assertEquals("prefix domain.com suffix\r\n", $s->buildDomainQuery("domain.com"));
    }

    public function testBuildDomainQueryCustomNoParam()
    {
        $s = new TldServer(".foo.bar", "some.host.com", false, self::getParser(), "prefix suffix\r\n");
        self::assertEquals("prefix suffix\r\n", $s->buildDomainQuery("domain.com"));
    }

    public function testFromDataFullArgs()
    {
        $s = TldServer::fromData([
            "zone" => ".abc",
            "host" => "some.host",
            "centralized" => true,
            "parserClass" => self::getParserClass(),
            "queryFormat" => "prefix %s suffix\r\n",
        ]);

        self::assertEquals(".abc", $s->getZone());
        self::assertEquals("some.host", $s->getHost());
        self::assertTrue($s->isCentralized());
        self::assertInstanceOf(self::getParserClass(), $s->getParser());
        self::assertEquals("prefix %s suffix\r\n", $s->getQueryFormat());
    }

    public function testFromDataZoneHostOnly()
    {
        $s = TldServer::fromData([ "zone" => ".abc", "host" => "some.host" ], self::getParser());

        self::assertEquals(".abc", $s->getZone());
        self::assertEquals("some.host", $s->getHost());
        self::assertFalse($s->isCentralized());
        self::assertInstanceOf(self::getParserClass(), $s->getParser());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromDataMissingZone()
    {
        TldServer::fromData([ "host" => "some.host" ], self::getParser());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromDataMissingHost()
    {
        TldServer::fromData([ "zone" => ".abc" ], self::getParser());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromDataMissingAll()
    {
        TldServer::fromData([], self::getParser());
    }

    public function testFromDataListOne()
    {
        $s = TldServer::fromDataList(
            [ [ "zone" => ".abc", "host" => "some.host" ] ],
            self::getParser()
        );
        self::assertTrue(is_array($s), "Array expected");
        self::assertEquals(1, count($s));
        self::assertInstanceOf(self::getServerClass(), $s[0]);
        self::assertEquals(".abc", $s[0]->getZone());
        self::assertEquals("some.host", $s[0]->getHost());
        self::assertInstanceOf(self::getParserClass(), $s[0]->getParser());
    }

    public function testFromDataListTwo()
    {
        $s = TldServer::fromDataList([
                [ "zone" => ".abc", "host" => "some.host" ],
                [ "zone" => ".cde", "host" => "other.host", "centralized" => true, "queryFormat" => "prefix %s suffix\r\n" ],
            ],
            self::getParser()
        );
        self::assertTrue(is_array($s), "Array expected");
        self::assertEquals(2, count($s));

        self::assertInstanceOf(self::getServerClass(), $s[0]);
        self::assertEquals(".abc", $s[0]->getZone());
        self::assertEquals("some.host", $s[0]->getHost());
        self::assertFalse($s[0]->isCentralized());
        self::assertInstanceOf(self::getParserClass(), $s[0]->getParser());

        self::assertInstanceOf(self::getServerClass(), $s[1]);
        self::assertEquals(".cde", $s[1]->getZone());
        self::assertEquals("other.host", $s[1]->getHost());
        self::assertTrue($s[1]->isCentralized());
        self::assertInstanceOf(self::getParserClass(), $s[1]->getParser());
        self::assertEquals("prefix %s suffix\r\n", $s[1]->getQueryFormat());
    }
}
