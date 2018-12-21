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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

/**
 * Statistics submodule providing all USER related methods.
 */
interface UserRepositoryInterface
{
    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedIn(): string;

    /**
     * Who is currently logged in?
     *
     * @return string
     */
    public function usersLoggedInList(): string;

    /**
     * Who is currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotal(): int;

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalAnon(): int;

    /**
     * Which visitors are currently logged in?
     *
     * @return int
     */
    public function usersLoggedInTotalVisible(): int;

    /**
     * Get the current user's ID.
     *
     * @return string
     */
    public function userId(): string;

    /**
     * Get the current user's username.
     *
     * @param string $visitor_text
     *
     * @return string
     */
    public function userName(string $visitor_text = ''): string;

    /**
     * Get the current user's full name.
     *
     * @return string
     */
    public function userFullName(): string;
}
