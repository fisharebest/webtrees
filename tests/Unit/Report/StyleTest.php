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

use LogicException;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Report\Style;

#[CoversClass(Style::class)]
class StyleTest extends TestCase
{
    public function testConstructorAssignsProperties(): void
    {
        $style = new Style('header', 'bi', 14.0);

        self::assertSame('header', $style->name);
        self::assertSame('bi', $style->style);
        self::assertSame(14.0, $style->size);
    }

    public function testConstructorRejectsUppercaseStyleFlags(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid style flags "B". Use only lowercase b, i, u, and d.');

        new Style('header', 'B', 14.0);
    }

    public function testConstructorAcceptsLowercaseStyleFlags(): void
    {
        $style = new Style('header', 'b', 14.0);

        self::assertSame('b', $style->style);
    }

    public function testConstructorRejectsUnknownStyleFlags(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid style flags "X". Use only lowercase b, i, u, and d.');

        new Style('header', 'X', 14.0);
    }

    public function testFromXmlAttributesCreatesStyle(): void
    {
        $style = Style::fromXmlAttributes([
            'name'  => 'header',
            'style' => 'bi',
            'size'  => '14',
        ]);

        self::assertSame('header', $style->name);
        self::assertSame('bi', $style->style);
        self::assertSame(14.0, $style->size);
    }

    public function testFromXmlAttributesRejectsUppercaseStyleFlags(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid style flags "BI". Use only lowercase b, i, u, and d.');

        Style::fromXmlAttributes([
            'name'  => 'header',
            'style' => 'BI',
            'size'  => '14',
        ]);
    }
}
