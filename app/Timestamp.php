<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

/**
 * Timestamps
 */
class Timestamp
{
    /** @var int UNIX style timestamp */
    private $time;

    /**
     * Create a timestamp.
     *
     * @param int $time Number of seconds since 1 Jan 1970
     */
    public function __construct(int $time = WT_TIMESTAMP)
    {
        $this->time = $time;
    }

    /**
     * Convert a timezone into a date.
     *
     * @param int $tz_offset
     *
     * @return Date
     */
    public function toDate(int $tz_offset = WT_TIMESTAMP_OFFSET): Date
    {
        return new Date(strtoupper(gmdate('j M Y', $this->time + $tz_offset)));
    }
}
