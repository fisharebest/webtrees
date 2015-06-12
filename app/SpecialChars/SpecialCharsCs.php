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
namespace Fisharebest\Webtrees\SpecialChars;

/**
 * Exemplar characters for Czech
 *
 * For each language, list the symbols, puncutation and letters with diacritics
 * that may be difficult to type.
 */
class SpecialCharsCs extends AbstractSpecialChars {
	/**
	 * A list of magiscule letters.
	 *
	 * @return string[]
	 */
	public function upper() {
		return array(
			'Á', 'Ą', 'Ä', 'É', 'Ę', 'Ě', 'Í', 'Ó', 'Ô', 'Ú', 'Ů', 'Ý', 'Č', 'Ĺ', 'Ň', 'Ŕ', 'Ř', 'Š', 'Ž',
		);
	}

	/**
	 * A list of miniscule letters.
	 *
	 * @return string[]
	 */
	public function lower() {
		return array(
			'á', 'ą', 'ä', 'é', 'ę', 'ě', 'í', 'ó', 'ô', 'ú', 'ů', 'ý', 'č', 'ď', 'ť', 'ĺ', 'ň', 'ŕ', 'ř', 'š', 'ž',
		);
	}
}
