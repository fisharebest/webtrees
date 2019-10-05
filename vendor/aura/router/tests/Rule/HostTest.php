<?php
namespace Aura\Router\Rule;

class HostTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Host();
    }

    public function testIsMatchOnStaticHost()
    {
        $proto = $this->newRoute('/foo/bar/baz')->host('foo.example.com');

        // right host
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_HOST' => 'foo.example.com']);
        $this->assertIsMatch($request, $route);

        // wrong host
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_HOST' => 'bar.example.com']);
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsMatchOnDynamicHost()
    {
        $proto = $this->newRoute('/foo/bar/baz')
            ->host('({subdomain}?.)?{domain}.com')
            ->tokens(['domain' => '.*']);

        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_HOST' => 'foo.example.com']);
        $this->assertIsMatch($request, $route);
        $this->assertEquals(['subdomain' => 'foo', 'domain' => 'example'], $route->attributes);

        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_HOST' => 'bar.example.com']);
        $this->assertIsMatch($request, $route);
        $this->assertEquals(['subdomain' => 'bar', 'domain' => 'example'], $route->attributes);

        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_HOST' => 'example.com']);
        $this->assertIsMatch($request, $route);
        $this->assertEquals(['subdomain' => null, 'domain' => 'example'], $route->attributes);
    }
}
