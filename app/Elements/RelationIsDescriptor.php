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
 * RELATION_IS_DESCRIPTOR := {Size=1:25}
 * A word or phrase that states object 1's relation is object 2. For example
 * you would read the following as "Joe Jacob's great grandson is the submitter
 * pointed to by the @XREF:SUBM@":
 * 0 INDI
 * 1 NAME Joe /Jacob/
 * 1 ASSO @<XREF:SUBM>@
 * 2 RELA great grandson
 */
class RelationIsDescriptor extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 25;

    /**
     * A list of controlled values for this element
     *
     * @param string $sex - the text depends on the sex of the *linked* individual
     *
     * @return array<int|string,string>
     */
    public function values(string $sex = 'U'): array
    {
        $values = [
            'M' => [
                ''                 => '',
                'attendant'        => I18N::translateContext('MALE', 'Attendant'),
                'attending'        => I18N::translateContext('MALE', 'Attending'),
                'best_man'         => I18N::translate('Best man'),
                'bridesmaid'       => I18N::translate('Bridesmaid'),
                'buyer'            => I18N::translateContext('MALE', 'Buyer'),
                'circumciser'      => I18N::translate('Circumciser'),
                'civil_registrar'  => I18N::translateContext('MALE', 'Civil registrar'),
                'employee'         => I18N::translateContext('MALE', 'Employee'),
                'employer'         => I18N::translateContext('MALE', 'Employer'),
                'foster_child'     => I18N::translate('Foster child'),
                'foster_father'    => I18N::translate('Foster father'),
                'foster_mother'    => I18N::translate('Foster mother'),
                'friend'           => I18N::translateContext('MALE', 'Friend'),
                'godfather'        => I18N::translate('Godfather'),
                'godmother'        => I18N::translate('Godmother'),
                'godparent'        => I18N::translate('Godparent'),
                'godson'           => I18N::translate('Godson'),
                'goddaughter'      => I18N::translate('Goddaughter'),
                'godchild'         => I18N::translate('Godchild'),
                'guardian'         => I18N::translateContext('MALE', 'Guardian'),
                'informant'        => I18N::translateContext('MALE', 'Informant'),
                'lodger'           => I18N::translateContext('MALE', 'Lodger'),
                'nanny'            => I18N::translate('Nanny'),
                'nurse'            => I18N::translateContext('MALE', 'Nurse'),
                'owner'            => I18N::translateContext('MALE', 'Owner'),
                'priest'           => I18N::translate('Priest'),
                'rabbi'            => I18N::translate('Rabbi'),
                'registry_officer' => I18N::translateContext('MALE', 'Registry officer'),
                'seller'           => I18N::translateContext('MALE', 'Seller'),
                'servant'          => I18N::translateContext('MALE', 'Servant'),
                'slave'            => I18N::translateContext('MALE', 'Slave'),
                'ward'             => I18N::translateContext('MALE', 'Ward'),
                'witness'          => I18N::translate('Witness'),
            ],
            'F' => [
                'attendant'        => I18N::translateContext('FEMALE', 'Attendant'),
                'attending'        => I18N::translateContext('FEMALE', 'Attending'),
                'best_man'         => I18N::translate('Best man'),
                'bridesmaid'       => I18N::translate('Bridesmaid'),
                'buyer'            => I18N::translateContext('FEMALE', 'Buyer'),
                'circumciser'      => I18N::translate('Circumciser'),
                'civil_registrar'  => I18N::translateContext('FEMALE', 'Civil registrar'),
                'employee'         => I18N::translateContext('FEMALE', 'Employee'),
                'employer'         => I18N::translateContext('FEMALE', 'Employer'),
                'foster_child'     => I18N::translate('Foster child'),
                'foster_father'    => I18N::translate('Foster father'),
                'foster_mother'    => I18N::translate('Foster mother'),
                'friend'           => I18N::translateContext('FEMALE', 'Friend'),
                'godfather'        => I18N::translate('Godfather'),
                'godmother'        => I18N::translate('Godmother'),
                'godparent'        => I18N::translate('Godparent'),
                'godson'           => I18N::translate('Godson'),
                'goddaughter'      => I18N::translate('Goddaughter'),
                'godchild'         => I18N::translate('Godchild'),
                'guardian'         => I18N::translateContext('FEMALE', 'Guardian'),
                'informant'        => I18N::translateContext('FEMALE', 'Informant'),
                'lodger'           => I18N::translateContext('FEMALE', 'Lodger'),
                'nanny'            => I18N::translate('Nanny'),
                'nurse'            => I18N::translateContext('FEMALE', 'Nurse'),
                'owner'            => I18N::translateContext('FEMALE', 'Owner'),
                'priest'           => I18N::translate('Priest'),
                'rabbi'            => I18N::translate('Rabbi'),
                'registry_officer' => I18N::translateContext('FEMALE', 'Registry officer'),
                'seller'           => I18N::translateContext('FEMALE', 'Seller'),
                'servant'          => I18N::translateContext('FEMALE', 'Servant'),
                'slave'            => I18N::translateContext('FEMALE', 'Slave'),
                'ward'             => I18N::translateContext('FEMALE', 'Ward'),
                'witness'          => I18N::translate('Witness'),
            ],
            'U' => [
                'attendant'        => I18N::translate('Attendant'),
                'attending'        => I18N::translate('Attending'),
                'best_man'         => I18N::translate('Best man'),
                'bridesmaid'       => I18N::translate('Bridesmaid'),
                'buyer'            => I18N::translate('Buyer'),
                'circumciser'      => I18N::translate('Circumciser'),
                'civil_registrar'  => I18N::translate('Civil registrar'),
                'employee'         => I18N::translate('Employee'),
                'employer'         => I18N::translate('Employer'),
                'foster_child'     => I18N::translate('Foster child'),
                'foster_father'    => I18N::translate('Foster father'),
                'foster_mother'    => I18N::translate('Foster mother'),
                'friend'           => I18N::translate('Friend'),
                'godfather'        => I18N::translate('Godfather'),
                'godmother'        => I18N::translate('Godmother'),
                'godparent'        => I18N::translate('Godparent'),
                'godson'           => I18N::translate('Godson'),
                'goddaughter'      => I18N::translate('Goddaughter'),
                'godchild'         => I18N::translate('Godchild'),
                'guardian'         => I18N::translate('Guardian'),
                'informant'        => I18N::translate('Informant'),
                'lodger'           => I18N::translate('Lodger'),
                'nanny'            => I18N::translate('Nanny'),
                'nurse'            => I18N::translate('Nurse'),
                'owner'            => I18N::translate('Owner'),
                'priest'           => I18N::translate('Priest'),
                'rabbi'            => I18N::translate('Rabbi'),
                'registry_officer' => I18N::translate('Registry officer'),
                'seller'           => I18N::translate('Seller'),
                'servant'          => I18N::translate('Servant'),
                'slave'            => I18N::translate('Slave'),
                'ward'             => I18N::translate('Ward'),
                'witness'          => I18N::translate('Witness'),
            ],
        ];

        return $values[$sex] ?? $values['U'];
    }
}
