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

namespace Fisharebest\Webtrees\Contracts;

/**
 * Create a unique identifier.
 */
interface IdFactoryInterface
{
    /**
     * @return string
     */
    public function uuid(): string;

    /**
     * An identifier for use in CSS/HTML
     *
     * @param string $prefix
     *
     * @return string
     */
    public function id(string $prefix = 'id-'): string;

    /**
     * A value for _UID fields, as created by PAF
     *
     * @return string
     */
    public function pafUid(): string;

    /**
     * @param string $uid - exactly 32 hex characters
     *
     * @return string
     */
    public function pafUidChecksum(string $uid): string;
}
