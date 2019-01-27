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

namespace Fisharebest\Webtrees;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

/**
 * Class Location
 *
 * @package Fisharebest\Webtrees
 */
class Location
{

    /**
     * @var stdClass $record
     */
    protected $record;

    /**
     * Location constructor.
     *
     * @param string $gedcomName
     * @param array  $record
     */
    public function __construct($gedcomName, $record = [])
    {
        $tmp = $this->getRecordFromName($gedcomName);
        if ($tmp !== null) {
            $this->record = $tmp;
        } elseif (!empty($record)) {
            $this->record = (object) $record;
        } else {
            $this->record = (object) [
                'fqpn'         => '',
                'pl_id'        => 0,
                'pl_parent_id' => 0,
                'pl_level'     => null,
                'pl_place'     => '',
                'pl_long'      => null,
                'pl_lati'      => null,
                'pl_zoom'      => null,
                'pl_icon'      => null,
            ];
        }
    }

    /**
     * @return bool
     */
    public function knownLatLon(): bool
    {
        return ($this->record->pl_lati && $this->record->pl_long);
    }

    /**
     * @param string $format
     *
     * @return string|float
     */
    public function getLat($format = 'signed')
    {
        switch ($format) {
            case 'signed':
                return $this->record->pl_lati ?
                    (float) strtr($this->record->pl_lati, [
                        'N' => '',
                        'S' => '-',
                        ',' => '.',
                    ]) : $this->record->pl_lati;
            default:
                return $this->record->pl_lati;
        }
    }

    /**
     * @param string $format
     *
     * @return string|float
     */
    public function getLon($format = 'signed')
    {
        switch ($format) {
            case 'signed':
                return $this->record->pl_long ?
                    (float) strtr($this->record->pl_long, [
                        'E' => '',
                        'W' => '-',
                        ',' => '.',
                    ]) : $this->record->pl_long;
            default:
                return $this->record->pl_long;
        }
    }

    /**
     * @return array
     */
    public function getLatLonJSArray(): array
    {
        return [
            $this->getLat('signed'),
            $this->getLon('signed'),
        ];
    }

    /**
     * GeoJSON requires the parameters to be in the order longitude, latitude
     *
     * @return array
     */
    public function getGeoJsonCoords(): array
    {
        return [
            $this->getLon('signed'),
            $this->getLat('signed'),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->record->pl_id;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->record->pl_level;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->record->pl_id !== 0;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->record->pl_place;
    }

    /**
     * @return string|null
     */
    public function getZoom()
    {
        return $this->record->pl_zoom;
    }

    /**
     * @return string|null
     */
    public function getIcon()
    {
        return $this->record->pl_icon;
    }

    /**
     * @return stdClass
     */
    public function getRecord(): stdClass
    {
        return $this->record;
    }

    /**
     * @return int
     */
    public function add(): int
    {
        $this->record->pl_id = 1 + (int) DB::table('placelocation')->max('pl_id');

        DB::table('placelocation')->insert([
            'pl_id'        => $this->record->pl_id,
            'pl_parent_id' => $this->record->pl_parent_id,
            'pl_level'     => $this->record->pl_level,
            'pl_place'     => $this->record->pl_place,
            'pl_long'      => $this->record->pl_long ?? null,
            'pl_lati'      => $this->record->pl_lati ?? null,
            'pl_zoom'      => $this->record->pl_zoom ?? null,
            'pl_icon'      => $this->record->pl_icon ?? null,
        ]);

        return $this->record->pl_id;
    }

    /**
     * @param stdClass $new_data
     *
     * @return void
     */
    public function update(stdClass $new_data)
    {
        DB::table('placelocation')
            ->where('pl_id', '=', $this->record->pl_id)
            ->update([
                'pl_lati' => $new_data->pl_lati ?? $this->record->pl_lati,
                'pl_long' => $new_data->pl_long ?? $this->record->pl_long,
                'pl_zoom' => $new_data->pl_zoom ?? $this->record->pl_zoom,
                'pl_icon' => $new_data->pl_icon ?? $this->record->pl_icon,
            ]);
    }

    /**
     * @param string $gedcomName
     *
     * @return null|stdClass
     */
    private function getRecordFromName(string $gedcomName)
    {
        $places = explode(Gedcom::PLACE_SEPARATOR, $gedcomName);

        $query = DB::table('placelocation AS pl0')
            ->where('pl0.pl_place', '=', $places[0])
            ->select(['pl0.*']);

        array_shift($places);

        foreach ($places as $n => $place) {
            $query->join('placelocation AS pl' . ($n + 1), function (JoinClause $join) use ($n, $place): void {
                $join
                    ->on('pl' . ($n + 1) . '.pl_id', '=', 'pl' . $n . '.pl_parent_id')
                    ->where('pl' . ($n + 1) . '.pl_place', '=', $place);
            });
        };

        return $query->first();
    }
}
