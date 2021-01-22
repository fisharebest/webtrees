<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use function abs;
use function array_filter;
use function array_unshift;
use function implode;
use function mb_strtolower;
use function round;

/**
 * Process geographic data.
 */
class MapDataService
{
    // Location of files to import
    public const PLACES_FOLDER = 'places/';

    // Format of CSV files
    public const CSV_SEPARATOR = ';';

    /**
     * @param int $id
     *
     * @return PlaceLocation
     */
    public function findById(int $id): PlaceLocation
    {
        $hierarchy = [];

        while (true) {
            $row = DB::table('place_location')
                ->where('id', '=', $id)
                ->select(['place', 'parent_id'])
                ->first();

            if ($row === null) {
                return new PlaceLocation(implode(Gedcom::PLACE_SEPARATOR, $hierarchy));
            }

            $hierarchy[] = $row->place;
            $id          = $row->parent_id;
        }
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
            $children[mb_strtolower($row->p_place)][] = $row;
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
                return implode(Gedcom::PLACE_SEPARATOR, array_filter((array) $row));
            });

        $all_locations = DB::table('place_location AS p0')
            ->leftJoin('place_location AS p1', 'p1.id', '=', 'p0.parent_id')
            ->leftJoin('place_location AS p2', 'p2.id', '=', 'p1.parent_id')
            ->leftJoin('place_location AS p3', 'p3.id', '=', 'p2.parent_id')
            ->leftJoin('place_location AS p4', 'p4.id', '=', 'p3.parent_id')
            ->leftJoin('place_location AS p5', 'p5.id', '=', 'p4.parent_id')
            ->leftJoin('place_location AS p6', 'p6.id', '=', 'p5.parent_id')
            ->leftJoin('place_location AS p7', 'p7.id', '=', 'p6.parent_id')
            ->leftJoin('place_location AS p8', 'p8.id', '=', 'p7.parent_id')
            ->select([
                'p0.place AS part_0',
                'p1.place AS part_1',
                'p2.place AS part_2',
                'p3.place AS part_3',
                'p4.place AS part_4',
                'p5.place AS part_5',
                'p6.place AS part_6',
                'p7.place AS part_7',
                'p8.place AS part_8',
            ])
            ->get()
            ->map(static function (stdClass $row): string {
                return implode(Gedcom::PLACE_SEPARATOR, array_filter((array) $row));
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

        while ($location->id() !== null) {
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
     * @param int $id
     */
    public function deleteRecursively(int $id): void
    {
        // Uses on-delete-cascade
        DB::table('place_location')
            ->where('id', '=', $id)
            ->delete();
    }

    /**
     * Find a list of child places.
     * How many children does each child place have?  How many have co-ordinates?
     *
     * @param int|null $parent_id
     *
     * @return Collection<object>
     */
    public function getPlaceListLocation(?int $parent_id): Collection
    {
        $prefix = DB::connection()->getTablePrefix();

        $expression =
            $prefix . 'p1.place IS NOT NULL AND ' . $prefix . 'p1.latitude IS NULL OR ' .
            $prefix . 'p2.place IS NOT NULL AND ' . $prefix . 'p2.latitude IS NULL OR ' .
            $prefix . 'p3.place IS NOT NULL AND ' . $prefix . 'p3.latitude IS NULL OR ' .
            $prefix . 'p4.place IS NOT NULL AND ' . $prefix . 'p4.latitude IS NULL OR ' .
            $prefix . 'p5.place IS NOT NULL AND ' . $prefix . 'p5.latitude IS NULL OR ' .
            $prefix . 'p6.place IS NOT NULL AND ' . $prefix . 'p6.latitude IS NULL OR ' .
            $prefix . 'p7.place IS NOT NULL AND ' . $prefix . 'p7.latitude IS NULL OR ' .
            $prefix . 'p8.place IS NOT NULL AND ' . $prefix . 'p8.latitude IS NULL OR ' .
            $prefix . 'p9.place IS NOT NULL AND ' . $prefix . 'p9.latitude IS NULL';

        $expression = 'CASE ' . $expression . ' WHEN TRUE THEN 1 ELSE 0 END';

        $query = DB::table('place_location AS p0')
            ->leftJoin('place_location AS p1', 'p1.parent_id', '=', 'p0.id')
            ->leftJoin('place_location AS p2', 'p2.parent_id', '=', 'p1.id')
            ->leftJoin('place_location AS p3', 'p3.parent_id', '=', 'p2.id')
            ->leftJoin('place_location AS p4', 'p4.parent_id', '=', 'p3.id')
            ->leftJoin('place_location AS p5', 'p5.parent_id', '=', 'p4.id')
            ->leftJoin('place_location AS p6', 'p6.parent_id', '=', 'p5.id')
            ->leftJoin('place_location AS p7', 'p7.parent_id', '=', 'p6.id')
            ->leftJoin('place_location AS p8', 'p8.parent_id', '=', 'p7.id')
            ->leftJoin('place_location AS p9', 'p9.parent_id', '=', 'p8.id');

        if ($parent_id === null) {
            $query->whereNull('p0.parent_id');
        } else {
            $query->where('p0.parent_id', '=', $parent_id);
        }

        return $query
            ->groupBy(['p0.id'])
            ->orderBy(new Expression($prefix . 'p0.place /*! COLLATE ' . I18N::collation() . ' */'))
            ->select([
                'p0.*',
                new Expression('COUNT(' . $prefix . 'p1.id) AS child_count'),
                new Expression('SUM(' . $expression . ') AS no_coord'),
            ])
            ->get()
            ->map(static function (stdClass $row): stdClass {
                $row->child_count = (int) $row->child_count;
                $row->no_coord    = (int) $row->no_coord;
                $row->key         = mb_strtolower($row->place);

                return $row;
            });
    }

    /**
     * @param float $latitude
     *
     * @return string
     */
    public function writeLatitude(float $latitude): string
    {
        return $this->writeDegrees($latitude, Gedcom::LATITUDE_NORTH, Gedcom::LATITUDE_SOUTH);
    }

    /**
     * @param float $longitude
     *
     * @return string
     */
    public function writeLongitude(float $longitude): string
    {
        return $this->writeDegrees($longitude, Gedcom::LONGITUDE_EAST, Gedcom::LONGITUDE_WEST);
    }

    /**
     * @param float  $degrees
     * @param string $positive
     * @param string $negative
     *
     * @return string
     */
    private function writeDegrees(float $degrees, string $positive, string $negative): string
    {
        $degrees = round($degrees, 5);

        if ($degrees < 0.0) {
            return $negative . abs($degrees);
        }

        return $positive . $degrees;
    }
}
