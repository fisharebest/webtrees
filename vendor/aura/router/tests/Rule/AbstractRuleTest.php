<?php
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;

abstract class AbstractRuleTest extends TestCase
{
    protected $rule;

    protected function newRequest($path, array $server = [], array $cookie = [])
    {
        $server['REQUEST_URI'] = $path;
        $server = array_merge($_SERVER, $server);
        $cookie = array_merge($_COOKIE, $cookie);

        $method = isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET';

        $uri = $this->getUriFromServer($server);
        $headers = $this->getHeaders($server);

        return new ServerRequest($method, $uri, $headers, null, '1.1', $server);
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

    // Used inside guzzle/psr7
    protected function getHeaders($server)
    {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($server as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($server[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        return $headers;
    }

    protected function getUriFromServer($server)
    {
        $uri = new Uri('');

        $uri = $uri->withScheme(!empty($server['HTTPS']) && $server['HTTPS'] !== 'off' ? 'https' : 'http');

        $hasPort = false;
        if (isset($server['HTTP_HOST'])) {
            list($host, $port) = self::extractHostAndPortFromAuthority($server['HTTP_HOST']);
            if ($host !== null) {
                $uri = $uri->withHost($host);
            }

            if ($port !== null) {
                $hasPort = true;
                $uri = $uri->withPort($port);
            }
        } elseif (isset($server['SERVER_NAME'])) {
            $uri = $uri->withHost($server['SERVER_NAME']);
        } elseif (isset($server['SERVER_ADDR'])) {
            $uri = $uri->withHost($server['SERVER_ADDR']);
        }

        if (!$hasPort && isset($server['SERVER_PORT'])) {
            $uri = $uri->withPort($server['SERVER_PORT']);
        }

        $hasQuery = false;
        if (isset($server['REQUEST_URI'])) {
            $requestUriParts = explode('?', $server['REQUEST_URI'], 2);
            $uri = $uri->withPath($requestUriParts[0]);
            if (isset($requestUriParts[1])) {
                $hasQuery = true;
                $uri = $uri->withQuery($requestUriParts[1]);
            }
        }

        if (!$hasQuery && isset($server['QUERY_STRING'])) {
            $uri = $uri->withQuery($server['QUERY_STRING']);
        }

        return $uri;
    }


    private static function extractHostAndPortFromAuthority($authority)
    {
        $uri = 'http://' . $authority;
        $parts = parse_url($uri);
        if (false === $parts) {
            return [null, null];
        }

        $host = isset($parts['host']) ? $parts['host'] : null;
        $port = isset($parts['port']) ? $parts['port'] : null;

        return [$host, $port];
    }
}
