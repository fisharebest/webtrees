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
 * Heredis custom tag INDI:_FIL - Child status
 */
class HeredisFIL extends AbstractElement
{

    private const string VALUE_LEGITIMATE_CHILD     = 'LEGITIMATE_CHILD';
    private const string VALUE_NATURAL_CHILD        = 'NATURAL_CHILD';
    private const string VALUE_RECOGNIZED_CHILD     = 'RECOGNIZED_CHILD';
    private const string VALUE_LEGITIMIZED_CHILD    = 'LEGITIMIZED_CHILD';
    private const string VALUE_CHILD_FOUND          = 'CHILD_FOUND';
    private const string VALUE_ADOPTED_CHILD        = 'ADOPTED_CHILD';
    private const string VALUE_ADULTEROUS_CHILD     = 'ADULTEROUS_CHILD';
    private const string VALUE_STILLBORN_CHILD      = 'STILLBORN_CHILD';
    private const string VALUE_RELATIONSHIP_UNKNOWN = 'RELATIONSHIP_UNKNOW';

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(string $sex = 'U'): array
    {
        $values = [
            'M' => [
                self::VALUE_LEGITIMATE_CHILD     => I18N::translate('Legitimate'),
                self::VALUE_NATURAL_CHILD        => I18N::translate('Natural'),
                self::VALUE_RECOGNIZED_CHILD     => I18N::translate('Legally recognised'),
                self::VALUE_LEGITIMIZED_CHILD    => I18N::translate('Legitimated'),
                self::VALUE_CHILD_FOUND          => I18N::translate('Foundling'),
                self::VALUE_ADOPTED_CHILD        => I18N::translateContext('Male pedigree', 'Adopted'),
                self::VALUE_ADULTEROUS_CHILD     => I18N::translate('Illegitimate'),
                self::VALUE_STILLBORN_CHILD      => I18N::translate('stillborn'),
                self::VALUE_RELATIONSHIP_UNKNOWN => I18N::translate('Unknown'),
            ],
            'F' => [
                self::VALUE_LEGITIMATE_CHILD     => I18N::translate('Legitimate'),
                self::VALUE_NATURAL_CHILD        => I18N::translate('Natural'),
                self::VALUE_RECOGNIZED_CHILD     => I18N::translate('Legally recognised'),
                self::VALUE_LEGITIMIZED_CHILD    => I18N::translate('Legitimated'),
                self::VALUE_CHILD_FOUND          => I18N::translate('Foundling'),
                self::VALUE_ADOPTED_CHILD        => I18N::translateContext('Female pedigree', 'Adopted'),
                self::VALUE_ADULTEROUS_CHILD     => I18N::translate('Illegitimate'),
                self::VALUE_STILLBORN_CHILD      => I18N::translate('stillborn'),
                self::VALUE_RELATIONSHIP_UNKNOWN => I18N::translate('Unknown'),
            ],
            'U' => [
                self::VALUE_LEGITIMATE_CHILD     => I18N::translate('Legitimate'),
                self::VALUE_NATURAL_CHILD        => I18N::translate('Natural'),
                self::VALUE_RECOGNIZED_CHILD     => I18N::translate('Legally recognised'),
                self::VALUE_LEGITIMIZED_CHILD    => I18N::translate('Legitimated'),
                self::VALUE_CHILD_FOUND          => I18N::translate('Foundling'),
                self::VALUE_ADOPTED_CHILD        => I18N::translateContext('Pedigree', 'Adopted'),
                self::VALUE_ADULTEROUS_CHILD     => I18N::translate('Illegitimate'),
                self::VALUE_STILLBORN_CHILD      => I18N::translate('stillborn'),
                self::VALUE_RELATIONSHIP_UNKNOWN => I18N::translate('Unknown'),
            ],
        ];

        return $values[$sex] ?? $values['U'];
    }
}
