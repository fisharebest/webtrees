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
use Fisharebest\Webtrees\PlaceLocation;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

use function array_unshift;

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
     * @param PlaceLocation $location
     *
     * @return void
     */
    public function importMissingChildren(PlaceLocation $location): void
    {
        $parents = $this->placeIdsForLocation($location);

        $rows = DB::table('places')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'p_file')
            ->whereIn('p_parent_id', $parents)
            ->whereNotIn('p_place', static function ($query) use ($location): void {
                $query->select(['pl_place'])->from('placelocation')->where('pl_parent_id', '=', $location->id());
            })
            ->groupBy(['p_place'])
            ->select(['p_place'])
            ->get();

        if ($rows->isNotEmpty()) {
            $tmp   = clone $location;
            $level = 1;

            while ($tmp->id() !== 0) {
                $level++;
                $tmp = $tmp->parent();
            }

            foreach ($rows as $row) {
                $place_id = 1 + (int) DB::table('placelocation')->max('pl_id');

                DB::table('placelocation')->insert([
                    'pl_id'        => $place_id,
                    'pl_parent_id' => $location->id(),
                    'pl_level'     => $level,
                    'pl_place'     => $row->p_place,
                    'pl_lati'      => '',
                    'pl_long'      => '',
                    'pl_zoom'      => 2,
                    'pl_icon'      => '',
                ]);
            }
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
}
