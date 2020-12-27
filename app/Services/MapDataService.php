<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use stdClass;

use function array_unshift;
use function implode;

/**
 * Process geographic data.
 */
class MapDataService
{
    /**
     * @param int $id
     *
     * @return PlaceLocation
     */
    public function findById(int $id): PlaceLocation
    {
        $hierarchy = [];

        while ($id !== 0) {
            $row = DB::table('placelocation')
                ->where('pl_id', '=', $id)
                ->select(['pl_place', 'pl_parent_id'])
                ->first();

            if ($row === null) {
                $id = 0;
            } else {
                $hierarchy[] = $row->pl_place;
                $id          = (int) $row->pl_parent_id;
            }
        }

        return new PlaceLocation(implode(Gedcom::PLACE_SEPARATOR, $hierarchy));
    }

    /**
     * Which trees use a particular location?
     *
     * @param PlaceLocation $location
     *
     * @return array<string,array<stdClass>>
     */
    public function activePlaces(PlaceLocation $location): array
    {
        $parents  = $this->placeIdsForLocation($location);
        $children = [];

        $rows = DB::table('places')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'p_file')
            ->join('gedcom_setting', 'gedcom_setting.gedcom_id', '=', 'gedcom.gedcom_id')
            ->where('setting_name', '=', 'title')
            ->whereIn('p_parent_id', $parents)
            ->select(['p_place', 'gedcom_name AS tree_name', 'setting_value AS tree_title', 'p_id'])
            ->get();

        foreach ($rows as $row) {
            $children[$row->p_place][] = $row;
        }

        return $children;
    }

    /**
     * Make sure that all places in the genealogy data also exist in the location data.
     *
     * @return void
     */
    public function importMissingLocations(): void
    {
        $all_places = DB::table('places AS p0')
            ->leftJoin('places AS p1', 'p1.p_id', '=', 'p0.p_parent_id')
            ->leftJoin('places AS p2', 'p2.p_id', '=', 'p1.p_parent_id')
            ->leftJoin('places AS p3', 'p3.p_id', '=', 'p2.p_parent_id')
            ->leftJoin('places AS p4', 'p4.p_id', '=', 'p3.p_parent_id')
            ->leftJoin('places AS p5', 'p5.p_id', '=', 'p4.p_parent_id')
            ->leftJoin('places AS p6', 'p6.p_id', '=', 'p5.p_parent_id')
            ->leftJoin('places AS p7', 'p7.p_id', '=', 'p6.p_parent_id')
            ->leftJoin('places AS p8', 'p8.p_id', '=', 'p7.p_parent_id')
            ->select([
                'p0.p_place AS part_0',
                'p1.p_place AS part_1',
                'p2.p_place AS part_2',
                'p3.p_place AS part_3',
                'p4.p_place AS part_4',
                'p5.p_place AS part_5',
                'p6.p_place AS part_6',
                'p7.p_place AS part_7',
                'p8.p_place AS part_8',
            ])
            ->get()
            ->map(static function (stdClass $row): string {
                return implode(Gedcom::PLACE_SEPARATOR, (array) $row);
            });

        $all_locations = DB::table('placelocation AS p0')
            ->leftJoin('placelocation AS p1', 'p1.pl_id', '=', 'p0.pl_parent_id')
            ->leftJoin('placelocation AS p2', 'p2.pl_id', '=', 'p1.pl_parent_id')
            ->leftJoin('placelocation AS p3', 'p3.pl_id', '=', 'p2.pl_parent_id')
            ->leftJoin('placelocation AS p4', 'p4.pl_id', '=', 'p3.pl_parent_id')
            ->leftJoin('placelocation AS p5', 'p5.pl_id', '=', 'p4.pl_parent_id')
            ->leftJoin('placelocation AS p6', 'p6.pl_id', '=', 'p5.pl_parent_id')
            ->leftJoin('placelocation AS p7', 'p7.pl_id', '=', 'p6.pl_parent_id')
            ->leftJoin('placelocation AS p8', 'p8.pl_id', '=', 'p7.pl_parent_id')
            ->select([
                'p0.pl_place AS part_0',
                'p1.pl_place AS part_1',
                'p2.pl_place AS part_2',
                'p3.pl_place AS part_3',
                'p4.pl_place AS part_4',
                'p5.pl_place AS part_5',
                'p6.pl_place AS part_6',
                'p7.pl_place AS part_7',
                'p8.pl_place AS part_8',
            ])
            ->get()
            ->map(static function (stdClass $row): string {
                return implode(Gedcom::PLACE_SEPARATOR, (array) $row);
            });

        $missing = $all_places->diff($all_locations);


        foreach ($missing as $location) {
            (new PlaceLocation($location))->id();
        }
    }

    /**
     * Find all active places that match a location
     *
     * @param PlaceLocation $location
     *
     * @return array<string>
     */
    private function placeIdsForLocation(PlaceLocation $location): array
    {
        $hierarchy = [];

        while ($location->id() !== 0) {
            array_unshift($hierarchy, $location->locationName());
            $location = $location->parent();
        }

        $place_ids = ['0'];

        foreach ($hierarchy as $place_name) {
            $place_ids = DB::table('places')
                ->whereIn('p_parent_id', $place_ids)
                ->where('p_place', '=', $place_name)
                ->groupBy(['p_id'])
                ->pluck('p_id')
                ->all();
        }

        return $place_ids;
    }

    /**
     * @param int $location_id
     */
    public function deleteRecursively(int $location_id): void
    {
        $child_ids = DB::table('placelocation')
            ->where('pl_parent_id', '=', $location_id)
            ->pluck('pl_id');

        foreach ($child_ids as $child_id) {
            $this->deleteRecursively((int) $child_id);
        }

        DB::table('placelocation')
            ->where('pl_id', '=', $location_id)
            ->delete();
    }

    /**
     * Find a list of child places.
     * How many children does each child place have?  How many have co-ordinates?
     *
     * @param int $parent_id
     *
     * @return Collection<object>
     */
    public function getPlaceListLocation(int $parent_id): Collection
    {
        $prefix = DB::connection()->getTablePrefix();

        $expression =
            $prefix . 'p1.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p1.pl_lati, '') = '' OR " .
            $prefix . 'p2.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p2.pl_lati, '') = '' OR " .
            $prefix . 'p3.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p3.pl_lati, '') = '' OR " .
            $prefix . 'p4.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p4.pl_lati, '') = '' OR " .
            $prefix . 'p5.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p5.pl_lati, '') = '' OR " .
            $prefix . 'p6.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p6.pl_lati, '') = '' OR " .
            $prefix . 'p7.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p7.pl_lati, '') = '' OR " .
            $prefix . 'p8.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p8.pl_lati, '') = '' OR " .
            $prefix . 'p9.pl_place IS NOT NULL AND COALESCE(' . $prefix . "p9.pl_lati, '') = ''";

        $expression = 'CASE ' . $expression . ' WHEN TRUE THEN 1 ELSE 0 END';

        return DB::table('placelocation AS p0')
            ->leftJoin('placelocation AS p1', 'p1.pl_parent_id', '=', 'p0.pl_id')
            ->leftJoin('placelocation AS p2', 'p2.pl_parent_id', '=', 'p1.pl_id')
            ->leftJoin('placelocation AS p3', 'p3.pl_parent_id', '=', 'p2.pl_id')
            ->leftJoin('placelocation AS p4', 'p4.pl_parent_id', '=', 'p3.pl_id')
            ->leftJoin('placelocation AS p5', 'p5.pl_parent_id', '=', 'p4.pl_id')
            ->leftJoin('placelocation AS p6', 'p6.pl_parent_id', '=', 'p5.pl_id')
            ->leftJoin('placelocation AS p7', 'p7.pl_parent_id', '=', 'p6.pl_id')
            ->leftJoin('placelocation AS p8', 'p8.pl_parent_id', '=', 'p7.pl_id')
            ->leftJoin('placelocation AS p9', 'p9.pl_parent_id', '=', 'p8.pl_id')
            ->where('p0.pl_parent_id', '=', $parent_id)
            ->groupBy(['p0.pl_id'])
            ->orderBy(new Expression($prefix . 'p0.pl_place /*! COLLATE ' . I18N::collation() . ' */'))
            ->select([
                'p0.*',
                new Expression('COUNT(' . $prefix . 'p1.pl_id) AS child_count'),
                new Expression('SUM(' . $expression . ') AS no_coord'),
            ])
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->child_count = (int) $row->child_count;
                $row->no_coord    = (int) $row->no_coord;

                return $row;
            });
    }
}
