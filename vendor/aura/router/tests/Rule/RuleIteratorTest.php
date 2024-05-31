<?php
namespace Aura\Router\Rule;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class RuleIteratorTest extends TestCase
{
    protected $ruleIterator;

    protected function set_up()
    {
        parent::set_up();
        $this->ruleIterator = new RuleIterator();
    }

    public function test()
    {
        $this->ruleIterator->set([
            new Secure(),
            function () { return new FakeCustom(); },
        ]);
        $this->ruleIterator->prepend(new Allows());

        $expect = [
            'Aura\Router\Rule\Allows',
            'Aura\Router\Rule\Secure',
            'Aura\Router\Rule\FakeCustom',
        ];

        // first traversal
        $actual = [];
        foreach ($this->ruleIterator as $key => $rule) {
            $actual[] = get_class($rule);
        }
        $this->assertSame($actual, $expect);

        // subsequent traversal
        $actual = [];
        foreach ($this->ruleIterator as $key => $rule) {
            $actual[] = get_class($rule);
        }
        $this->assertSame($actual, $expect);
    }

    public function testUnexpectedValue()
    {
        $this->ruleIterator->set([
            function () { return 'string'; }
        ]);

        $this->expectException(
            'Aura\Router\Exception\UnexpectedValue'
        );

        $this->ruleIterator->current();
    }

    public function testUnexpectedValueObject()
    {
        $this->ruleIterator->set([
            function () { return (object) []; }
        ]);

        $this->expectException(
            'Aura\Router\Exception\UnexpectedValue'
        );

        $this->ruleIterator->current();
    }
}
