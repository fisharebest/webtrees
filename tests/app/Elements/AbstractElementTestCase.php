<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;


abstract class AbstractElementTestCase extends TestCase
{
    private const EVIL_VALUE = '<script>evil()</script>';
    private const TEST_VALUE = '01 JAN 1970';

    protected static ElementInterface $element;

    public function testCanonical(): void
    {
        self::assertSame('Foo bAr baZ', self::$element->canonical('Foo  bAr  baZ'));
        self::assertSame('Foo bAr baZ', self::$element->canonical("\t Foo\t bAr \tbaZ\t "));
        self::assertSame('Foo bAr baZ', self::$element->canonical("\nFoo \n\r bAr \r\n baZ\r"));
    }

    public function testEscapeAtSigns(): void
    {
        if (static::$element instanceof AbstractXrefElement) {
            self::assertSame('@X123@', static::$element->escape('@X123@'));
        } else {
            self::assertSame('@@X123@@', static::$element->escape('@X123@'));
        }
    }

    public function testXssInValue(): void
    {
        $tree    = $this->createMock(Tree::class);
        $html    = static::$element->value(self::EVIL_VALUE, $tree);
        $message = 'XSS vulnerability in value()';

        self::assertStringNotContainsStringIgnoringCase(self::EVIL_VALUE, $html, $message);
    }

    public function testXssInLabelValue(): void
    {
        $tree    = $this->createMock(Tree::class);
        $html    = static::$element->labelValue(self::EVIL_VALUE, $tree);
        $message = 'XSS vulnerability in labelValue()';

        self::assertStringNotContainsStringIgnoringCase(self::EVIL_VALUE, $html, $message);
    }

    public function testXssInEdit(): void
    {
        $tree    = $this->createMock(Tree::class);
        $html    = static::$element->edit('id', 'name', self::EVIL_VALUE, $tree);
        $message = 'XSS vulnerability in edit()';

        self::assertStringNotContainsStringIgnoringCase(self::EVIL_VALUE, $html, $message);
    }

    public function testValidHtmlInValue(): void
    {
        $tree = $this->createMock(Tree::class);
        $html = static::$element->value(self::TEST_VALUE, $tree);

        $this->validateHtml($html);
    }

    public function testValidHtmlInEdit(): void
    {
        $tree = $this->createMock(Tree::class);
        $html = static::$element->edit('id', 'name', self::TEST_VALUE, $tree);

        $this->validateHtml($html);
    }
}
