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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Http\Controllers\MapDataSave;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapDataSave::class)]
class MapDataSaveTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(MapDataSave::class));
    }

    public function testCreatesOnlyNewPlacesUnderEachParent(): void
    {
        self::createDatabase();

        $controller   = new MapDataSave();
        $root_request = self::createRequest(HttpRequestMethod::POST->value, params: [
            'parent_id'      => '',
            'place_id'       => '',
            'new_place_lati' => '',
            'new_place_long' => '',
            'new_place_name' => 'Country',
            'url'            => 'https://webtrees.test/',
        ]);

        $controller->post($root_request);
        $controller->post($root_request);

        $parent_id = DB::query(table: 'place_location')->whereEqual('place', 'Country')->value('id');
        self::assertIsInt($parent_id);
        self::assertSame(1, DB::query(table: 'place_location')->count());

        $child_request = self::createRequest(HttpRequestMethod::POST->value, params: [
            'parent_id'      => (string) $parent_id,
            'place_id'       => '',
            'new_place_lati' => '',
            'new_place_long' => '',
            'new_place_name' => 'Country',
            'url'            => 'https://webtrees.test/',
        ]);

        $controller->post($child_request);
        $controller->post($child_request);

        self::assertSame(2, DB::query(table: 'place_location')->count());
    }
}
