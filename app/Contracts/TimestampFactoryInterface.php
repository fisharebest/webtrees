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

namespace Fisharebest\Webtrees\Contracts;

/**
 * Create a timestamp object.
 */
interface TimestampFactoryInterface
{
    /**
     * @param int                $timestamp
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function make(int $timestamp, UserInterface|null $user = null): TimestampInterface;

    /**
     * @param string|null        $string YYYY-MM-DD HH:MM:SS (as provided by SQL).
     * @param string             $format
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function fromString(string|null $string, string $format = 'Y-m-d H:i:s', UserInterface|null $user = null): TimestampInterface;

    /**
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function now(UserInterface|null $user = null): TimestampInterface;
}
