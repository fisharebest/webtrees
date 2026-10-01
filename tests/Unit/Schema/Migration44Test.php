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

use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\Migration44;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

#[CoversClass(Migration44::class)]
class Migration44Test extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(Migration44::class));
    }

    public function testImportsOnlyPlacesReachableFromRoots(): void
    {
        self::createDatabase();

        DB::schemaBuilder()->create('placelocation', static function (Blueprint $table): void {
            $table->integer('pl_id');
            $table->integer('pl_parent_id')->nullable();
            $table->string('pl_place');
            $table->string('pl_lati');
            $table->string('pl_long');
            $table->primary('pl_id');
        });

        $locations = [
            [1, null], [2, 0], [3, 2], [4, 99], [5, 4], [6, 5], [7, 1],
        ];

        foreach ($locations as [$id, $parent_id]) {
            DB::query(table: 'placelocation')->insert([
                'pl_id'        => $id,
                'pl_parent_id' => $parent_id,
                'pl_place'     => 'Place ' . $id,
                'pl_lati'      => 'N10',
                'pl_long'      => 'W20',
            ]);
        }

        DB::query(table: 'place_location')->insert([
            'id'        => 1,
            'parent_id' => null,
            'place'     => 'Place 1',
        ]);

        $select = (new ReflectionMethod(Migration44::class, 'legacyPlacesToImport'))->invoke(new Migration44());
        self::assertInstanceOf(QueryBuilder::class, $select);

        DB::query(table: 'place_location')
            ->insertFrom(['id', 'parent_id', 'place', 'latitude', 'longitude'], $select);

        self::assertSame([1, 2, 3, 7], DB::query()
            ->from('place_location')
            ->orderBy('id')
            ->pluck('id'));
        self::assertSame([null, null, 2, 1], DB::query()
            ->from('place_location')
            ->orderBy('id')
            ->pluck('parent_id'));
        self::assertSame(7, DB::query(table: 'placelocation')->count());
    }
}
