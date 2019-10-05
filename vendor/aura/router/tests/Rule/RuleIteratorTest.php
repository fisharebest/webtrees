<?php
namespace Aura\Router\Rule;

class RuleIteratorTest extends \PHPUnit_Framework_TestCase
{
    protected $ruleIterator;

    protected function setUp()
    {
        parent::setUp();
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

        $this->setExpectedException(
            'Aura\Router\Exception\UnexpectedValue',
            'string'
        );

        $this->ruleIterator->current();
    }

    public function testUnexpectedValueObject()
    {
        $this->ruleIterator->set([
            function () { return (object) []; }
        ]);

        $this->setExpectedException(
            'Aura\Router\Exception\UnexpectedValue',
            'object of type stdClass'
        );

        $this->ruleIterator->current();
    }
}
