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
 * Class GedcomCodeName - Functions and logic for GEDCOM "NAME" codes
 */
class GedcomCodeName
{
    // A list of possible types of name
    private const TYPES = [
        'adopted',
        'aka',
        'birth',
        'change',
        'estate',
        'immigrant',
        'maiden',
        'married',
        'religious',
    ];

    /**
     * Translate a code, for an (optional) record
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
            case 'adopted':
                if ($sex === 'M') {
                    /* I18N: The name given to a child by its adoptive parents */
                    return I18N::translateContext('MALE', 'adopted name');
                }

                if ($sex === 'F') {
                    /* I18N: The name given to a child by its adoptive parents */
                    return I18N::translateContext('FEMALE', 'adopted name');
                }

                /* I18N: The name given to a child by its adoptive parents */
                return I18N::translate('adopted name');

            case 'aka':
                if ($sex === 'M') {
                    /* I18N: The name by which an individual is also known. e.g. a professional name or a stage name */
                    return I18N::translateContext('MALE', 'also known as');
                }

                if ($sex === 'F') {
                    /* I18N: The name by which an individual is also known. e.g. a professional name or a stage name */
                    return I18N::translateContext('FEMALE', 'also known as');
                }

                /* I18N: The name by which an individual is also known. e.g. a professional name or a stage name */
                return I18N::translate('also known as');

            case 'birth':
                if ($sex === 'M') {
                    /* I18N: The name given to an individual at their birth */
                    return I18N::translateContext('MALE', 'birth name');
                }

                if ($sex === 'F') {
                    /* I18N: The name given to an individual at their birth */
                    return I18N::translateContext('FEMALE', 'birth name');
                }

                /* I18N: The name given to an individual at their birth */
                return I18N::translate('birth name');

            case 'change':
                if ($sex === 'M') {
                    /* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
                    return I18N::translateContext('MALE', 'change of name');
                }

                if ($sex === 'F') {
                    /* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
                    return I18N::translateContext('FEMALE', 'change of name');
                }

                /* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
                return I18N::translate('change of name');

            case 'estate':
                if ($sex === 'M') {
                    /* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
                    return I18N::translateContext('MALE', 'estate name');
                }

                if ($sex === 'F') {
                    /* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
                    return I18N::translateContext('FEMALE', 'estate name');
                }

                /* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
                return I18N::translate('estate name');

            case 'immigrant':
                if ($sex === 'M') {
                    /* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
                    return I18N::translateContext('MALE', 'immigration name');
                }

                if ($sex === 'F') {
                    /* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
                    return I18N::translateContext('FEMALE', 'immigration name');
                }

                /* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
                return I18N::translate('immigration name');

            case 'maiden':
                // Only women have “maiden” names!
                /* I18N: A woman’s name, before she marries (in cultures where women take their new husband’s name on marriage) */
                return I18N::translate('maiden name');

            case 'married':
                if ($sex === 'M') {
                    /* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
                    return I18N::translateContext('MALE', 'married name');
                }

                if ($sex === 'F') {
                    /* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
                    return I18N::translateContext('FEMALE', 'married name');
                }

                /* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
                return I18N::translate('married name');

            case 'religious':
                if ($sex === 'M') {
                    /* I18N: A name taken when entering a religion or a religious order */
                    return I18N::translateContext('MALE', 'religious name');
                }

                if ($sex === 'F') {
                    /* I18N: A name taken when entering a religion or a religious order */
                    return I18N::translateContext('FEMALE', 'religious name');
                }

                /* I18N: A name taken when entering a religion or a religious order */
                return I18N::translate('religious name');

            default:
                return $type;
        }
    }

    /**
     * A list of all possible values for NAME types
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
}
