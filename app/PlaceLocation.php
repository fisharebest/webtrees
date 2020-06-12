<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Services\GedcomService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;

use function app;
use function max;
use function min;
use function preg_split;

/**
 * Class PlaceLocation
 */
class PlaceLocation
{
    /** @var string e.g. "Westminster, London, England" */
    private $location_name;

    /** @var Collection|string[] The parts of a location name, e.g. ["Westminster", "London", "England"] */
    private $parts;

    /**
     * Create a place-location.
     *
     * @param string $location_name
     */
    public function __construct(string $location_name)
    {
        // Ignore any empty parts in location names such as "Village, , , Country".
        $this->parts = (new Collection(preg_split(Gedcom::PLACE_SEPARATOR_REGEX, $location_name)))
            ->filter();

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
     * The database row that contains this location.
     * Note that due to database collation, both "Quebec" and "Québec" will share the same row.
     *
     * @return int
     */
    public function id(): int
    {
        return app('cache.array')->remember('location-' . $this->location_name, function () {
            // The "top-level" location won't exist in the database.
            if ($this->parts->isEmpty()) {
                return 0;
            }

            $parent_location_id = $this->parent()->id();

            $location_id = (int) DB::table('placelocation')
                ->where('pl_place', '=', $this->parts->first())
                ->where('pl_parent_id', '=', $parent_location_id)
                ->value('pl_id');

            if ($location_id === 0) {
                $location = $this->parts->first();

                $location_id = 1 + (int) DB::table('placelocation')->max('pl_id');

                DB::table('placelocation')->insert([
                    'pl_id'        => $location_id,
                    'pl_place'     => $location,
                    'pl_parent_id' => $parent_location_id,
                    'pl_level'     => $this->parts->count() - 1,
                    'pl_lati'      => '',
                    'pl_long'      => '',
                    'pl_icon'      => '',
                    'pl_zoom'      => 2,
                ]);
            }

            return $location_id;
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
        $location_id = 0;

        $this->parts->reverse()->each(static function (string $place) use (&$location_id) {
            if ($location_id !== null) {
                $location_id = DB::table('placelocation')
                    ->where('pl_parent_id', '=', $location_id)
                    ->where('pl_place', '=', $place)
                    ->value('pl_id');
            }
        });

        return $location_id !== null;
    }

    /**
     * @return stdClass
     */
    private function details(): stdClass
    {
        return app('cache.array')->remember('location-details-' . $this->id(), function () {
            // The "top-level" location won't exist in the database.
            if ($this->parts->isEmpty()) {
                return (object) [
                    'pl_id'        => '0',
                    'pl_parent_id' => '0',
                    'pl_level' => null,
                    'pl_place' => '',
                    'pl_lati' => null,
                    'pl_long' => null,
                    'pl_zoom' => null,
                    'pl_icon' => null,
                    'pl_media' => null,
                ];
            }

            return DB::table('placelocation')
                ->where('pl_id', '=', $this->id())
                ->first();
        });
    }

    /**
     * Latitude of the location.
     *
     * @return float
     */
    public function latitude(): float
    {
        $gedcom_service = new GedcomService();

        $tmp = $this;
        do {
            $pl_lati = (string) $tmp->details()->pl_lati;
            $tmp = $tmp->parent();
        } while ($pl_lati === '' && $tmp->id() !== 0);

        return $gedcom_service->readLatitude($pl_lati);
    }

    /**
     * Latitude of the longitude.
     *
     * @return float
     */
    public function longitude(): float
    {
        $gedcom_service = new GedcomService();

        $tmp = $this;
        do {
            $pl_long = (string) $tmp->details()->pl_long;
            $tmp = $tmp->parent();
        } while ($pl_long === '' && $tmp->id() !== 0);

        return $gedcom_service->readLongitude($pl_long);
    }

    /**
     * The icon for the location.
     *
     * @return string
     */
    public function icon(): string
    {
        return (string) $this->details()->pl_icon;
    }

    /**
     * Zoom level for the location.
     *
     * @return int
     */
    public function zoom(): int
    {
        return (int) $this->details()->pl_zoom ?: 2;
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
        if ($this->id() === 0) {
            return [[-180.0, -90.0], [180.0, 90.0]];
        }

        // Find our own co-ordinates and those of any child places
        $latitudes = DB::table('placelocation')
            ->where('pl_parent_id', '=', $this->id())
            ->orWhere('pl_id', '=', $this->id())
            ->pluck('pl_lati')
            ->filter()
            ->map(static function (string $x): float {
                return (new GedcomService())->readLatitude($x);
            });

        $longitudes = DB::table('placelocation')
            ->where('pl_parent_id', '=', $this->id())
            ->orWhere('pl_id', '=', $this->id())
            ->pluck('pl_long')
            ->filter()
            ->map(static function (string $x): float {
                return (new GedcomService())->readLongitude($x);
            });

        if ($latitudes->count() > 1 || $longitudes->count() > 1) {
            return [[$latitudes->min(), $longitudes->min()], [$latitudes->max(), $longitudes->max()]];
        }

        return $this->parent()->boundingRectangle();
    }
}
