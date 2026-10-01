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

namespace Fisharebest\Webtrees\Tests\Unit\Schema;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\SeedDefaultResnTable;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SeedDefaultResnTable::class)]
class SeedDefaultResnTableTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(SeedDefaultResnTable::class));
    }

    public function testRunRestoresFiveDefaultRowsWithoutDuplicatingThem(): void
    {
        self::createDatabase();

        DB::query()
            ->from('default_resn')
            ->whereEqual('default_resn_id', 1)
            ->update(['resn' => 'hidden']);
        DB::query(table: 'default_resn')->insert([
            'gedcom_id' => -1,
            'tag_type'  => 'BIRT',
            'resn'      => 'none',
        ]);

        $seed = new SeedDefaultResnTable();
        $seed->run();
        $seed->run();

        self::assertSame([
            1 => 'SSN',
            2 => 'SOUR',
            3 => 'REPO',
            4 => 'SUBM',
            5 => 'SUBN',
            6 => 'BIRT',
        ], DB::query()
            ->from('default_resn')
            ->orderBy('default_resn_id')
            ->pluck('tag_type', 'default_resn_id'));
        self::assertSame([
            1 => 'confidential',
            2 => 'privacy',
            3 => 'privacy',
            4 => 'confidential',
            5 => 'confidential',
            6 => 'none',
        ], DB::query()
            ->from('default_resn')
            ->orderBy('default_resn_id')
            ->pluck('resn', 'default_resn_id'));
    }
}
