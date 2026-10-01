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

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\StatisticsData;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;
use SortDirection;

#[CoversClass(StatisticsData::class)]
class StatisticsDataTest extends TestCase
{
    private StatisticsData $statistics;
    private Tree $tree;

    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();

        $this->tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $this->statistics = new StatisticsData($this->tree, new UserService(new SystemClock()));
    }

    public function testEmptyAggregatesAndZeroLimits(): void
    {
        self::assertSame(0.0, $this->statistics->averageChildrenPerFamily());
        self::assertSame(0, $this->statistics->countChildren());
        self::assertSame(0, $this->statistics->countHits('page', 'parameter'));
        self::assertSame(0, $this->statistics->averageLifespanDays(null));
        self::assertSame([], $this->statistics->commonSurnames(0, 1, 'alpha'));
        self::assertSame([], $this->statistics->countCountries(0));
        self::assertSame([], $this->statistics->familiesWithTheMostChildren(0));
        self::assertSame([], $this->statistics->maximumAgeBetweenSiblings(0));
        self::assertTrue($this->statistics->topTenOldestAliveQuery(null, 0)->isEmpty());
        self::assertTrue($this->statistics->topTenOldestQuery(null, 0)->isEmpty());
    }

    public function testFamilyAggregatesAndRankings(): void
    {
        $import_service = new GedcomImportService();
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Father /Example/\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1900\n1 DEAT\n2 DATE 1 JAN 1980", $this->tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Mother /Example/\n1 SEX F\n1 BIRT\n2 DATE 1 JAN 1910\n1 DEAT\n2 DATE 1 JAN 1990", $this->tree, true);
        $import_service->importRecord("0 @I3@ INDI\n1 NAME Child /Example/\n1 SEX M\n1 BIRT\n2 DATE 1 JAN 1930", $this->tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @I1@\n1 WIFE @I2@\n1 CHIL @I3@\n1 MARR\n2 DATE 1 JAN 1929", $this->tree, true);

        self::assertSame(1.0, $this->statistics->averageChildrenPerFamily());
        self::assertSame(1, $this->statistics->countChildren());
        DB::query()->from('hit_counter')->insert([
            'gedcom_id' => $this->tree->id(),
            'page_name' => 'page',
            'page_parameter' => 'parameter',
            'page_count' => 4,
        ]);
        self::assertSame(4, $this->statistics->countHits('page', 'parameter'));
        self::assertSame(0, $this->statistics->countHits('page', 'other'));
        self::assertSame('F1', $this->statistics->familiesWithTheMostChildren(1)[0]->family->xref());
        self::assertSame(['Example' => ['Example' => 2]], $this->statistics->commonSurnames(1, 1, 'alpha'));
        self::assertGreaterThan(0, $this->statistics->averageLifespanDays(Sex::Female));
        self::assertCount(1, $this->statistics->topTenOldestQuery(Sex::Female, 1));
        self::assertNotEmpty($this->statistics->statsAgeQuery(Sex::Female, 1900, 2000));
    }

    public function testFirstEventOrdersBySortDirection(): void
    {
        $import_service = new GedcomImportService();
        $import_service->importRecord("0 @I1@ INDI\n1 NAME Earlier /Example/\n1 BIRT\n2 DATE 1 JAN 1700", $this->tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Later /Example/\n1 BIRT\n2 DATE 1 JAN 2000", $this->tree, true);

        self::assertSame('1700', $this->statistics->firstEventYear(['BIRT'], SortDirection::Ascending));
        self::assertSame('2000', $this->statistics->firstEventYear(['BIRT'], SortDirection::Descending));
    }
}
