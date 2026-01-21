<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

/**
 * CustomBooleanFact, leniently accepting values yes and no, true and false, 1 and 0, in uppercase or lowercase.
 */
class CustomBooleanFact extends AbstractEventElement
{
    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = strtoupper(parent::canonical($value));
        if (in_array($value, ['YES', 'TRUE', '1'])) {
            return 'Y';
        }
        if (in_array($value, ['NO', 'FALSE', '0'])) {
            return 'N';
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
            'Y' => I18N::translate('yes'),
            'N' => I18N::translate('no'),
        ];
    }
}
