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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

use function strtoupper;

/**
 * ADDRESS_WEB_PAGE := {Size=1:4}
 * [ HUSB | WIFE | BOTH ]
 * A code which shows which parent in the associated family record adopted this person. Where:
 * HUSB = The HUSBand in the associated family adopted this person.
 * WIFE = The WIFE in the associated family adopted this person.
 * BOTH = Both HUSBand and WIFE adopted this person.
 */
class AdoptedByWhichParent extends AbstractElement
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
        return [
            ''     => '',
            'BOTH' => I18N::translate('Adopted by both parents'),
            'HUSB' => I18N::translate('Adopted by father'),
            'WIFE' => I18N::translate('Adopted by mother'),
        ];
    }
}
