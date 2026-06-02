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

namespace Fisharebest\Webtrees\Report;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GedcomTextReader::class)]
class GedcomTextReaderTest extends TestCase
{
    public function testGetSubRecordReturnsEmptyForEmptyInput(): void
    {
        self::assertSame('', GedcomTextReader::getSubRecord(1, '1 BIRT', ''));
    }

    public function testGetSubRecordExtractsSimpleSubrecord(): void
    {
        $gedrec = "0 @I1@ INDI\n1 BIRT\n2 DATE 1 JAN 1900\n2 PLAC Phoenix\n1 DEAT\n2 DATE 1 JAN 2000";

        $result = GedcomTextReader::getSubRecord(1, '1 BIRT', $gedrec);

        self::assertStringContainsString('1 BIRT', $result);
        self::assertStringContainsString('2 DATE 1 JAN 1900', $result);
        self::assertStringContainsString('2 PLAC Phoenix', $result);
        self::assertStringNotContainsString('DEAT', $result);
    }

    public function testGetSubRecordReturnsNthOccurrence(): void
    {
        $gedrec = "0 @I1@ INDI\n1 NAME John /Doe/\n1 NAME Johnny /Doe/";

        $first = GedcomTextReader::getSubRecord(1, '1 NAME', $gedrec, 1);
        $second = GedcomTextReader::getSubRecord(1, '1 NAME', $gedrec, 2);

        self::assertStringContainsString('John /Doe/', $first);
        self::assertStringContainsString('Johnny /Doe/', $second);
    }

    public function testGetSubRecordReturnsEmptyWhenNotFound(): void
    {
        $gedrec = "0 @I1@ INDI\n1 NAME John /Doe/";

        self::assertSame('', GedcomTextReader::getSubRecord(1, '1 BIRT', $gedrec));
    }

    public function testGetContReturnsEmptyWhenNoContinuationLines(): void
    {
        $record = "1 NOTE First line only";

        self::assertSame('', GedcomTextReader::getCont(2, $record));
    }

    public function testGetContMergesContinuationLines(): void
    {
        $record = "1 NOTE First line\n2 CONT Second line\n2 CONT Third line";

        $result = GedcomTextReader::getCont(2, $record);

        self::assertSame("\nSecond line\nThird line", $result);
    }

    public function testGetContIgnoresOtherLevels(): void
    {
        $record = "1 NOTE First line\n2 CONT Second line\n3 CONT Not this one";

        $result = GedcomTextReader::getCont(2, $record);

        self::assertSame("\nSecond line", $result);
    }
}
