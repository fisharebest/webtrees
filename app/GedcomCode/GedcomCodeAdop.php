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

namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Webtrees\I18N;

/**
 * Class GedcomCodeAdop - Functions and logic for GEDCOM "ADOP" codes
 */
class GedcomCodeAdop
{
    /**
     * Translate a code, for an (optional) record
     *
     * @param string $type
     *
     * @return string
     */
    public static function getValue(string $type): string
    {
        return self::getValues()[$type] ?? e($type);
    }

    /**
     * A list of all possible values for PEDI
     *
     * @return array<string>
     */
    public static function getValues(): array
    {
        return [
            'BOTH' => I18N::translate('Adopted by both parents'),
            'HUSB' => I18N::translate('Adopted by father'),
            'WIFE' => I18N::translate('Adopted by mother'),
        ];
    }
}
