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
 * Class GedcomCodeRela - Functions and logic for GEDCOM "RELA" codes
 */
class GedcomCodeRela
{
    // List of possible values for the RELA tag
    private const TYPES = [
        'attendant',
        'attending',
        'best_man',
        'bridesmaid',
        'buyer',
        'circumciser',
        'civil_registrar',
        'employee',
        'employer',
        'foster_child',
        'foster_father',
        'foster_mother',
        'friend',
        'godfather',
        'godmother',
        'godparent',
        'godson',
        'goddaughter',
        'godchild',
        'guardian',
        'informant',
        'lodger',
        'nanny',
        'nurse',
        'owner',
        'priest',
        'rabbi',
        'registry_officer',
        'seller',
        'servant',
        'slave',
        'ward',
        'witness',
    ];

    /**
     * Translate a code, for an (optional) record.
     * We need the record to translate the sex (godfather/godmother) but
     * we won’t have this when adding data for new individuals.
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
            case 'attendant':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Attendant');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Attendant');
                }

                return I18N::translate('Attendant');

            case 'attending':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Attending');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Attending');
                }

                return I18N::translate('Attending');

            case 'best_man':
                // always male
                return I18N::translate('Best man');

            case 'bridesmaid':
                // always female
                return I18N::translate('Bridesmaid');

            case 'buyer':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Buyer');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Buyer');
                }

                return I18N::translate('Buyer');

            case 'circumciser':
                // always male
                return I18N::translate('Circumciser');

            case 'civil_registrar':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Civil registrar');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Civil registrar');
                }

                return I18N::translate('Civil registrar');

            case 'employee':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Employee');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Employee');
                }

                return I18N::translate('Employee');

            case 'employer':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Employer');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Employer');
                }

                return I18N::translate('Employer');

            case 'foster_child':
                // no sex implied
                return I18N::translate('Foster child');

            case 'foster_father':
                // always male
                return I18N::translate('Foster father');

            case 'foster_mother':
                // always female
                return I18N::translate('Foster mother');

            case 'friend':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Friend');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Friend');
                }

                return I18N::translate('Friend');

            case 'godfather':
                // always male
                return I18N::translate('Godfather');

            case 'godmother':
                // always female
                return I18N::translate('Godmother');

            case 'godparent':
                if ($sex === 'M') {
                    return I18N::translate('Godfather');
                }

                if ($sex === 'F') {
                    return I18N::translate('Godmother');
                }

                return I18N::translate('Godparent');

            case 'godson':
                // always male
                return I18N::translate('Godson');

            case 'goddaughter':
                // always female
                return I18N::translate('Goddaughter');

            case 'godchild':
                if ($sex === 'M') {
                    return I18N::translate('Godson');
                }

                if ($sex === 'F') {
                    return I18N::translate('Goddaughter');
                }

                return I18N::translate('Godchild');

            case 'guardian':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Guardian');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Guardian');
                }

                return I18N::translate('Guardian');

            case 'informant':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Informant');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Informant');
                }

                return I18N::translate('Informant');

            case 'lodger':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Lodger');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Lodger');
                }

                return I18N::translate('Lodger');

            case 'nanny':
                // no sex implied
                return I18N::translate('Nanny');

            case 'nurse':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Nurse');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Nurse');
                }

                return I18N::translate('Nurse');

            case 'owner':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Owner');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Owner');
                }

                return I18N::translate('Owner');

            case 'priest':
                // no sex implied
                return I18N::translate('Priest');

            case 'rabbi':
                // always male
                return I18N::translate('Rabbi');

            case 'registry_officer':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Registry officer');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Registry officer');
                }

                return I18N::translate('Registry officer');

            case 'seller':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Seller');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Seller');
                }

                return I18N::translate('Seller');

            case 'servant':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Servant');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Servant');
                }

                return I18N::translate('Servant');

            case 'slave':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Slave');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Slave');
                }

                return I18N::translate('Slave');

            case 'ward':
                if ($sex === 'M') {
                    return I18N::translateContext('MALE', 'Ward');
                }

                if ($sex === 'F') {
                    return I18N::translateContext('FEMALE', 'Ward');
                }

                return I18N::translate('Ward');

            case 'witness':
                // Do we need separate male/female translations for this?
                return I18N::translate('Witness');

            default:
                return I18N::translate($type);
        }
    }

    /**
     * A list of all possible values for RELA
     *
     * @param GedcomRecord|null $record
     *
     * @return array<string>
     */
    public static function getValues(GedcomRecord $record = null): array
    {
        $values = [];
        foreach (self::TYPES as $type) {
            $values[$type] = self::getValue($type, $record);
        }
        uasort($values, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $values;
    }
}
