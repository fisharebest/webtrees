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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(GedcomEditService::class)]
class GedcomEditServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testEditLinesToGedcom(): void
    {
        $gedcom_edit_service = new GedcomEditService();

        self::assertSame(
            '1 BIRT Y',
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1'],
                ['BIRT'],
                ['Y'],
                false
            )
        );

        self::assertSame(
            "\n1 BIRT Y\n2 ADDR England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'ADDR'],
                ['Y', 'England']
            )
        );

        self::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2'],
                ['BIRT', 'PLAC'],
                ['Y', 'England']
            )
        );

        self::assertSame(
            "\n1 BIRT\n2 PLAC England\n2 SOUR @S1@\n3 PAGE 123",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '@S1@', '123']
            )
        );

        // Missing SOUR, so ignore PAGE
        self::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        self::assertSame(
            "\n1 BIRT\n2 PLAC England",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123']
            )
        );

        self::assertSame(
            "\n1 BIRT\n2 PLAC England\n1 DEAT\n2 PLAC Scotland",
            $gedcom_edit_service->editLinesToGedcom(
                'INDI',
                ['1', '2', '2', '3', '1', '2', '2', '3'],
                ['BIRT', 'PLAC', 'SOUR', 'PAGE', 'DEAT', 'PLAC', 'SOUR', 'PAGE'],
                ['Y', 'England', '', '123', 'Y', 'Scotland', '', '123']
            )
        );
    }

    /**
     * @param string $required_famfacts
     * @param array<string> $expected_new_facts
     */
    #[DataProvider('newFamilyFactsData')]
    public function testNewFamilyFacts(string $required_famfacts, array $expected_new_facts): void
    {
        $gedcom_edit_service = new GedcomEditService();

        $tree = $this->createMock(Tree::class);
        $tree->method('getPreference')->with('QUICK_REQUIRED_FAMFACTS')->willReturn($required_famfacts);

        $new_facts = $gedcom_edit_service->newFamilyFacts($tree);
        self::assertSameSize($expected_new_facts, $new_facts);
        for ($i = 0; $i < count($expected_new_facts); $i++) {
            $new_fact = $new_facts->get($i);
            self::assertInstanceOf(Fact::class, $new_fact);
            self::assertSame($expected_new_facts[$i], $new_fact->tag());
        }
    }

    /**
     * @param array<string> $names
     * @param array<string> $expected_new_facts
     */
    #[DataProvider('newIndividualFactsData')]
    public function testNewIndividualFactsWithNoFacts(
        string $required_facts,
        string $sex,
        array $names,
        array $expected_new_facts
    ): void {
        $gedcom_edit_service = new GedcomEditService();

        $tree = $this->createMock(Tree::class);
        $tree->method('getPreference')->with('QUICK_REQUIRED_FACTS')->willReturn($required_facts);

        $new_facts = $gedcom_edit_service->newIndividualFacts($tree, $sex, $names);
        self::assertSameSize($expected_new_facts, $new_facts);
        for ($i = 0; $i < count($expected_new_facts); $i++) {
            $new_fact = $new_facts->get($i);
            self::assertInstanceOf(Fact::class, $new_fact);
            self::assertSame($expected_new_facts[$i], $new_fact->tag());
        }
    }

    /**
     * @return array<array<string|array<string>>>
     */
    public static function newFamilyFactsData(): array
    {
        return [
            ['', []],
            ['MARR', ['FAM:MARR']],
            ['FOOTAG', ['FAM:FOOTAG']],
            ['MARR,DIV', ['FAM:MARR', 'FAM:DIV']],
        ];
    }

    /**
     * @return array<array<string|array<string>>>
     */
    public static function newIndividualFactsData(): array
    {
        return [
            ['', 'F', ['1 NAME FOONAME'], ['INDI:SEX', 'INDI:NAME']],
            ['BIRT', 'F', ['1 NAME FOONAME'], ['INDI:SEX', 'INDI:NAME', 'INDI:BIRT']],
            ['FOOTAG', 'F', ['1 NAME FOONAME'], ['INDI:SEX', 'INDI:NAME', 'INDI:FOOTAG']],
            ['BIRT,DEAT', 'F', ['1 NAME FOONAME'], ['INDI:SEX', 'INDI:NAME', 'INDI:BIRT', 'INDI:DEAT']],
        ];
    }
}
