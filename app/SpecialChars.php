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
namespace Fisharebest\Webtrees;

use Fisharebest\Localization\Locale;
use Fisharebest\Webtrees\SpecialChars\SpecialCharsInterface;

/**
 * Class SpecialChars - exemplar and difficult-to-type characters.
 */
class SpecialChars {
	/** @var string[] A list of supported language-tags. */
	private static $languages = array(
		'af', 'ar', 'cs', 'da', 'de', 'el', 'en', 'es', 'eu', 'fi', 'fr', 'gd', 'haw', 'he',
		'hu', 'is', 'it', 'lt', 'nl', 'nn', 'pl', 'pt', 'ru', 'sk', 'sl', 'sv', 'tr', 'vi',
);
	/**
	 * A list of languages for which special characters are available.
	 *
	 * @return string[]
	 */
	public static function allLanguages() {
		$array = array();
		foreach (self::$languages as $language) {
			$array[$language] = Locale::create($language)->endonym();
		}
		uasort($array, '\Fisharebest\Webtrees\I18N::strCaseCmp');

		return $array;
	}

	/**
	 * Create a SpecialChars object for the specified language
	 *
	 * @param string $language
	 *
	 * @return SpecialCharsInterface
	 */
	public static function create($language) {
		$class = '\Fisharebest\Webtrees\SpecialChars\SpecialChars' . ucfirst($language);

		return new $class;
	}
}
