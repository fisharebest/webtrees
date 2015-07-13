<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\SurnameTradition;

/**
 * Children take a patronym instead of a surname.
 *
 * Sons get their father’s given name plus “sson”
 * Daughters get their father’s given name plus “sdottir”
 */
class IcelandicSurnameTradition extends DefaultSurnameTradition implements SurnameTraditionInterface {
	/**
	 * Does this surname tradition use surnames?
	 *
	 * @return bool
	 */
	public function hasSurnames() {
		return false;
	}

	/**
	 * What names are given to a new child
	 *
	 * @param string $father_name A GEDCOM NAME
	 * @param string $mother_name A GEDCOM NAME
	 * @param string $child_sex   M, F or U
	 *
	 * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
	 */
	public function newChildNames($father_name, $mother_name, $child_sex) {
		if (preg_match(self::REGEX_GIVN, $father_name, $father_match)) {
			switch($child_sex) {
			case 'M':
				return array(
					'NAME' => $father_match['GIVN'] . 'sson',
				);
			case 'F':
				return array(
					'NAME' => $father_match['GIVN'] . 'sdottir',
				);
			}
		}

		return array();
	}

	/**
	 * What names are given to a new parent
	 *
	 * @param string $child_name A GEDCOM NAME
	 * @param string $parent_sex M, F or U
	 *
	 * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
	 */
	public function newParentNames($child_name, $parent_sex) {
		if ($parent_sex === 'M' && preg_match('~(?<GIVN>[^ /]+)(:?sson|sdottir)$~', $child_name, $child_match)) {
			return array(
				'NAME' => $child_match['GIVN'],
				'GIVN' => $child_match['GIVN'],
			);
		} else {
			return array();
		}
	}

	/**
	 * What names are given to a new spouse
	 *
	 * @param string $spouse_name A GEDCOM NAME
	 * @param string $spouse_sex  M, F or U
	 *
	 * @return string[] Associative array of GEDCOM name parts (SURN, _MARNM, etc.)
	 */
	public function newSpouseNames($spouse_name, $spouse_sex) {
		return array();
	}
}
