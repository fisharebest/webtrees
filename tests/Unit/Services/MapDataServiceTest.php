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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MapDataService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_column;

#[CoversClass(MapDataService::class)]
class MapDataServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(MapDataService::class));
    }

    public function testFindByIdAndLocationList(): void
    {
        self::createDatabase();

        $country = new PlaceLocation('Country');
        $village = new PlaceLocation('Village, Country');
        $country_id = $country->id();
        $village_id = $village->id();
        self::assertNotNull($country_id);
        self::assertNotNull($village_id);

        $service = new MapDataService();

        self::assertSame('Village', $service->findById($village_id)->locationName());
        self::assertSame('Country', $service->findById($village_id)->parent()->locationName());
        self::assertSame('', $service->findById(-1)->locationName());

        $country_row = $service->getPlaceListLocation(null)->first();
        self::assertNotNull($country_row);
        self::assertSame($country_id, $country_row->id);
        self::assertSame('country', $country_row->key);
        self::assertSame(1, $country_row->child_count);
        self::assertSame(1, $country_row->no_coord);

        $village_row = $service->getPlaceListLocation($country_id)->first();
        self::assertNotNull($village_row);
        self::assertSame($village_id, $village_row->id);
        self::assertSame(0, $village_row->child_count);
        self::assertSame(0, $village_row->no_coord);
        self::assertCount(0, $service->getPlaceListLocation(-1));

        DB::query()
            ->from('place_location')
            ->whereEqual('id', $village_id)
            ->update(['latitude' => 42.5, 'longitude' => -3.5]);

        $village_row = $service->getPlaceListLocation($country_id)->first();
        self::assertNotNull($village_row);
        self::assertSame(42.5, $village_row->latitude);
        self::assertSame(-3.5, $village_row->longitude);
        self::assertSame(0, $service->getPlaceListLocation(null)->first()?->no_coord);
    }

    public function testActivePlacesIncludesAllMatchingTrees(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $first_tree = $tree_service->create('first', 'First tree');
        $second_tree = $tree_service->create('second', 'Second tree');

        foreach ([$first_tree, $second_tree] as $tree) {
            $country_id = DB::query(table: 'places')->insertGetId(
                values: ['p_place' => 'Country', 'p_parent_id' => 0, 'p_file' => $tree->id()],
                id_column: 'p_id',
            );
            $village_id = DB::query(table: 'places')->insertGetId(
                values: ['p_place' => 'Village', 'p_parent_id' => $country_id, 'p_file' => $tree->id()],
                id_column: 'p_id',
            );
            DB::query(table: 'places')->insert(values: [
                'p_place' => 'Hamlet', 'p_parent_id' => $village_id, 'p_file' => $tree->id(),
            ]);
        }

        $service = new MapDataService();
        self::assertCount(2, $service->activePlaces(new PlaceLocation('Country'))['village']);
        $places = $service->activePlaces(new PlaceLocation('Village, Country'));
        self::assertArrayHasKey('hamlet', $places);
        self::assertCount(2, $places['hamlet']);
        self::assertSame(['first', 'second'], array_column($places['hamlet'], 'tree_name'));
        self::assertSame(['First tree', 'Second tree'], array_column($places['hamlet'], 'tree_title'));
        self::assertSame([], $service->activePlaces(new PlaceLocation('Missing, Country')));
    }

    public function testImportMissingLocationsOnlyAddsMissingHierarchy(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $country_id = DB::query(table: 'places')->insertGetId(
            values: ['p_place' => 'Country', 'p_parent_id' => 0, 'p_file' => $tree->id()],
            id_column: 'p_id',
        );
        DB::query(table: 'places')->insert(values: [
            'p_place' => 'Village', 'p_parent_id' => $country_id, 'p_file' => $tree->id(),
        ]);

        $service = new MapDataService();
        $service->importMissingLocations();

        self::assertTrue((new PlaceLocation('Village, Country'))->exists());
        self::assertSame(2, DB::query()
            ->from('place_location')
            ->count());

        $service->importMissingLocations();
        self::assertSame(2, DB::query()
            ->from('place_location')
            ->count());
    }

    public function testImportMissingLocationsBeyondNineLevels(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $parent_id = DB::query(table: 'places')->insertGetId(
            values: ['p_place' => 'Country', 'p_parent_id' => 0, 'p_file' => $tree->id()],
            id_column: 'p_id',
        );
        $path = 'Country';
        (new PlaceLocation($path))->id();

        for ($depth = 1; $depth <= 12; $depth++) {
            $name = 'Level' . $depth;
            $parent_id = DB::query(table: 'places')->insertGetId(
                values: ['p_place' => $name, 'p_parent_id' => $parent_id, 'p_file' => $tree->id()],
                id_column: 'p_id',
            );
            $path = $name . ', ' . $path;
        }

        $service = new MapDataService();
        $service->importMissingLocations();

        self::assertTrue((new PlaceLocation($path))->exists());
        self::assertSame(13, DB::query()
            ->from('place_location')
            ->count());

        $service->importMissingLocations();
        self::assertSame(13, DB::query()
            ->from('place_location')
            ->count());
    }

    public function testCountsDeepAndBranchingDescendantPaths(): void
    {
        self::createDatabase();

        $root_id = DB::query(table: 'place_location')->insertGetId(
            values: ['place' => 'Country', 'parent_id' => null],
            id_column: 'id',
        );
        $parent_id = $root_id;
        $first_level_id = 0;
        $penultimate_id = 0;
        for ($depth = 1; $depth <= 12; $depth++) {
            if ($depth === 12) {
                $penultimate_id = $parent_id;
            }

            $parent_id = DB::query(table: 'place_location')->insertGetId(
                values: [
                    'place' => 'Level' . $depth,
                    'parent_id' => $parent_id,
                    'latitude' => $depth === 12 ? null : 42.5,
                ],
                id_column: 'id',
            );

            if ($depth === 1) {
                $first_level_id = $parent_id;
            }
        }
        DB::query(table: 'place_location')->insert(values: [
            'place' => 'Other branch', 'parent_id' => $first_level_id, 'latitude' => 42.5,
        ]);
        DB::query(table: 'place_location')->insert(values: [
            'place' => 'Root branch', 'parent_id' => $root_id, 'latitude' => 42.5,
        ]);

        $service = new MapDataService();
        $root = $service->getPlaceListLocation(null)->first();
        self::assertNotNull($root);
        self::assertSame(3, $root->child_count);
        self::assertSame(1, $root->no_coord);

        $first_level = $service->getPlaceListLocation($root_id)->first();
        self::assertNotNull($first_level);
        self::assertSame($first_level_id, $first_level->id);
        self::assertSame(2, $first_level->child_count);
        self::assertSame(1, $first_level->no_coord);
        self::assertSame(0, $service->getPlaceListLocation($penultimate_id)->first()?->no_coord);
    }

    public function testDeleteUnusedLocationsKeepsLocationsWithPlaces(): void
    {
        self::createDatabase();

        $country_id = (new PlaceLocation('Country'))->id();
        $used_id = (new PlaceLocation('Used, Country'))->id();
        $unused_id = (new PlaceLocation('Unused, Country'))->id();
        self::assertNotNull($country_id);
        self::assertNotNull($used_id);
        self::assertNotNull($unused_id);

        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');
        $parent_place_id = DB::query(table: 'places')->insertGetId(
            values: ['p_place' => 'Country', 'p_parent_id' => 0, 'p_file' => $tree->id()],
            id_column: 'p_id',
        );
        DB::query(table: 'places')->insert(values: [
            'p_place' => 'Used', 'p_parent_id' => $parent_place_id, 'p_file' => $tree->id(),
        ]);

        (new MapDataService())->deleteUnusedLocations(null, [0]);

        self::assertSame([$country_id, $used_id], DB::query()
            ->from('place_location')
            ->orderBy('id')
            ->pluck('id'));
    }
}
