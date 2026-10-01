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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\PlaceLocation;
use Illuminate\Support\Collection;
use stdClass;

use function abs;
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
    public const string PLACES_FOLDER = 'places/';

    // Format of CSV files
    public const string CSV_SEPARATOR = ';';

    public function findById(int $id): PlaceLocation
    {
        $hierarchy = [];

        while (true) {
            $row = DB::query()
                ->select('place', 'parent_id')
                ->from('place_location')
                ->whereEqual('id', $id)
                ->first();

            if ($row === null) {
                return new PlaceLocation(implode(Gedcom::PLACE_SEPARATOR, $hierarchy));
            }

            $hierarchy[] = $row->place;

            if ($row->parent_id === null) {
                return new PlaceLocation(implode(Gedcom::PLACE_SEPARATOR, $hierarchy));
            }

            $id = (int) $row->parent_id;
        }
    }

    /**
     * Which trees use a particular location?
     *
     * @return array<list<object{p_place:string,tree_name:string,tree_title:string,p_id:int}>>
     */
    public function activePlaces(PlaceLocation $location): array
    {
        $parents  = $this->placeIdsForLocation($location);
        $children = [];

        $rows = new Collection(DB::query()
            ->select('p_place', 'gedcom_name AS tree_name', 'title AS tree_title', 'p_id')
            ->from('places')
            ->join('gedcom', static fn (JoinOn $j): JoinOn => $j->on('gedcom_id', 'p_file'))
            ->whereIn('p_parent_id', $parents)
            ->get());

        $rows = $rows->map(static fn (object $row): object => (object) [
            'p_place'    => $row->p_place,
            'tree_name'  => $row->tree_name,
            'tree_title' => $row->tree_title,
            'p_id'       => (int) $row->p_id,
        ]);

        foreach ($rows as $row) {
            $children[mb_strtolower($row->p_place)][] = $row;
        }

        return $children;
    }

    /**
     * Make sure that all places in the genealogy data also exist in the location data.
     */
    public function importMissingLocations(): void
    {
        $missing = $this->hierarchyPaths('places', 'p_id', 'p_parent_id', 'p_place')
            ->diff($this->hierarchyPaths('place_location', 'id', 'parent_id', 'place'));

        foreach ($missing as $location) {
            (new PlaceLocation($location))->id();
        }
    }

    public function deleteRecursively(int $id): void
    {
        // Uses on-delete-cascade
        DB::query(table: 'place_location')
            ->whereEqual('id', $id)
            ->delete();
    }

    /**
     * @param list<int> $parent_place_ids
     */
    public function deleteUnusedLocations(int|null $parent_location_id, array $parent_place_ids): void
    {
        if ($parent_location_id === null) {
            $location_query = DB::query()
                ->select('id', 'place')
                ->from('place_location')
                ->whereNull('parent_id');
        } else {
            $location_query = DB::query()
                ->select('id', 'place')
                ->from('place_location')
                ->whereEqual('parent_id', $parent_location_id);
        }

        foreach ($location_query->get() as $location) {
            $places = DB::query()
                ->select('p_id')
                ->from('places')
                ->whereIn('p_parent_id', $parent_place_ids)
                ->whereEqual('p_place', $location->place)
                ->get(in: Collection::class);

            if ($places->isEmpty()) {
                FlashMessages::addMessage(I18N::translate('“%s” has been deleted.', e($location->place)));

                DB::query(table: 'place_location')
                    ->whereEqual('id', $location->id)
                    ->delete();
            } else {
                $place_ids = $places->map(static fn (object $place): int => (int) $place->p_id)->all();
                $this->deleteUnusedLocations((int) $location->id, $place_ids);
            }
        }
    }

    /**
     * Find a list of child places.
     * How many children does each child place have?  How many have co-ordinates?
     *
     * @return Collection<int,object{id:int,key:string,place:string,latitude:float|null,longitude:float|null,child_count:int,no_coord:int}>
     */
    public function getPlaceListLocation(int|null $parent_id): Collection
    {
        return (new Collection($this->locationRowsWithCte($parent_id)))
            ->map(static fn (object $row): object => (object) [
                'id'          => (int) $row->id,
                'place'       => $row->place,
                'key'         => mb_strtolower($row->place),
                'latitude'    => $row->latitude === null ? null : (float) $row->latitude,
                'longitude'   => $row->longitude === null ? null : (float) $row->longitude,
                'child_count' => (int) $row->child_count,
                'no_coord'    => (int) $row->no_coord,
            ])
            ->sort(static fn (object $x, object $y): int => I18N::compare($x->place, $y->place));
    }

    public function writeLatitude(float $latitude): string
    {
        return $this->writeDegrees($latitude, Gedcom::LATITUDE_NORTH, Gedcom::LATITUDE_SOUTH);
    }

    public function writeLongitude(float $longitude): string
    {
        return $this->writeDegrees($longitude, Gedcom::LONGITUDE_EAST, Gedcom::LONGITUDE_WEST);
    }

    /**
     * @return Collection<int,string>
     */
    private function hierarchyPaths(string $table, string $id_column, string $parent_column, string $name_column): Collection
    {
        $initial = DB::query()
            ->select(
                $id_column . ' AS node_id',
                $parent_column,
                $name_column,
                DB::alias(DB::value(0), 'depth'),
            )
            ->from($table);

        $recursive = DB::query()
            ->select(
                'child.node_id',
                'parent.' . $parent_column,
                'parent.' . $name_column,
                DB::raw('child.depth + 1'),
            )
            ->from($table, 'parent')
            ->join('place_hierarchy AS child', static fn (JoinOn $j): JoinOn => $j->on('parent.' . $id_column, 'child.' . $parent_column));

        $rows = DB::query()
            ->withRecursiveCte('place_hierarchy', $initial, $recursive)
            ->select('node_id', $name_column)
            ->from('place_hierarchy')
            ->orderBy('node_id', 'depth')
            ->get();

        $parts = [];
        foreach ($rows as $row) {
            $parts[$row->node_id][] = $row->$name_column;
        }

        return (new Collection($parts))
            ->map(static fn (array $names): string => implode(Gedcom::PLACE_SEPARATOR, $names));
    }

    /**
     * @return list<stdClass>
     */
    private function locationRowsWithCte(int|null $parent_id): array
    {
        $initial = DB::query()
            ->select(
                'parent.id AS root_id',
                'child.id AS descendant_id',
                DB::alias(DB::raw('CASE WHEN child.latitude IS NULL THEN 1 ELSE 0 END'), 'missing'),
            )
            ->from('place_location', 'parent')
            ->join('place_location AS child', static fn (JoinOn $j): JoinOn => $j->on('child.parent_id', 'parent.id'));

        $recursive = DB::query()
            ->select(
                'path.root_id',
                'child.id',
                DB::raw('CASE WHEN path.missing = 1 OR child.latitude IS NULL THEN 1 ELSE 0 END'),
            )
            ->from('location_paths', 'path')
            ->join('place_location AS child', static fn (JoinOn $j): JoinOn => $j->on('child.parent_id', 'path.descendant_id'));

        // Each terminal path counts once, and is flagged if any descendant lacks coordinates.
        $counts = DB::query()
            ->select(
                'path.root_id',
                DB::alias(DB::count('*'), 'child_count'),
                DB::alias(DB::sum('path.missing'), 'no_coord'),
            )
            ->from('location_paths', 'path')
            ->leftJoin('place_location AS child', static fn (JoinOn $j): JoinOn => $j->on('child.parent_id', 'path.descendant_id'))
            ->whereNull('child.id')
            ->groupBy('path.root_id');

        $query = DB::query()
            ->withRecursiveCte('location_paths', $initial, $recursive)
            ->withCte('location_counts', $counts)
            ->select(
                'p0.id',
                'p0.parent_id',
                'p0.place',
                'p0.latitude',
                'p0.longitude',
                DB::alias(DB::coalesce(DB::column('location_counts.child_count'), DB::value(0)), 'child_count'),
                DB::alias(DB::coalesce(DB::column('location_counts.no_coord'), DB::value(0)), 'no_coord'),
            )
            ->from('place_location', 'p0')
            ->leftJoin('location_counts', static fn (JoinOn $j): JoinOn => $j->on('location_counts.root_id', 'p0.id'));

        if ($parent_id === null) {
            $query = $query->whereNull('p0.parent_id');
        } else {
            $query = $query->whereEqual('p0.parent_id', $parent_id);
        }

        return $query->get();
    }

    /**
     * Find all active places that match a location
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
            $place_ids = DB::query()
                ->from('places')
                ->whereIn('p_parent_id', $place_ids)
                ->whereEqual('p_place', $place_name)
                ->groupBy('p_id')
                ->pluck('p_id');
        }

        return $place_ids;
    }

    private function writeDegrees(float $degrees, string $positive, string $negative): string
    {
        $degrees = round($degrees, 5);

        if ($degrees < 0.0) {
            return $negative . abs($degrees);
        }

        return $positive . $degrees;
    }
}
