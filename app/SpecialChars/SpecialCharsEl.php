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
 * Exemplar characters for Greek
 *
 * For each language, list the symbols, puncutation and letters with diacritics
 * that may be difficult to type.
 */
class SpecialCharsEl extends AbstractSpecialChars {
	/**
	 * A list of magiscule letters.
	 *
	 * @return string[]
	 */
	public function upper() {
		return array(
			'Ά', 'Α', 'Β', 'Γ', 'Δ', 'Έ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ί', 'Ϊ', 'Ι', 'Κ', 'Λ', 'Μ',
			'Ν', 'Ξ', 'Ό', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Ύ', 'Ϋ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ώ', 'Ω',
		);
	}

	/**
	 * A list of miniscule letters.
	 *
	 * @return string[]
	 */
	public function lower() {
		return array(
			'ά', 'α', 'β', 'γ', 'δ', 'έ', 'ε', 'ζ', 'η', 'θ', 'ί', 'ϊ', 'ΐ', 'ι', 'κ', 'λ', 'μ', 'ν',
			'ξ', 'ό', 'ο', 'π', 'ρ', 'σ', 'ς', 'τ', 'ύ', 'ϋ', 'ΰ', 'υ', 'φ', 'χ', 'ψ', 'ώ', 'ω',
		);
	}
}
