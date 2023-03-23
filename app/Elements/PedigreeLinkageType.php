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

    public const VALUE_ADOPTED = 'ADOPTED';
    public const VALUE_BIRTH   = 'BIRTH';
    public const VALUE_FOSTER  = 'FOSTER';
    public const VALUE_SEALING = 'SEALING';
    public const VALUE_RADA    = 'RADA';

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
     * @param string $sex the text depends on the sex of the individual
     *
     * @return array<int|string,string>
     */
    public function values(string $sex = 'U'): array
    {
        $values = [
            'M' => [
                ''                  => '',
                self::VALUE_BIRTH   => I18N::translateContext('Male pedigree', 'Birth'),
                self::VALUE_ADOPTED => I18N::translateContext('Male pedigree', 'Adopted'),
                self::VALUE_FOSTER  => I18N::translateContext('Male pedigree', 'Foster'),
                /* I18N: “sealing” is a Mormon ceremony. */
                self::VALUE_SEALING => I18N::translateContext('Male pedigree', 'Sealing'),
                /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                self::VALUE_RADA    => I18N::translateContext('Male pedigree', 'Rada'),
            ],
            'F' => [
                ''                  => '',
                self::VALUE_BIRTH   => I18N::translateContext('Female pedigree', 'Birth'),
                self::VALUE_ADOPTED => I18N::translateContext('Female pedigree', 'Adopted'),
                self::VALUE_FOSTER  => I18N::translateContext('Female pedigree', 'Foster'),
                /* I18N: “sealing” is a Mormon ceremony. */
                self::VALUE_SEALING => I18N::translateContext('Female pedigree', 'Sealing'),
                /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                self::VALUE_RADA    => I18N::translateContext('Female pedigree', 'Rada'),
            ],
            'U' => [
                ''                  => '',
                self::VALUE_BIRTH   => I18N::translateContext('Pedigree', 'Birth'),
                self::VALUE_ADOPTED => I18N::translateContext('Pedigree', 'Adopted'),
                self::VALUE_FOSTER  => I18N::translateContext('Pedigree', 'Foster'),
                /* I18N: “sealing” is a Mormon ceremony. */
                self::VALUE_SEALING => I18N::translateContext('Pedigree', 'Sealing'),
                /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                self::VALUE_RADA    => I18N::translateContext('Pedigree', 'Rada'),
            ],
        ];

        return $values[$sex] ?? $values['U'];
    }
}
