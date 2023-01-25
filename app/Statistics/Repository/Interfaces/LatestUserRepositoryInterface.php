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
 * A repository providing methods for latest user related statistics.
 */
interface LatestUserRepositoryInterface
{
    /**
     * Get the newest registered user's ID.
     *
     * @return string
     */
    public function latestUserId(): string;

    /**
     * Get the newest registered user's username.
     *
     * @return string
     */
    public function latestUserName(): string;

    /**
     * Get the newest registered user's real name.
     *
     * @return string
     */
    public function latestUserFullName(): string;

    /**
     * Get the date of the newest user registration.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegDate(string $format = null): string;

    /**
     * Find the timestamp of the latest user to register.
     *
     * @param string|null $format
     *
     * @return string
     */
    public function latestUserRegTime(string $format = null): string;

    /**
     * Is the most recently registered user logged in right now?
     *
     * @param string|null $yes
     * @param string|null $no
     *
     * @return string
     */
    public function latestUserLoggedin(string $yes = null, string $no = null): string;
}
