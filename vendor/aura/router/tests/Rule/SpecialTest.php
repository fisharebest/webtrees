<?php
namespace Aura\Router\Rule;

class SpecialTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Special();
    }

    public function testIsSpecialMatch()
    {
        $proto = $this->newRoute('/foo/bar/baz')
            ->special(function ($request, $route) {
                return $request->getHeader('x-foo')[0] == 'bar';
            });

        // match
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'bar']);
        $this->assertIsMatch($request, $route);

        // no match
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTP_X_FOO' => 'baz']);
        $this->assertIsNotMatch($request, $route);
    }
}
