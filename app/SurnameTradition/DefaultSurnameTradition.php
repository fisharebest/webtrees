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
 * All family members keep their original surname
 */
class DefaultSurnameTradition implements SurnameTraditionInterface {
	/** Extract a GIVN from a NAME */
	const REGEX_GIVN = '~^(?<GIVN>[^/ ]+)~';

	/** Extract a SPFX and SURN from a NAME */
	const REGEX_SPFX_SURN = '~(?<NAME>/(?<SPFX>[a-z]{0,4}(?: [a-z]{1,4})*) ?(?<SURN>[^/]*)/)~';

	/** Extract a simple SURN from a NAME */
	const REGEX_SURN = '~(?<NAME>/(?<SURN>[^/]+)/)~';

	/** Extract two Spanish/Portuguese SURNs from a NAME */
	const REGEX_SURNS = '~/(?<SURN1>[^ /]+)(?: | y |/ /|/ y /)(?<SURN2>[^ /]+)/~';

	/**
	 * Does this surname tradition change surname at marriage?
	 *
	 * @return bool
	 */
	public function hasMarriedNames() {
		return false;
	}

	/**
	 * Does this surname tradition use surnames?
	 *
	 * @return bool
	 */
	public function hasSurnames() {
		return true;
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
		return array(
			'NAME' => '//',
		);
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
		return array(
			'NAME' => '//',
		);
	}
}
