<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use function mb_strlen;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;

/**
 * @see https://en.wikipedia.org/wiki/Maidenhead_Locator_System
 */
class MaidenheadLocator extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 8;
    protected const PATTERN        = '[A-Ra-r][A-Ra-r]([0-9][0-9]([A-Xa-x][A-Xa-x]([0-9][0-9])?)?)?';

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = parent::canonical($value);

        // Characters 1 and 2 are upper case
        if (mb_strlen($value) >= 2) {
            $value = mb_strtoupper(mb_substr($value, 0, 2)) . mb_substr($value, 2);
        }

        // Characters 5 and 6 are lower case
        if (mb_strlen($value) >= 6) {
            $value = mb_substr($value, 0, 4) . mb_strtolower(mb_substr($value, 4, 2)) . mb_substr($value, 6);
        }

        return $value;
    }
}
