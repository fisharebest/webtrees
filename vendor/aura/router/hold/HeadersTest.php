<?php
namespace Aura\Router\Rule;

class HeadersTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Headers();
    }

    public function test()
    {
        $proto = $this->newRoute('/foo/bar/baz')
            ->setHeaders([
                'X-Foo' => '/fooval/',
            ]);

        /* single-value regex */

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'fooval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['X-Foo'], 'fooval');

        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'barval']);
        $this->assertIsNotMatch($request, $route);

        /* multi-value regex */
        $proto = $this->newRoute('/foo/bar/baz')
            ->setHeaders([
                'X-Foo' => '/fooval|barval/',
            ]);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'fooval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['X-Foo'], 'fooval');

        // also correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'barval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['X-Foo'], 'barval');

        // incorrect
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'bazval']);
        $this->assertIsNotMatch($request, $route);

        // no header
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', []);
        $this->assertIsNotMatch($request, $route);
    }
}
