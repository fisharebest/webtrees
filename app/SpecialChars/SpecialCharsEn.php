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
 * Exemplar characters for English
 *
 * For each language, list the symbols, puncutation and letters with diacritics
 * that may be difficult to type.
 */
class SpecialCharsEn extends AbstractSpecialChars {
	/**
	 * A list of magiscule letters.
	 *
	 * @return string[]
	 */
	public function upper() {
		return array(
			'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'Ð', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï',
			'Ĳ', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Œ', 'Ø', 'Þ', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ',
		);
	}

	/**
	 * A list of miniscule letters.
	 *
	 * @return string[]
	 */
	public function lower() {
		return array(
			'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'ð', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï',
			'ĳ', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'œ', 'ø', 'þ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ß',
		);
	}
}
