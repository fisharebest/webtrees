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
 * Exemplar characters for Vietnamese
 *
 * For each language, list the symbols, puncutation and letters with diacritics
 * that may be difficult to type.
 */
class SpecialCharsVi extends AbstractSpecialChars {
	/**
	 * A list of magiscule letters.
	 *
	 * @return string[]
	 */
	public function upper() {
		return array(
			'À', 'Á', 'Â', 'Ã', 'Ạ', 'Ả', 'Ă', 'Ấ', 'Ầ', 'Ẫ', 'Ậ', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',
			'Đ', 'È', 'É', 'Ê', 'Ẹ', 'Ẻ', 'Ẽ', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ì', 'Í', 'Ĩ', 'Ỉ', 'Ị',
			'Ò', 'Ó', 'Ô', 'Õ', 'Ơ', 'Ọ', 'Ỏ', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',
			'Ù', 'Ú', 'Ũ', 'Ư', 'Ụ', 'Ủ', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ý', 'Ỳ', 'Ỵ', 'Ỷ', 'Ỹ',
		);
	}

	/**
	 * A list of miniscule letters.
	 *
	 * @return string[]
	 */
	public function lower() {
		return array(
			'à', 'á', 'â', 'ã', 'ạ', 'ả', 'ă', 'ấ', 'ầ', 'ẫ', 'ậ', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',
			'đ', 'è', 'é', 'ê', 'ẹ', 'ẻ', 'ẽ', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ì', 'í', 'ĩ', 'ỉ', 'ị',
			'ò', 'ó', 'ô', 'õ', 'ơ', 'ọ', 'ỏ', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
			'ù', 'ú', 'ũ', 'ư', 'ụ', 'ủ', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỵ', 'ỷ', 'ỹ',
		);
	}
}
