<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Fisharebest\Webtrees\Report\PlaceholderExpander;
use Fisharebest\Webtrees\Report\VariableTable;

#[CoversClass(PlaceholderExpander::class)]
class PlaceholderExpanderTest extends TestCase
{
    private function createExpander(array $vars = []): PlaceholderExpander
    {
        $tree = self::createStub(Tree::class);

        return new PlaceholderExpander(new VariableTable($vars), $tree);
    }


    // --- resolveSetVarValue tests ---

    public function testResolveAtId(): void
    {
        $expander = $this->createExpander();
        $gedrec   = "0 @I123@ INDI\n1 NAME John /Doe/";

        $result = $expander->resolveSetVarValue('@ID', $gedrec, '', '', 1);

        self::assertSame('I123', $result);
    }

    public function testResolveAtFact(): void
    {
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue('@fact', '', 'BIRT', '', 1);

        self::assertSame('BIRT', $result);
    }

    public function testResolveAtDesc(): void
    {
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue('@desc', '', '', 'some description', 1);

        self::assertSame('some description', $result);
    }

    public function testResolveAtGeneration(): void
    {
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue('@generation', '', '', '', 5);

        self::assertSame('5', $result);
    }

    public function testResolveAtTagFromGedrec(): void
    {
        $expander = $this->createExpander();
        $gedrec   = "0 @I1@ INDI\n1 SEX M";

        $result = $expander->resolveSetVarValue('@SEX', $gedrec, '', '', 1);

        self::assertSame('M', $result);
    }

    public function testResolveVariableSubstitution(): void
    {
        $expander = $this->createExpander(['count' => '7', 'total' => '10']);

        // The "/" is not arithmetic (it's part of "7 / 10" with spaces around a slash)
        // but the arithmetic regex matches "7" and "10". Use a non-arithmetic example instead.
        $result = $expander->resolveSetVarValue('$count items of $total', "0 @I1@ INDI", '', '', 1);

        self::assertSame('7 items of 10', $result);
    }

    public function testResolveArithmetic(): void
    {
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue('3 + 4', '', '', '', 1);

        self::assertSame('7', $result);
    }

    public function testResolveI18nNumber(): void
    {
        I18N::init('en-US', true);
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue('I18N::number(42)', '', '', '', 1);

        self::assertSame('42', $result);
    }

    public function testResolveI18nTranslate(): void
    {
        I18N::init('en-US', true);
        $expander = $this->createExpander();

        $result = $expander->resolveSetVarValue("I18N::translate('Total')", '', '', '', 1);

        self::assertSame('Total', $result);
    }

    public function testUnresolvedAtReferenceBecomesEmpty(): void
    {
        $expander = $this->createExpander();

        // An @-reference that cannot be resolved is cleared
        $result = $expander->resolveSetVarValue('@UNKNOWN', "0 @I1@ INDI", '', '', 1);

        self::assertSame('', $result);
    }

    // --- substituteVars tests ---

    public function testSubstituteVarsReplaces(): void
    {
        $expander = $this->createExpander(['name' => 'John']);

        $result = $expander->substituteVars('Hello $name', false);

        self::assertSame('Hello John', $result);
    }

    public function testSubstituteVarsQuoted(): void
    {
        $expander = $this->createExpander(['name' => "O'Brien"]);

        $result = $expander->substituteVars('$name == "test"', true);

        self::assertSame("'O\\'Brien' == \"test\"", $result);
    }

    public function testSubstituteVarsThrowsOnUndefined(): void
    {
        $expander = $this->createExpander();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Undefined variable $missing');

        $expander->substituteVars('$missing', false);
    }

    // --- applyI18nFunctions tests ---

    public function testApplyI18nTranslateContext(): void
    {
        I18N::init('en-US', true);
        $expander = $this->createExpander();

        $result = $expander->applyI18nFunctions("I18N::translateContext('context', 'text')");

        self::assertSame('text', $result);
    }

    public function testApplyI18nPassesThrough(): void
    {
        $expander = $this->createExpander();

        $result = $expander->applyI18nFunctions('plain text');

        self::assertSame('plain text', $result);
    }

    // --- evaluateArithmetic tests ---

    public function testEvaluateArithmeticAddition(): void
    {
        $expander = $this->createExpander();

        self::assertSame('10', $expander->evaluateArithmetic('7 + 3'));
    }

    public function testEvaluateArithmeticMultiplication(): void
    {
        $expander = $this->createExpander();

        self::assertSame('12', $expander->evaluateArithmetic('3 * 4'));
    }

    public function testEvaluateArithmeticNoOp(): void
    {
        $expander = $this->createExpander();

        self::assertSame('hello', $expander->evaluateArithmetic('hello'));
    }

    // --- evaluateCondition tests ---

    public function testEvaluateConditionTrue(): void
    {
        $expander = $this->createExpander(['x' => '5']);

        $result = $expander->evaluateCondition('$x == "5"', '', '', '', 1);

        self::assertTrue($result);
    }

    public function testEvaluateConditionFalse(): void
    {
        $expander = $this->createExpander(['x' => '5']);

        $result = $expander->evaluateCondition('$x == "3"', '', '', '', 1);

        self::assertFalse($result);
    }

    public function testEvaluateConditionWithAtId(): void
    {
        $expander = $this->createExpander();
        $gedrec   = "0 @I99@ INDI\n1 NAME Test /Person/";
        $gedrec   = "0 @I99@ INDI\n1 NAME Test /Person/";

        $result = $expander->evaluateCondition('@ID == "I99"', $gedrec, '', '', 1);

        self::assertTrue($result);
    }

    public function testEvaluateConditionWithLtGt(): void
    {
        $expander = $this->createExpander(['x' => '10']);

        $result = $expander->evaluateCondition('$x GT 5', '', '', '', 1);

        self::assertTrue($result);
    }
}
