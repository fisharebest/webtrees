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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\HitCountFooterModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HitCountFooterModule::class)]
class HitCountFooterModuleTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(HitCountFooterModule::class));
    }

    public function testCountsNewAndRepeatedPageVisits(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('name', 'title');
        DB::query(table: 'hit_counter')->insert([
            'gedcom_id'      => $tree->id(),
            'page_name'      => 'index.php',
            'page_parameter' => 'gedcom:' . $tree->id(),
            'page_count'     => 4,
        ]);

        $module = new class () extends HitCountFooterModule {
            public function countVisit(Tree $tree, string $page, string $parameter): int
            {
                return $this->countHit($tree, $page, $parameter);
            }
        };

        self::assertSame(5, $module->countVisit($tree, 'index.php', 'gedcom:' . $tree->id()));
        self::assertSame(5, $module->countVisit($tree, 'index.php', 'gedcom:' . $tree->id()));
        self::assertSame(1, $module->countVisit($tree, 'individual.php', 'X1'));

        self::assertSame(5, (int) DB::query()
            ->from('hit_counter')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('page_name', 'index.php')
            ->whereEqual('page_parameter', 'gedcom:' . $tree->id())
            ->value('page_count'));
        self::assertSame(1, (int) DB::query()
            ->from('hit_counter')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('page_name', 'individual.php')
            ->whereEqual('page_parameter', 'X1')
            ->value('page_count'));
    }
}
