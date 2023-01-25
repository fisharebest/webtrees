<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

/**
 * A repository providing methods for server related statistics.
 */
interface ServerRepositoryInterface
{
    /**
     * What is the current date on the server?
     *
     * @return string
     */
    public function serverDate(): string;

    /**
     * What is the current time on the server (in 12 hour clock)?
     *
     * @return string
     */
    public function serverTime(): string;

    /**
     * What is the current time on the server (in 24 hour clock)?
     *
     * @return string
     */
    public function serverTime24(): string;

    /**
     * What is the timezone of the server.
     *
     * @return string
     */
    public function serverTimezone(): string;
}
