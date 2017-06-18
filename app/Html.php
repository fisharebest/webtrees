<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Class Html - Add HTML markup to elements consistently.
 */
class Html {
	/**
	 * Escape a string for inclusion within HTML.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected static function escape($string) {
		return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	/**
	 * Convert an array of HTML attributes to an HTML string.
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function attributes(array $attributes) {
		$html = [];
		foreach ($attributes as $key => $value) {
			if (is_string($value) || is_integer($value)) {
				$html[] = self::escape($key) . '="' . self::escape($value) . '"';
			} elseif ($value !== false) {
				$html[] = self::escape($key);
			}
		}

		return implode(' ', $html);
	}
	/**
	 * Filenames are (almost?) always LTR, even on RTL systems.
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public static function filename($filename) {
		return '<samp class="filename" dir="ltr">' . self::escape($filename) . '</samp>';
	}
}
