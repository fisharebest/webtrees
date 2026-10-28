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

use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Report\GedcomTextReader;

#[CoversClass(GedcomTextReader::class)]
class GedcomTextReaderTest extends TestCase
{
    private function createTree(): Tree
    {
        return self::createStub(Tree::class);
    }

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

    public function testGetGedcomValueReturnsEmptyForEmptyRecord(): void
    {
        self::assertSame('', GedcomTextReader::getGedcomValue('NAME', 1, '', $this->createTree(), ''));
    }

    public function testGetGedcomValueReturnsEmptyWhenTagNotFound(): void
    {
        $gedrec = "0 @I1@ INDI\n1 NAME John /Doe/";

        self::assertSame('', GedcomTextReader::getGedcomValue('BIRT:DATE', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueExtractsSimpleValue(): void
    {
        $gedrec = "0 @I1@ INDI\n1 SEX M";

        self::assertSame('M', GedcomTextReader::getGedcomValue('SEX', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueExtractsNestedValue(): void
    {
        $gedrec = "0 @I1@ INDI\n1 BIRT\n2 DATE 1 JAN 1900";

        self::assertSame('1 JAN 1900', GedcomTextReader::getGedcomValue('BIRT:DATE', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueCanonicalizesSexValue(): void
    {
        // SexValue::canonical() converts to uppercase
        $gedrec = "0 @I1@ INDI\n1 SEX m";

        self::assertSame('M', GedcomTextReader::getGedcomValue('SEX', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueCanonicalizesNameType(): void
    {
        // NameType::canonical() converts to uppercase
        $gedrec = "0 @I1@ INDI\n1 NAME Jane /Doe/\n2 TYPE married";

        self::assertSame('MARRIED', GedcomTextReader::getGedcomValue('NAME:TYPE', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueCanonicalizesNameTypeInSubrecord(): void
    {
        // When gedrec is a sub-record (level 1), the caller provides the context
        $gedrec = "1 NAME Jane /Doe/\n2 TYPE married";

        self::assertSame('MARRIED', GedcomTextReader::getGedcomValue('NAME:TYPE', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueUsesExplicitElementTag(): void
    {
        // The context parameter provides the parent context for canonicalization
        $gedrec = "1 BIRT\n2 DATE 1 JAN 1900";

        self::assertSame('1 JAN 1900', GedcomTextReader::getGedcomValue('DATE', 2, $gedrec, $this->createTree(), 'INDI:BIRT'));
    }

    public function testGetGedcomValueNormalizesWhitespace(): void
    {
        // canonical() collapses multiple spaces
        $gedrec = "0 @I1@ INDI\n1 BIRT\n2 PLAC London,  England";

        self::assertSame('London, England', GedcomTextReader::getGedcomValue('BIRT:PLAC', 1, $gedrec, $this->createTree(), 'INDI'));
    }

    public function testGetGedcomValueStripsNameSlashes(): void
    {
        $gedrec = "0 @I1@ INDI\n1 NAME John /Doe/";

        self::assertSame('John Doe', GedcomTextReader::getGedcomValue('NAME', 1, $gedrec, $this->createTree(), 'INDI'));
    }
}
