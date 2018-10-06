<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class GedcomCodeAdop - Functions and logic for GEDCOM "ADOP" codes
 */
class GedcomCodeAdop
{
    /** @var string[] A list of possible adoption codes */
    private static $TYPES = [
        'BOTH',
        'HUSB',
        'WIFE',
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
            $sex = $record->getSex();
        } else {
            $sex = 'U';
        }

        switch ($type) {
            case 'BOTH':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Adopted by both parents');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Adopted by both parents');
                }

                return I18N::translate('Adopted by both parents');

            case 'HUSB':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Adopted by father');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Adopted by father');
                }

                return I18N::translate('Adopted by father');

            case 'WIFE':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Adopted by mother');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Adopted by mother');
                }

                return I18N::translate('Adopted by mother');

            default:
                return $type;
        }
    }

    /**
     * A list of all possible values for PEDI
     *
     * @param GedcomRecord|null $record
     *
     * @return string[]
     */
    public static function getValues(GedcomRecord $record = null): array
    {
        $values = [];
        foreach (self::$TYPES as $type) {
            $values[$type] = self::getValue($type, $record);
        }

        // Don't sort these. We want the order: both parents, father, mother
        return $values;
    }
}
