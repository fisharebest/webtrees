<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\SurnameTradition;

/**
 * Children take their father’s surname.
 */
class ArabicSurnameTradition extends DefaultSurnameTradition
{
    /**
     * What names are given to a new child
     *
     * @param string $father_name A GEDCOM NAME
     * @param string $mother_name A GEDCOM NAME
     * @param string $child_sex   M, F or U
     *
     * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newChildNames(string $father_name, string $mother_name, string $child_sex): array
    {
        $hasSurname = preg_match(self::REGEX_SURN, $father_name, $father_surn_match);

        $new_spfx = self::getChildSpfx($child_sex, $father_name);
        $family_name = self::getFamilyName($father_name);

        return array_filter([
            'NAME' => '/' . join(' ', array_filter([$new_spfx, $family_name])) . '/',
            'SURN' => $family_name,
        ]);
    }

    /**
     * Returns the last part of the father's surname.
     *
     * @param string $father_name
     * @return string
     */
    public static function getFamilyName(string $father_name): string
    {
        if (!preg_match(self::REGEX_SURN, $father_name, $father_surn_match)) {
            return '';
        }

        $parts = explode(' ', $father_surn_match['SURN']);

        return array_pop($parts);
    }

    /**
     * Use the first part (RTL!) of the father's SPFX, prefix (RTL!) with the father's GIVN.
     * SURN only contains family name and can be kept as-is.
     *
     * @param string $child_sex the child sex, either 'F' or 'M' or unknown.
     * @param string[] $match the match on REGEX_SPFX_SURN.
     * @return string The new child's middle name.
     */
    public static function getChildSpfx(string $child_sex, string $father_name): string
    {
        $grandfather_name_prefixed = ArabicSurnameTradition::getGrandfatherNameWithPrefix($father_name);
        $hasGivenName = preg_match(self::REGEX_GIVN, $father_name, $father_givn_match);
        if (!$hasGivenName) {
            return '';
        }

        $father_name = trim($father_givn_match['GIVN']);

        if ($child_sex === 'F') {
            $father_name_prefixed = join(' ', [ArabicSurnameTradition::doughterOfPrefix(), $father_name]);
            return trim(join(' ', array_filter([$father_name_prefixed, $grandfather_name_prefixed])));
        }

        $father_name_prefixed = join(' ', [ArabicSurnameTradition::sonOfPrefix(), $father_name]);
        return trim(join(' ', array_filter([$father_name_prefixed, $grandfather_name_prefixed])));
    }

    public static function getGrandfatherNameWithPrefix(string $father_name): string
    {
        if (!preg_match(self::REGEX_SURN, $father_name, $father_surn_match)) {
            return '';
        }

        // TODO: only take the father's first spfx.
        $parts = explode(' ', $father_surn_match['SURN']);
        if (count($parts) <= 1) {
            // no grandfather name known. SURN may only  be last name.
            return '';
        }

        // remove last name.
        while (count($parts) > 1) {
            array_pop($parts);
        }

        return trim(join(' ', array_filter([ArabicSurnameTradition::sonOfPrefix(), $parts[0]])));
    }

    /**
     * What names are given to a new parent
     *
     * @param string $child_name A GEDCOM NAME
     * @param string $parent_sex M, F or U
     *
     * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
     */
    public function newParentNames(string $child_name, string $parent_sex): array
    {
        if ($parent_sex === 'M' && preg_match(self::REGEX_SURN, $child_name, $match)) {
            // we can gues the given name. It is the SPFX first part without 'ibn/bin/ben' (ابن).
            $sonSurname = explode(' ', $match['SURN']);
            $familyName = array_pop($sonSurname);

            // reverse the other names to have the father's first name at the end.
            $reversed_remaining_names = array_reverse($sonSurname);
            $guessedFirstName = array_pop($reversed_remaining_names);

            // any other remaining names at the end might be the grandfathers name.
            if (count($reversed_remaining_names) >= 1) {
                $middle_name = array_pop($reversed_remaining_names);
            } else {
                $middle_name = '';
            }


            return array_filter([
                'NAME' => $guessedFirstName . ' ' . $middle_name . ' ' . $familyName,
                'SURN' => $middle_name . ' ' . $familyName,
                'GIVN' => $guessedFirstName
            ]);
        }

        // if parent sex is not known, we cannot guess names.
        return [
            'NAME' => '//',
        ];
    }

    /* **********
     * SETTINGS *
     * **********/

    /**
     * Prefix for 'daughter of' (bin/bint).
     * Is not used nowadays, so just omit it for now.
     *
     * @return string the 'doughter of' prefix.
     */
    public function doughterOfPrefix(): string
    {
        return '';
    }

    /**
     * Prefix for 'son of' (ibn/bin/ben).
     * Is not used nowadays, so just omit it for now.
     *
     * @return string the 'son of' prefix.
     */
    public function sonOfPrefix(): string
    {
        return '';
    }
}
