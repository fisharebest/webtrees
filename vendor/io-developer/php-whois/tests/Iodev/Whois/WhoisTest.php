<?php

namespace Iodev\Whois;

use Iodev\Whois\Loaders\FakeSocketLoader;
use Iodev\Whois\Loaders\SocketLoader;

class WhoisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Whois */
    private $whois;

    /** @var FakeSocketLoader */
    private $loader;

    /**
     * @return Whois
     */
    private function getWhois()
    {
        $this->loader = new FakeSocketLoader();
        $this->whois = new Whois($this->loader);
        return $this->whois;
    }

    public function testConstruct()
    {
        new Whois(new SocketLoader());
    }

    public function testGetLoader()
    {
        $w = $this->getWhois();
        self::assertSame($this->loader, $w->getLoader());
    }
}
