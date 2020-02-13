<?php

namespace Iodev\Whois\Modules\Tld;

use Iodev\Whois\Loaders\FakeSocketLoader;

class TldModuleServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $zone
     * @return TldServer
     */
    private static function createServer($zone)
    {
        return new TldServer($zone, "some.host.net", false, TldParser::create());
    }

    /** @var TldModule */
    private $mod;


    public function setUp()
    {
        $this->mod = new TldModule(new FakeSocketLoader());
    }

    public function tearDown()
    {
    }


    public function testAddServersReturnsSelf()
    {
        $res = $this->mod->addServers([ self::createServer(".abc") ]);
        self::assertSame($this->mod, $res, "Result must be self reference");
    }

    public function testMatchServersQuietEmpty()
    {
        $servers = $this->mod->matchServers("domain.com", true);
        self::assertTrue(is_array($servers), "Result must be Array");
        self::assertEquals(0, count($servers), "Count must be zero");
    }

    public function testMatchServersOne()
    {
        $s = self::createServer(".com");
        $this->mod->addServers([$s]);
        $servers = $this->mod->matchServers("domain.com");
        self::assertTrue(is_array($servers), "Result must be Array");
        self::assertEquals(1, count($servers), "Count must be 1");
        self::assertSame($servers[0], $s, "Wrong matched server");
    }

    public function testMatchServersSome()
    {
        $s = self::createServer(".com");
        $this->mod->addServers([
            self::createServer(".net"),
            self::createServer(".com"),
            self::createServer(".net"),
            self::createServer(".com"),
            self::createServer(".su"),
            $s,
            self::createServer(".com"),
            self::createServer(".gov"),
        ]);

        $servers = $this->mod->matchServers("domain.com");
        self::assertTrue(is_array($servers), "Result must be Array");
        self::assertEquals(4, count($servers), "Count of matched servers not equals");
        self::assertContains($s, $servers, "Server not matched");
    }

    public function testMatchServersQuietNoneInSome()
    {
        $this->mod->addServers([
            self::createServer(".net"),
            self::createServer(".com"),
            self::createServer(".net"),
            self::createServer(".com"),
            self::createServer(".su"),
            self::createServer(".com"),
            self::createServer(".gov"),
        ]);

        $servers = $this->mod->matchServers("domain.xyz", true);
        self::assertTrue(is_array($servers), "Result must be Array");
        self::assertEquals(0, count($servers), "Count of matched servers must be zaro");
    }

    public function testMatchServersCollisionLongest()
    {
        $this->mod->addServers([
            self::createServer(".com"),
            self::createServer(".bar.com"),
            self::createServer(".foo.bar.com"),
        ]);
        $servers = $this->mod->matchServers("domain.foo.bar.com");

        self::assertEquals(3, count($servers), "Count of matched servers not equals");
        self::assertEquals(".foo.bar.com", $servers[0]->getZone(), "Invalid matched zone");
        self::assertEquals(".bar.com", $servers[1]->getZone(), "Invalid matched zone");
        self::assertEquals(".com", $servers[2]->getZone(), "Invalid matched zone");
    }

    public function testMatchServersCollisionMiddle()
    {
        $this->mod->addServers([
            self::createServer(".com"),
            self::createServer(".bar.com"),
            self::createServer(".foo.bar.com"),
        ]);
        $servers = $this->mod->matchServers("domain.bar.com");

        self::assertEquals(2, count($servers), "Count of matched servers not equals");
        self::assertEquals(".bar.com", $servers[0]->getZone(), "Invalid matched zone");
        self::assertEquals(".com", $servers[1]->getZone(), "Invalid matched zone");
    }

    public function testMatchServersCollisionShorter()
    {
        $this->mod->addServers([
            self::createServer(".com"),
            self::createServer(".bar.com"),
            self::createServer(".foo.bar.com"),
        ]);
        $servers = $this->mod->matchServers("domain.com");

        self::assertEquals(1, count($servers), "Count of matched servers not equals");
        self::assertEquals(".com", $servers[0]->getZone(), "Invalid matched zone");
    }

    public function testMatchServersCollisiondWildcard()
    {
        $this->mod->addServers([
            self::createServer(".com"),
            self::createServer(".*.com"),
        ]);
        $servers = $this->mod->matchServers("domain.com");

        self::assertEquals(1, count($servers), "Count of matched servers not equals");
        self::assertEquals(".com", $servers[0]->getZone(), "Invalid matched zone");
    }

    public function testMatchServersCollisionMissingZone()
    {
        $this->mod->addServers([
            self::createServer(".com"),
            self::createServer(".bar.com"),
        ]);
        $servers = $this->mod->matchServers("domain.foo.bar.com");

        self::assertEquals(2, count($servers), "Count of matched servers not equals");
        self::assertEquals(".bar.com", $servers[0]->getZone(), "Invalid matched zone");
        self::assertEquals(".com", $servers[1]->getZone(), "Invalid matched zone");
    }

    public function testMatchServersCollisionFallback()
    {
        $this->mod->addServers([
            self::createServer(".*"),
            self::createServer(".*.foo"),
            self::createServer(".*.com"),
            self::createServer(".bar.*"),
            self::createServer(".foo.*.*"),
            self::createServer(".bar.com"),
        ]);
        $servers = $this->mod->matchServers("domain.foo.bar.com");

        self::assertEquals(5, count($servers), "Count of matched servers not equals");
        self::assertEquals(".foo.*.*", $servers[0]->getZone(), "Invalid matched zone");
        self::assertEquals(".bar.com", $servers[1]->getZone(), "Invalid matched zone");
        self::assertEquals(".bar.*", $servers[2]->getZone(), "Invalid matched zone");
        self::assertEquals(".*.com", $servers[3]->getZone(), "Invalid matched zone");
        self::assertEquals(".*", $servers[4]->getZone(), "Invalid matched zone");
    }
}