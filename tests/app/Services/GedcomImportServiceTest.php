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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

#[CoversClass(GedcomImportService::class)]
class GedcomImportServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomImportService::class));
    }

    public function testReformatNoteWithoutTrailingSpace(): void
    {
        $tree   = self::createStub(Tree::class);
        $method = new ReflectionMethod(GedcomImportService::class, 'reformatRecord');

        self::assertSame(
            '0 @N1@ NOTE',
            $method->invoke(new GedcomImportService(), '0 @N1@ NOTE ', $tree)
        );
    }

    public function testReformatNoteWithInitialConcatenation(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('getPreference')->willReturn('0');
        $method = new ReflectionMethod(GedcomImportService::class, 'reformatRecord');

        self::assertSame(
            '0 @N1@ NOTE first',
            $method->invoke(new GedcomImportService(), "0 @N1@ NOTE\n1 CONC first", $tree)
        );
        self::assertSame(
            "0 @N1@ NOTE first\n1 CONT second",
            $method->invoke(new GedcomImportService(), "0 @N1@ NOTE first\n1 CONT\n1 CONC second", $tree)
        );
        self::assertSame(
            "0 @N1@ NOTE first\n1 CONT second",
            $method->invoke(new GedcomImportService(), "0 @N1@ NOTE first\n1 CONT\n1 CONC\n1 CONC second", $tree)
        );
        self::assertSame(
            "0 @N1@ NOTE first\n1 CONT secondthird",
            $method->invoke(new GedcomImportService(), "0 @N1@ NOTE first\n1 CONT second\n1 CONC third", $tree)
        );
    }

    public function testReformatWordWrappedNote(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('getPreference')->willReturn('1');
        $method = new ReflectionMethod(GedcomImportService::class, 'reformatRecord');

        self::assertSame(
            "0 @N1@ NOTE first\n1 CONT second third",
            $method->invoke(new GedcomImportService(), "0 @N1@ NOTE first\n1 CONT second\n1 CONC third", $tree)
        );
    }
}
