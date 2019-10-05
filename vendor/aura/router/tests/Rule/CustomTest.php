<?php
namespace Aura\Router\Rule;

class CustomTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new FakeCustom();
    }

    public function test()
    {
        $request = $this->newRequest('/foo/bar/baz');

        $route = $this->newRoute('/foo/bar/baz')
            ->extras([
                'aura/router:fake' => true
            ]);
        $this->assertIsMatch($request, $route);
        $this->assertSame($route->attributes['aura/router:fake'], 'fake');

        $route = $this->newRoute('/foo/bar/baz');
        $this->assertIsNotMatch($request, $route);
    }
}
