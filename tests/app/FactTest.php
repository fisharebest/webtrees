<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees;

/**
 * Test harness for the class Fact
 *
 * @covers \Fisharebest\Webtrees\Fact
 */
class FactTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testAttribute(): void
    {
        $individual = $this->createStub(Individual::class);
        $individual->method('tag')->willReturn('INDI');

        $fact = new Fact("1 BIRT\n2 ADDR address", $individual, '');
        self::assertSame('address', $fact->attribute('ADDR'));

        $fact = new Fact("1 BIRT\n2 ADDR line 1\n3 CONT line 2", $individual, '');
        self::assertSame("line 1\nline 2", $fact->attribute('ADDR'));
    }

    /**
     * @see https://github.com/fisharebest/webtrees/issues/4417
     */
    public function testIssue4417(): void
    {
        $individual = $this->createStub(Individual::class);
        $individual->method('tag')->willReturn('INDI');

        $fact = new Fact("1 BIRT\n2 PLACXYZ\n3 CONT place", $individual, '');
        self::assertSame('', $fact->attribute('PLAC'));

        $fact = new Fact("1 BIRT\n2 PLACXYZ place", $individual, '');
        self::assertSame('', $fact->attribute('PLAC'));
    }
}
