<?php
namespace Fisharebest\Webtrees;

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

use Fisharebest\ExtCalendar\ArabicCalendar;

/**
 * Class HijriDate - Definitions for the Hijri calendar.
 *
 * Note that these are "theoretical" dates.
 * "True" dates are based on local lunar observations, and can be a +/- one day.
 */
class HijriDate extends CalendarDate {
	const CALENDAR_ESCAPE = '@#DHIJRI@';
	const CAL_START_JD = 1948440; // @#DHIJRI@ 1 MUHAR 0001 = @#JULIAN@ 16 JUL 0622

	/** {@inheritdoc} */
	public static $MONTH_ABBREV = array('' => 0, 'MUHAR' => 1, 'SAFAR' => 2, 'RABIA' => 3, 'RABIT' => 4, 'JUMAA' => 5, 'JUMAT' => 6, 'RAJAB' => 7, 'SHAAB' => 8, 'RAMAD' => 9, 'SHAWW' => 10, 'DHUAQ' => 11, 'DHUAH' => 12);

	/** {@inheritdoc} */
	public function __construct($date) {
		$this->calendar = new ArabicCalendar;
		parent::__construct($date);
	}

	/** {@inheritdoc} */
	public static function calendarName() {
		return /* I18N: The Arabic/Hijri calendar */
			I18N::translate('Hijri');
	}

	/** {@inheritdoc} */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ I18N::translateContext('NOMINATIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ I18N::translateContext('NOMINATIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ I18N::translateContext('NOMINATIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ I18N::translateContext('NOMINATIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ I18N::translateContext('NOMINATIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ I18N::translateContext('NOMINATIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ I18N::translateContext('NOMINATIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ I18N::translateContext('NOMINATIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ I18N::translateContext('NOMINATIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ I18N::translateContext('NOMINATIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ I18N::translateContext('NOMINATIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ I18N::translateContext('NOMINATIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/** {@inheritdoc} */
	static function monthNameGenitiveCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ I18N::translateContext('GENITIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ I18N::translateContext('GENITIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ I18N::translateContext('GENITIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ I18N::translateContext('GENITIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ I18N::translateContext('GENITIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ I18N::translateContext('GENITIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ I18N::translateContext('GENITIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ I18N::translateContext('GENITIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ I18N::translateContext('GENITIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ I18N::translateContext('GENITIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ I18N::translateContext('GENITIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ I18N::translateContext('GENITIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/** {@inheritdoc} */
	static function monthNameLocativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ I18N::translateContext('LOCATIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ I18N::translateContext('LOCATIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ I18N::translateContext('LOCATIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ I18N::translateContext('LOCATIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ I18N::translateContext('LOCATIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ I18N::translateContext('LOCATIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ I18N::translateContext('LOCATIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ I18N::translateContext('LOCATIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ I18N::translateContext('LOCATIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ I18N::translateContext('LOCATIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ I18N::translateContext('LOCATIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ I18N::translateContext('LOCATIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/** {@inheritdoc} */
	static function monthNameInstrumentalCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ I18N::translateContext('INSTRUMENTAL', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ I18N::translateContext('INSTRUMENTAL', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ I18N::translateContext('INSTRUMENTAL', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ I18N::translateContext('INSTRUMENTAL', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ I18N::translateContext('INSTRUMENTAL', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ I18N::translateContext('INSTRUMENTAL', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ I18N::translateContext('INSTRUMENTAL', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ I18N::translateContext('INSTRUMENTAL', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ I18N::translateContext('INSTRUMENTAL', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ I18N::translateContext('INSTRUMENTAL', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ I18N::translateContext('INSTRUMENTAL', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ I18N::translateContext('INSTRUMENTAL', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/** {@inheritdoc} */
	static function monthNameAbbreviated($month_number, $leap_year) {
		return self::monthNameNominativeCase($month_number, $leap_year);
	}
}
