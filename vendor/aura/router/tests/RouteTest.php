<?php
namespace Aura\Router;

use Zend\Diactoros\ServerRequestFactory;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function test__get()
    {
        $route = new Route();
        $route->path('/foo/bar/baz');
        $this->assertSame('/foo/bar/baz', $route->path);
    }

    public function testImmutablePath()
    {
        $route = new Route();
        $route->path('/foo');
        $this->setExpectedException(
            'Aura\Router\Exception\ImmutableProperty',
            'Aura\Router\Route::$path'
        );
        $route->path('/bar');
    }

    public function testImmutablePathPrefix()
    {
        $route = new Route();
        $route->path('/foo');
        $this->setExpectedException(
            'Aura\Router\Exception\ImmutableProperty',
            'Aura\Router\Route::$pathPrefix'
        );
        $route->pathPrefix('/bar');
    }

    public function testImmutableName()
    {
        $route = new Route();
        $route->name('/foo');
        $this->setExpectedException(
            'Aura\Router\Exception\ImmutableProperty',
            'Aura\Router\Route::$name'
        );
        $route->name('/bar');
    }

    public function testImmutableNamePrefix()
    {
        $route = new Route();
        $route->name('/foo');
        $this->setExpectedException(
            'Aura\Router\Exception\ImmutableProperty',
            'Aura\Router\Route::$namePrefix'
        );
        $route->namePrefix('/bar');
    }

    public function testAuth()
    {
        $route = new Route();
        $route->auth(true);
        $this->assertTrue($route->auth);
    }

    public function testSpecial()
    {
        $route = new Route();
        $callable = ['StaticObject', 'method'];
        $route->special($callable);
        $this->assertSame($callable, $route->special);
    }
}
