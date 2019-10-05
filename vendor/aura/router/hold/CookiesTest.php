<?php
namespace Aura\Router\Rule;

class CookiesTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Cookies();
    }

    public function test()
    {
        $proto = $this->newRoute('/foo/bar/baz')
            ->setCookies([
                'foo' => '/fooval/',
            ]);

        /* single-value regex */

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', [], ['foo' => 'fooval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['foo'], 'fooval');

        // incorrect
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', [], ['foo' => 'barval']);
        $this->assertIsNotMatch($request, $route);

        /* multi-value regex */

        $proto = $this->newRoute('/foo/bar/baz')
            ->setCookies([
                'foo' => '/fooval|barval/',
            ]);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', [], ['foo' => 'fooval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['foo'], 'fooval');

        // also correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', [], ['foo' => 'barval']);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['foo'], 'barval');

        // incorrect
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', [], ['foo' => 'bazval']);
        $this->assertIsNotMatch($request, $route);

        // no cookie
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz');
        $this->assertIsNotMatch($request, $route);
    }
}
