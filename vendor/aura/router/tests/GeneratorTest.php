<?php
namespace Aura\Router;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class GeneratorTest extends TestCase
{
    /**
     * @var Map
     */
    protected $map;

    /**
     * @var Generator
     */
    protected $generator;

    protected function set_up()
    {
        parent::set_up();
        $container = new RouterContainer();
        $this->map = $container->getMap();
        $this->generator = $container->getGenerator();
    }

    protected function setProperties($basepath = null)
    {
        $container = new RouterContainer($basepath);
        $this->map = $container->getMap();
        $this->generator = $container->getGenerator();
    }

    /**
     * This test should not throw exception for the urlencode on closure
     */
    public function testGenerateControllerAsClosureIssue19()
    {
        $this->map->route('issue19', '/blog/{id}/edit')
            ->defaults([
                'controller' => function ($attributes) {
                    $id = (int) $attributes['id'];
                    return "Hello World";
                },
                'action' => 'read',
                'format' => '.html',
            ])
            ->tokens([
                'id' => '([0-9]+)',
            ]);

        $url = $this->generator->generate('issue19', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/blog/42/edit', $url);
    }

    /**
     * This test should not throw exception for the urlencode on closure
     */
    public function testGenerateControllerAsClosureIssue19WithFastRouteFormat()
    {
        $this->map->route('issue19', '/blog/{id:\d+}/edit')
            ->defaults([
                'controller' => function ($attributes) {
                    $id = (int) $attributes['id'];
                    return "Hello World";
                },
                'action' => 'read',
                'format' => '.html',
            ]);

        $url = $this->generator->generate('issue19', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/blog/42/edit', $url);
    }

    public function testGenerate()
    {
        $this->map->route('test', '/blog/{id}/edit')
            ->tokens([
                'id' => '([0-9]+)',
            ]);

        $url = $this->generator->generate('test', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/blog/42/edit', $url);
    }

    public function testGenerateWithFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:([0-9]+)}/edit');

        $url = $this->generator->generate('test', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/blog/42/edit', $url);
    }

    public function testGenerateWithFastRouteFormatAndOtherQuantifier()
    {
        $this->map->route('test', '/blog/{id:([0-9]{1,})}/edit');

        $url = $this->generator->generate('test', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/blog/42/edit', $url);
    }

    public function testGenerateMatchingException()
    {
        $this->map->route('test', '/blog/{id}/edit')
            ->tokens([
                'id' => '([0-9]+)',
            ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parameter value for [id] did not match the regex `([0-9]+)`');
        $url = $this->generator->generate('test', ['id' => '4 2', 'foo' => 'bar']);
    }

    public function testGenerateMatchingExceptionWithFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:([0-9]+)}/edit');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parameter value for [id] did not match the regex `([0-9]+)`');
        $url = $this->generator->generate('test', ['id' => '4 2', 'foo' => 'bar']);
    }

    public function testGenerateMissing()
    {
        $this->expectException('Aura\Router\Exception\RouteNotFound');
        $this->generator->generate('no-such-route');
    }

    public function testGenerateWithWildcard()
    {
        $this->map->route('test', '/blog/{id}')
            ->tokens([
                'id' => '([0-9]+)',
            ])
            ->wildcard('other');

        $url = $this->generator->generate('test', [
            'id' => 42,
            'foo' => 'bar',
            'other' => [
                'dib' => 'zim',
                'irk' => 'gir',
            ],
        ]);

        $this->assertEquals('/blog/42/zim/gir', $url);
    }

    public function testGenerateWithWildcardAndWithFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:([0-9]+)}')
            ->wildcard('other');

        $url = $this->generator->generate('test', [
            'id' => 42,
            'foo' => 'bar',
            'other' => [
                'dib' => 'zim',
                'irk' => 'gir',
            ],
        ]);

        $this->assertEquals('/blog/42/zim/gir', $url);
    }

    public function testGenerateWithOptional()
    {
        $this->map->route('test', '/archive/{category}{/year,month,day}');

        // some
        $url = $this->generator->generate('test', [
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
        ]);
        $this->assertEquals('/archive/foo/1979/11', $url);

        // all
        $url = $this->generator->generate('test', [
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
            'day' => '07',
        ]);
        $this->assertEquals('/archive/foo/1979/11/07', $url);
    }

    public function testGenerateWithOptionalAndFastRouteFormatInOptional()
    {
        $this->map->route('test', '/archive/{category}{/year:\d{4},month:\d{2},day:\d{2}}');

        // some
        $url = $this->generator->generate('test', [
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
        ]);
        $this->assertEquals('/archive/foo/1979/11', $url);

        // all
        $url = $this->generator->generate('test', [
            'category' => 'foo',
            'year' => '1979',
            'month' => '11',
            'day' => '07',
        ]);
        $this->assertEquals('/archive/foo/1979/11/07', $url);
    }

    public function testGenerateOnFullUri()
    {
        $this->map->route('test', 'http://google.com/?q={q}', ['action' => 'google-search'])
            ->isRoutable(false);

        $actual = $this->generator->generate('test', ['q' => "what's up doc?"]);
        $expect = "http://google.com/?q=what%27s%20up%20doc%3F";
        $this->assertSame($expect, $actual);
    }

    public function testGenerateOnFullUriWithFastRouteFormat()
    {
        $this->map->route('test', 'http://google.com/?q={q:[^/]*}', ['action' => 'google-search'])
            ->isRoutable(false);

        $actual = $this->generator->generate('test', ['q' => "what's up doc?"]);
        $expect = "http://google.com/?q=what%27s%20up%20doc%3F";
        $this->assertSame($expect, $actual);
    }

    public function testGenerateRFC3986()
    {
        $this->map->route('test', '/path/{string}', ['action' => 'rfc3986'])
            ->isRoutable(false);

        // examples taken from http://php.net/manual/en/function.rawurlencode.php
        $actual = $this->generator->generate('test', ['string' => 'foo @+%/']);
        $expect = '/path/foo%20%40%2B%25%2F';
        $this->assertSame($actual, $expect);

        $actual = $this->generator->generate('test', ['string' => 'sales and marketing/Miami']);
        $expect = '/path/sales%20and%20marketing%2FMiami';
        $this->assertSame($actual, $expect);
    }

    public function testGenerateRaw()
    {
        $this->map->route('test', '/{vendor}/{package}/{file}');
        $data = [
            'vendor' => 'vendor+name',
            'package' => 'package+name',
            'file' => 'foo/bar/baz.jpg',
        ];
        $actual = $this->generator->generateRaw('test', $data);
        $expect = '/vendor+name/package+name/foo/bar/baz.jpg';
        $this->assertSame($actual, $expect);
    }

    public function testGenerateWithBasepath()
    {
        $this->setProperties('/path/to/sub/index.php');

        $this->map->route('test', '/blog/{id}/edit')
            ->tokens([
                'id' => '([0-9]+)',
            ]);

        $url = $this->generator->generate('test', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/path/to/sub/index.php/blog/42/edit', $url);
    }

    public function testGenerateWithBasepathAndFastRouteFormat()
    {
        $this->setProperties('/path/to/sub/index.php');

        $this->map->route('test', '/blog/{id:([0-9]+)}/edit');

        $url = $this->generator->generate('test', ['id' => 42, 'foo' => 'bar']);
        $this->assertEquals('/path/to/sub/index.php/blog/42/edit', $url);
    }

    public function testGenerateWithHost()
    {
        $this->map->route('test', '/blog/{id}/edit')
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('//bar.example.com/blog/42/edit', $url);
    }

    public function testGenerateWithHostAndFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:\d+}/edit')
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('//bar.example.com/blog/42/edit', $url);
    }

    public function testGenerateWithHttpHost()
    {
        $this->map->route('test', '/blog/{id}/edit')
            ->secure(false)
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('http://bar.example.com/blog/42/edit', $url);
    }

    public function testGenerateWithHttpHostAndFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:\d+}/edit')
            ->secure(false)
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('http://bar.example.com/blog/42/edit', $url);
    }

    public function testGenerateWithHttpsHost()
    {
        $this->map->route('test', '/blog/{id}/edit')
            ->secure(true)
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('https://bar.example.com/blog/42/edit', $url);
    }

    public function testGenerateWithHttpsHostAndFastRouteFormat()
    {
        $this->map->route('test', '/blog/{id:\d+}/edit')
            ->secure(true)
            ->host('{host}.example.com');

        $url = $this->generator->generate('test', ['id' => 42, 'host' => 'bar']);
        $this->assertEquals('https://bar.example.com/blog/42/edit', $url);
    }
}
