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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class GedcomCodePedi - Functions and logic for GEDCOM "PEDI" codes
 */
class GedcomCodePedi
{
    // Possible values for pedigree field
    private const TYPES = [
        'adopted',
        'birth',
        'foster',
        'rada',
        'sealing',
    ];

    /**
     * Translate a code, for an optional record
     *
     * @param string            $type
     * @param GedcomRecord|null $record
     *
     * @return string
     */
    public static function getValue(string $type, GedcomRecord $record = null): string
    {
        if ($record instanceof Individual) {
            $sex = $record->sex();
        } else {
            $sex = 'U';
        }

        switch ($type) {
            case 'birth':
                if ($sex === 'M') {
                    return I18N::translateContext('Male pedigree', 'Birth');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('Female pedigree', 'Birth');
                }

                return I18N::translateContext('Pedigree', 'Birth');

            case 'adopted':
                if ($sex === 'M') {
                    return I18N::translateContext('Male pedigree', 'Adopted');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('Female pedigree', 'Adopted');
                }

                return I18N::translateContext('Pedigree', 'Adopted');

            case 'foster':
                if ($sex === 'M') {
                    return I18N::translateContext('Male pedigree', 'Foster');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('Female pedigree', 'Foster');
                }

                return I18N::translateContext('Pedigree', 'Foster');

            case 'sealing':
                if ($sex === 'M') {
                    /* I18N: “sealing” is a ceremony in the Mormon church. */
                    return I18N::translateContext('Male pedigree', 'Sealing');
                }

                if ($sex === 'F') {
                    /* I18N: “sealing” is a ceremony in the Mormon church. */
                    return I18N::translateContext('Female pedigree', 'Sealing');
                }

                /* I18N: “sealing” is a ceremony in the Mormon church. */
                return I18N::translateContext('Pedigree', 'Sealing');

            case 'rada':
                // Not standard GEDCOM - a webtrees extension
                // This is an arabic word which does not exist in other languages.
                // So, it will not have any inflected forms.
                /* I18N: This is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                return I18N::translate('Rada');
            default:
                return $type;
        }
    }

    /**
     * A list of all possible values for PEDI
     *
     * @param GedcomRecord|null $record
     *
     * @return array<string>
     */
    public static function getValues(GedcomRecord $record = null): array
    {
        $values = ['' => ''];
        foreach (self::TYPES as $type) {
            $values[$type] = self::getValue($type, $record);
        }
        uasort($values, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $values;
    }

    /**
     * A label for a parental family group
     *
     * @param string $pedi
     *
     * @return string
     */
    public static function getChildFamilyLabel(string $pedi): string
    {
        switch ($pedi) {
            case '':
            case 'birth':
                return I18N::translate('Family with parents');
            case 'adopted':
                return I18N::translate('Family with adoptive parents');
            case 'foster':
                return I18N::translate('Family with foster parents');
            case 'sealing':
                /* I18N: “sealing” is a Mormon ceremony. */
                return I18N::translate('Family with sealing parents');
            case 'rada':
                /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                return I18N::translate('Family with rada parents');
            default:
                return I18N::translate('Family with parents') . ' - ' . $pedi;
        }
    }

    /**
     * Create GEDCOM for a new child-family pedigree
     *
     * @param string $pedi
     * @param string $xref
     *
     * @return string
     */
    public static function createNewFamcPedi(string $pedi, string $xref): string
    {
        switch ($pedi) {
            case '':
                return "1 FAMC @$xref@";
            case 'adopted':
                return "1 FAMC @$xref@\n2 PEDI $pedi\n1 ADOP\n2 FAMC @$xref@\n3 ADOP BOTH";
            case 'sealing':
                return "1 FAMC @$xref@\n2 PEDI $pedi\n1 SLGC\n2 FAMC @$xref@";
            case 'foster':
                return "1 FAMC @$xref@\n2 PEDI $pedi\n1 EVEN\n2 TYPE $pedi";
            default:
                return "1 FAMC @$xref@\n2 PEDI $pedi";
        }
    }
}
