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

use Fisharebest\Webtrees\I18N;

use function array_key_exists;
use function strtoupper;

/**
 * For Gedcom-L
 * Programs with internal data fields "not married" or "never married" or a data field
 * "Status", should introduce a user-defined tag _STAT directly below of FAM:
 * _STAT can have the following values:
 * <STATUS_TEXT>:= [NOT MARRIED | NEVER MARRIED | UNKNOWN |<plain text of the user>]
 */
class FamilyStatusText extends AbstractElement
{
    protected const SUBTAGS = [
        'DATE' => '0:1',
        'PLAC' => '0:1',
        'NOTE' => '0:M',
        'SOUR' => '0:M',
    ];

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
        $upper = strtoupper($value);

        if (array_key_exists($upper, $this->values())) {
            return $upper;
        }

        return $value;
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            ''              => '',
            'NOT MARRIED'   => I18N::translate('Not married'),
            'NEVER MARRIED' => I18N::translate('Never married'),
            'UNKNOWN'       => I18N::translate('Unknown'),
        ];
    }
}
