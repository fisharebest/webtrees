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

use function strtoupper;

/**
 * <TYPE_OF_DEMOGRAPHICAL_DATA>:= {Size=1:35}
 * Type the demographic data for a place.
 * [ HSHO | CITI ] means: household | resident
 */
class DemographicDataType extends AbstractElement
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
        return strtoupper(parent::canonical($value));
    }


    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        $values = [
            ''     => '',
            'HSHO' => /* I18N: Type of demographic data */ I18N::translate('household'),
            'CITI' => /* I18N: Type of demographic data */ I18N::translate('citizen'),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}
