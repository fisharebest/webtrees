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

/**
 * PEDIGREE_LINKAGE_TYPE := {Size=5:7}
 * [ adopted | birth | foster | sealing ]
 * A code used to indicate the child to family relationship for pedigree navigation purposes.
 * Where:
 * adopted = indicates adoptive parents.
 * birth   = indicates birth parents.
 * foster  = indicates child was included in a foster or guardian family.
 * sealing = indicates child was sealed to parents other than birth parents.
 */
class PedigreeLinkageType extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 7;

    /**
     * A list of controlled values for this element
     *
     * @param string $sex - the text depends on the sex of the individual
     *
     * @return array<int|string,string>
     */
    public function values(string $sex = 'U'): array
    {
        $values = [
            'M' => [
                ''        => '',
                'birth'   => I18N::translateContext('Male pedigree', 'Birth'),
                'adopted' => I18N::translateContext('Male pedigree', 'Adopted'),
                'foster'  => I18N::translateContext('Male pedigree', 'Foster'),
                'sealing' => /* I18N: “sealing” is a Mormon ceremony. */
                    I18N::translateContext('Male pedigree', 'Sealing'),
                'rada'    => /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                    I18N::translateContext('Male pedigree', 'Rada'),
            ],
            'F' => [
                ''        => '',
                'birth'   => I18N::translateContext('Female pedigree', 'Birth'),
                'adopted' => I18N::translateContext('Female pedigree', 'Adopted'),
                'foster'  => I18N::translateContext('Female pedigree', 'Foster'),
                'sealing' => /* I18N: “sealing” is a Mormon ceremony. */
                    I18N::translateContext('Female pedigree', 'Sealing'),
                'rada'    => /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                    I18N::translateContext('Female pedigree', 'Rada'),
            ],
            'U' => [
                ''        => '',
                'birth'   => I18N::translateContext('Pedigree', 'Birth'),
                'adopted' => I18N::translateContext('Pedigree', 'Adopted'),
                'foster'  => I18N::translateContext('Pedigree', 'Foster'),
                'sealing' => /* I18N: “sealing” is a Mormon ceremony. */
                    I18N::translateContext('Pedigree', 'Sealing'),
                'rada'    => /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                    I18N::translateContext('Pedigree', 'Rada'),
            ],
        ];

        return $values[$sex] ?? $values['U'];
    }
}
