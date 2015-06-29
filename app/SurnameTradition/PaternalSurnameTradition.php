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
 * Children take their father’s surname. Wives take their husband’s surname.
 */
class PaternalSurnameTradition extends PatrilinealSurnameTradition implements SurnameTraditionInterface {
	/**
	 * Does this surname tradition change surname at marriage?
	 *
	 * @return bool
	 */
	public function hasMarriedNames() {
		return true;
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
		if (preg_match(self::REGEX_SPFX_SURN, $child_name, $match)) {
			switch ($parent_sex) {
			case 'M':
				return array_filter(array(
					'NAME' => $match['NAME'],
					'SPFX' => $match['SPFX'],
					'SURN' => $match['SURN'],
				));
			case 'F':
				return array(
					'NAME'   => '//',
					'_MARNM' => '/' . trim($match['SPFX'] . ' ' . $match['SURN']) . '/',
				);
			}
		}

		return array(
			'NAME' => '//',
		);
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
		if ($spouse_sex === 'F' && preg_match(self::REGEX_SURN, $spouse_name, $match)) {
			return array(
				'NAME'   => '//',
				'_MARNM' => $match['NAME'],
			);
		} else {
			return array(
				'NAME' => '//',
			);
		}
	}
}
