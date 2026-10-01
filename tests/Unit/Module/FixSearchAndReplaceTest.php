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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Module\FixSearchAndReplace;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FixSearchAndReplace::class)]
class FixSearchAndReplaceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(FixSearchAndReplace::class));
    }

    public function testSearchFiltersAllRecordTypesAndEscapesLikeWildcards(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $import_service = new GedcomImportService();

        foreach (['F1' => 'FAM', 'I1' => 'INDI', 'L1' => '_LOC', 'M1' => 'OBJE', 'N1' => 'NOTE', 'R1' => 'REPO', 'S1' => 'SOUR', 'U1' => 'SUBM'] as $xref => $type) {
            $import_service->importRecord("0 @$xref@ $type\n1 NOTE Needle_% here", $tree, true);
        }
        $import_service->importRecord("0 @I2@ INDI\n1 NOTE Needle_X% here", $tree, true);

        $module = new FixSearchAndReplace(new DataFixService());

        foreach (['FAM' => 'F1', 'INDI' => 'I1', '_LOC' => 'L1', 'OBJE' => 'M1', 'NOTE' => 'N1', 'REPO' => 'R1', 'SOUR' => 'S1', 'SUBM' => 'U1'] as $type => $xref) {
            $records = $module->recordsToFix($tree, [
                'type' => $type,
                'search-for' => 'Needle_%',
                'method' => 'exact',
                'case' => '',
            ]);

            self::assertSame([$xref], $records->pluck('xref')->all(), $type);
        }

        self::assertSame([], $module->recordsToFix($tree, [
            'type' => 'SOUR',
            'search-for' => 'Needle_%',
            'method' => 'exact',
            'case' => '',
            'start' => 'S2',
            'end' => 'S9',
        ])->all());
    }

    public function testCaseInsensitiveLikeAndRegexFindCandidates(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        (new GedcomImportService())->importRecord("0 @I1@ INDI\n1 NOTE Needle value", $tree, true);

        $module = new FixSearchAndReplace(new DataFixService());

        foreach (['exact' => 'needle', 'wildcards' => 'nee*le', 'regex' => 'needle'] as $method => $search) {
            $records = $module->recordsToFix($tree, [
                'type' => 'INDI',
                'search-for' => $search,
                'method' => $method,
                'case' => 'i',
            ]);

            self::assertSame(['I1'], $records->pluck('xref')->all(), $method);
        }

        self::assertSame([], $module->recordsToFix($tree, [
            'type' => 'INDI',
            'search-for' => 'needle',
            'method' => 'regex',
            'case' => '',
        ])->all());
    }
}
