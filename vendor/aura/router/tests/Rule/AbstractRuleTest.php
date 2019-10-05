<?php
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Zend\Diactoros\ServerRequestFactory;

abstract class AbstractRuleTest extends \PHPUnit_Framework_TestCase
{
    protected $rule;

    protected function newRequest($path, array $server = [], array $cookie = [])
    {
        $server['REQUEST_URI'] = $path;
        $server = array_merge($_SERVER, $server);
        $cookie = array_merge($_COOKIE, $cookie);
        return ServerRequestFactory::fromGlobals(
            $server,
            [],
            [],
            $cookie
        );
    }

    protected function newRoute($path)
    {
        $route = new Route();
        return $route->path($path);
    }

    protected function assertIsMatch($request, $route)
    {
        $this->assertTrue($this->rule->__invoke($request, $route));
    }

    protected function assertIsNotMatch($request, $route)
    {
        $this->assertFalse($this->rule->__invoke($request, $route));
    }
}
