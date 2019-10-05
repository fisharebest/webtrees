<?php
namespace Aura\Router\Rule;

class PathTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Path();
    }

    public function testIsMatchOnStaticPath()
    {
        $proto = $this->newRoute('/foo/bar/baz');

        // right path
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz');
        $this->assertIsMatch($request, $route);

        // wrong path
        $route = clone $proto;
        $request = $this->newRequest('/zim/dib/gir');
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsMatchOnDynamicPath()
    {
        $route = $this->newRoute('/{controller}/{action}/{id}{format}')
            ->tokens([
                'controller' => '([a-zA-Z][a-zA-Z0-9_-]+)',
                'action' => '([a-zA-Z][a-zA-Z0-9_-]+)',
                'id' => '([0-9]+)',
                'format' => '(\.[^/]+)?',
            ]);

        $request = $this->newRequest('/foo/bar/42');

        $this->assertIsMatch($request, $route);

        $expect = [
            'controller' => 'foo',
            'action' => 'bar',
            'id' => '42',
            'format' => null,
        ];
        $this->assertEquals($expect, $route->attributes);
    }

    public function testIsMatchOnDefaultAndDefinedSubpatterns()
    {
        $route = $this->newRoute('/{controller}/{action}/{id}{format}')
            ->tokens([
                'action' => '(browse|read|edit|add|delete)',
                'id' => '(\d+)',
                'format' => '(\.[^/]+)?',
            ]);

        $request = $this->newRequest('/any-value/read/42');
        $this->assertIsMatch($request, $route);
        $expect = [
            'controller' => 'any-value',
            'action' => 'read',
            'id' => '42',
            'format' => null,
        ];
        $this->assertSame($expect, $route->attributes);
    }

    public function testIsMatchOnDefaultAndCustomSubpatterns()
    {
        $route = $this->newRoute('/assets/{file}\.{semver}{format}')
            ->tokens([
                'file' => '([\w\d\-\_]+)',
                'semver' => function ($semver, $route, $request) {
                    if ($semver === '1.3.1') {
                        return true;
                    }
                    return false;
                },
                'format' => '(\.[^/]+)',
            ]);

        $request = $this->newRequest('/assets/any-file.1.3.1.zip');
        $this->assertIsMatch($request, $route);
        $expect = [
            'file' => 'any-file',
            'semver' => '1.3.1',
            'format' => '.zip'
        ];
        $this->assertSame($expect, $route->attributes);
    }

    public function testIsMatchOnRFC3986Paths()
    {
        $route = $this->newRoute('/{controller}/{action}/{attribute1}/{attribute2}');

        // examples taken from http://php.net/manual/en/function.rawurlencode.php
        $request = $this->newRequest('/some-controller/some%20action/foo%20%40%2B%25%2F/sales%20and%20marketing%2FMiami');
        $this->assertIsMatch($request, $route);
        $expect = [
            'controller' => 'some-controller',
            'action' => 'some action',
            'attribute1' => 'foo @+%/',
            'attribute2' => 'sales and marketing/Miami',
        ];
        $this->assertEquals($expect, $route->attributes);
    }

    public function testIsMatchOnWildcard()
    {
        $proto = $this->newRoute('/foo/{zim}/')
            ->wildcard('wild');

        // right path with wildcard values
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz/dib');
        $this->assertIsMatch($request, $route);
        $this->assertSame('bar', $route->attributes['zim']);
        $this->assertSame(['baz', 'dib'], $route->attributes['wild']);

        // right path with trailing slash but no wildcard values
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/');
        $this->assertIsMatch($request, $route);
        $this->assertSame('bar', $route->attributes['zim']);
        $this->assertSame([], $route->attributes['wild']);

        // right path without trailing slash
        $route = clone $proto;
        $this->assertIsMatch($request, $route);
        $this->assertSame([], $route->attributes['wild']);

        // wrong path
        $route = clone $proto;
        $request = $this->newRequest('/zim/dib/gir');
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsMatchOnOptionalAttributes()
    {
        $route = $this->newRoute('/foo/{bar}{/baz,dib,zim}');

        // not enough attributes
        $request = $this->newRequest('/foo');
        $this->assertIsNotMatch($request, $route);

        // just enough attributes
        $request = $this->newRequest('/foo/bar');
        $this->assertIsMatch($request, $route);

        // optional attribute 1
        $request = $this->newRequest('/foo/bar/baz');
        $this->assertIsMatch($request, $route);

        // optional attribute 2
        $request = $this->newRequest('/foo/bar/baz/dib');
        $this->assertIsMatch($request, $route);

        // optional attribute 3
        $request = $this->newRequest('/foo/bar/baz/dib/zim');
        $this->assertIsMatch($request, $route);

        // too many attributes
        $request = $this->newRequest('/foo/bar/baz/dib/zim/gir');
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsMatchOnOnlyOptionalAttributes()
    {
        $route = $this->newRoute('{/foo,bar,baz}');

        $request = $this->newRequest('/');
        $this->assertIsMatch($request, $route);

        $request = $this->newRequest('/foo');
        $this->assertIsMatch($request, $route);

        $request = $this->newRequest('/foo/bar');
        $this->assertIsMatch($request, $route);

        $request = $this->newRequest('/foo/bar/baz');
        $this->assertIsMatch($request, $route);

        $request = $this->newRequest('/foo/bar/baz/dib');
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsMatchOnOptionalEndingSlash()
    {
        $route = $this->newRoute('/foo(/)?');
        $request = $this->newRequest('/foo');
        $this->assertIsMatch($request, $route);

        $request = $this->newRequest('/foo/');
        $this->assertIsMatch($request, $route);
    }

    public function testIsMatchWithBasepath()
    {
        $this->rule = new Path('/path/to/sub/index.php');
        $proto = $this->newRoute('/foo/bar/baz');

        // request has correct basepath
        $route = clone $proto;
        $request = $this->newRequest('/path/to/sub/index.php/foo/bar/baz');
        $this->assertIsMatch($request, $route);

        // request is missing basepath
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz');
        $this->assertIsNotMatch($request, $route);
    }
}
