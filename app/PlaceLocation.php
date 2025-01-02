<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

use function max;
use function min;
use function preg_split;
use function trim;

use const PREG_SPLIT_NO_EMPTY;

class PlaceLocation
{
    // e.g. "Westminster, London, England"
    private string $location_name;

    /** @var Collection<int,string> The parts of a location name, e.g. ["Westminster", "London", "England"] */
    private Collection $parts;

    /**
     * Create a place-location.
     *
     * @param string $location_name
     */
    public function __construct(string $location_name)
    {
        // Ignore any empty parts in location names such as "Village, , , Country".
        $location_name = trim($location_name);
        $this->parts   = new Collection(preg_split(Gedcom::PLACE_SEPARATOR_REGEX, $location_name, -1, PREG_SPLIT_NO_EMPTY));

        // Rebuild the location name in the correct format.
        $this->location_name = $this->parts->implode(Gedcom::PLACE_SEPARATOR);
    }

    /**
     * Get the higher level location.
     *
     * @return PlaceLocation
     */
    public function parent(): PlaceLocation
    {
        return new self($this->parts->slice(1)->implode(Gedcom::PLACE_SEPARATOR));
    }

    /**
     * The database row id that contains this location.
     * Note that due to database collation, both "Quebec" and "Québec" will share the same row.
     *
     * @return int|null
     */
    public function id(): int|null
    {
        // The "top-level" location won't exist in the database.
        if ($this->parts->isEmpty()) {
            return null;
        }

        return Registry::cache()->array()->remember('location-' . $this->location_name, function () {
            $parent_id = $this->parent()->id();

            $place = $this->parts->first();
            $place = mb_substr($place, 0, 120);

            if ($parent_id === null) {
                $location_id = DB::table('place_location')
                    ->where('place', '=', $place)
                    ->whereNull('parent_id')
                    ->value('id');
            } else {
                $location_id = DB::table('place_location')
                    ->where('place', '=', $place)
                    ->where('parent_id', '=', $parent_id)
                    ->value('id');
            }

            $location_id ??= DB::table('place_location')->insertGetId([
                    'parent_id' => $parent_id,
                    'place'     => $place,
                ]);

            return (int) $location_id;
        });
    }

    /**
     * Does this location exist in the database?  Note that calls to PlaceLocation::id() will
     * create the row, so this function is only meaningful when called before a call to PlaceLocation::id().
     *
     * @return bool
     */
    public function exists(): bool
    {
        $parent_id = null;

        foreach ($this->parts->reverse() as $place) {
            if ($parent_id === null) {
                $parent_id = DB::table('place_location')
                    ->whereNull('parent_id')
                    ->where('place', '=', mb_substr($place, 0, 120))
                    ->value('id');
            } else {
                $parent_id = DB::table('place_location')
                    ->where('parent_id', '=', $parent_id)
                    ->where('place', '=', mb_substr($place, 0, 120))
                    ->value('id');
            }

            if ($parent_id === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return object
     */
    private function details(): object
    {
        return Registry::cache()->array()->remember('location-details-' . $this->id(), function () {
            // The "top-level" location won't exist in the database.
            if ($this->parts->isEmpty()) {
                return (object) [
                    'latitude'  => null,
                    'longitude' => null,
                ];
            }

            $row = DB::table('place_location')
                ->where('id', '=', $this->id())
                ->select(['latitude', 'longitude'])
                ->first();

            if ($row->latitude !== null) {
                $row->latitude = (float) $row->latitude;
            }

            if ($row->longitude !== null) {
                $row->longitude = (float) $row->longitude;
            }

            return $row;
        });
    }

    /**
     * Latitude of the location.
     */
    public function latitude(): float|null
    {
        return $this->details()->latitude;
    }

    /**
     * Longitude of the location.
     */
    public function longitude(): float|null
    {
        return $this->details()->longitude;
    }

    /**
     * @return string
     */
    public function locationName(): string
    {
        return (string) $this->parts->first();
    }

    /**
     * Find a rectangle that (approximately) encloses this place.
     *
     * @return array<array<float>>
     */
    public function boundingRectangle(): array
    {
        if ($this->id() === null) {
            return [[-180.0, -90.0], [180.0, 90.0]];
        }

        // Find our own co-ordinates and those of any child places
        $latitudes = DB::table('place_location')
            ->whereNotNull('latitude')
            ->where(function (Builder $query): void {
                $query
                    ->where('parent_id', '=', $this->id())
                    ->orWhere('id', '=', $this->id());
            })
            ->groupBy(['latitude'])
            ->pluck('latitude')
            ->map(static fn (string $x): float => (float) $x);

        $longitudes = DB::table('place_location')
            ->whereNotNull('longitude')
            ->where(function (Builder $query): void {
                $query
                    ->where('parent_id', '=', $this->id())
                    ->orWhere('id', '=', $this->id());
            })
            ->groupBy(['longitude'])
            ->pluck('longitude')
            ->map(static fn (string $x): float => (float) $x);

        // No co-ordinates?  Use the parent place instead.
        if ($latitudes->isEmpty() || $longitudes->isEmpty()) {
            return $this->parent()->boundingRectangle();
        }

        // Many co-ordinates?  Generate a bounding rectangle that includes them.
        if ($latitudes->count() > 1 || $longitudes->count() > 1) {
            return [[$latitudes->min(), $longitudes->min()], [$latitudes->max(), $longitudes->max()]];
        }

        // Just one co-ordinate?  Draw a box around it.
        switch ($this->parts->count()) {
            case 1:
                // Countries
                $delta = 5.0;
                break;
            case 2:
                // Regions
                $delta = 1.0;
                break;
            default:
                // Cities and districts
                $delta = 0.2;
                break;
        }

        return [[
            max($latitudes->min() - $delta, -90.0),
            max($longitudes->min() - $delta, -180.0),
        ], [
            min($latitudes->max() + $delta, 90.0),
            min($longitudes->max() + $delta, 180.0),
        ]];
    }
}
