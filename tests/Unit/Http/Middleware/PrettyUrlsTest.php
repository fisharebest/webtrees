<?php

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit\Http\Middleware;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Middleware\PrettyUrls;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(PrettyUrls::class)]
class PrettyUrlsTest extends TestCase
{
    public function testRouteParameterIsConvertedToPrettyUrl(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo/index.php?route=/foo/bar');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (
                    ServerRequestInterface $request
                ) {
                    $this->assertSame('/foo/bar', $request->getUri()->getPath());

                    return true;
                })
            )
            ->willReturn($this->createStub(ResponseInterface::class));

        $middleware = new PrettyUrls();
        $middleware->process($request, $handler);
    }

    public function testRouteParameterIsRedirectedToPrettyUrl(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo/index.php?route=/foo/bar');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri)->withAttribute('rewrite_urls', '1');
        $handler         = $this->createStub(RequestHandlerInterface::class);
        $middleware      = new PrettyUrls();
        $response        = $middleware->process($request, $handler);

        $this->assertSame(HttpStatusCode::PermanentRedirect->value, $response->getStatusCode());
        $this->assertSame('https://example.com/foo/bar', $response->getHeaderLine('Location'));
    }

    public function testRouteParameterShouldBeFirst(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo/index.php?foo=bar&route=/bar/baz');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createStub(RequestHandlerInterface::class);
        $middleware      = new PrettyUrls();

        $this->expectException(HttpBadRequestException::class);

        $middleware->process($request, $handler);
    }

    public function testTrailingIndexPhpIsRemoved(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo/index.php');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (
                    ServerRequestInterface $request
                ) {
                    $this->assertSame('/foo/', $request->getUri()->getPath());

                    return true;
                })
            )
            ->willReturn($this->createStub(ResponseInterface::class));

        $middleware = new PrettyUrls();
        $middleware->process($request, $handler);
    }

    public function testMissingRouteParameterIsPassedThrough(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (
                    ServerRequestInterface $request
                ) {
                    $this->assertSame('/foo', $request->getUri()->getPath());

                    return true;
                })
            )
            ->willReturn($this->createStub(ResponseInterface::class));

        $middleware = new PrettyUrls();
        $middleware->process($request, $handler);
    }

    public function testInvalidRouteParameterIsRemoved(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo?route%5B%5D=1&route%5B%5D=2');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (
                    ServerRequestInterface $request
                ) {
                    $this->assertSame('https://example.com/foo', $request->getUri()->__toString());

                    return true;
                })
            )
            ->willReturn($this->createStub(ResponseInterface::class));

        $middleware = new PrettyUrls();
        $middleware->process($request, $handler);
    }

    public function testRequestUnalteredWhenNoRouteParameter(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/foo/bar');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (
                    ServerRequestInterface $r
                ) use ($request) {
                    $this->assertSame($request, $r);

                    return true;
                })
            )
            ->willReturn($this->createStub(ResponseInterface::class));

        $middleware = new PrettyUrls();
        $middleware->process($request, $handler);
    }
}
