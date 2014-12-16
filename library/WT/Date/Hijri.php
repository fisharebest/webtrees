<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use Fisharebest\ExtCalendar\ArabicCalendar;

/**
 * Class WT_Date_Hijri - Definitions for the Hijri calendar.
 * Note that these are "theoretical" dates.
 * "True" dates are based on local lunar observations, and can be a +/- one day.
 */
class WT_Date_Hijri extends WT_Date_Calendar {
	const CALENDAR_ESCAPE = '@#DHIJRI@';
	const CAL_START_JD = 1948440; // @#DHIJRI@ 1 MUHAR 0001 = @#JULIAN@ 16 JUL 0622
	static $MONTH_ABBREV = array('' => 0, 'MUHAR' => 1, 'SAFAR' => 2, 'RABIA' => 3, 'RABIT' => 4, 'JUMAA' => 5, 'JUMAT' => 6, 'RAJAB' => 7, 'SHAAB' => 8, 'RAMAD' => 9, 'SHAWW' => 10, 'DHUAQ' => 11, 'DHUAH' => 12);

	/**
	 * {@inheritdoc}
	 */
	public function __construct($date) {
		$this->calendar = new ArabicCalendar;
		parent::__construct($date);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function calendarName() {
		return /* I18N: The Arabic/Hijri calendar */
			WT_I18N::translate('Hijri');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function monthNameNominativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ WT_I18N::translate_c('NOMINATIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ WT_I18N::translate_c('NOMINATIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ WT_I18N::translate_c('NOMINATIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ WT_I18N::translate_c('NOMINATIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ WT_I18N::translate_c('NOMINATIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ WT_I18N::translate_c('NOMINATIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ WT_I18N::translate_c('NOMINATIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ WT_I18N::translate_c('NOMINATIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ WT_I18N::translate_c('NOMINATIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ WT_I18N::translate_c('NOMINATIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ WT_I18N::translate_c('NOMINATIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ WT_I18N::translate_c('NOMINATIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameGenitiveCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ WT_I18N::translate_c('GENITIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ WT_I18N::translate_c('GENITIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ WT_I18N::translate_c('GENITIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ WT_I18N::translate_c('GENITIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ WT_I18N::translate_c('GENITIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ WT_I18N::translate_c('GENITIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ WT_I18N::translate_c('GENITIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ WT_I18N::translate_c('GENITIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ WT_I18N::translate_c('GENITIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ WT_I18N::translate_c('GENITIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ WT_I18N::translate_c('GENITIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ WT_I18N::translate_c('GENITIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameLocativeCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ WT_I18N::translate_c('LOCATIVE', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ WT_I18N::translate_c('LOCATIVE', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ WT_I18N::translate_c('LOCATIVE', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ WT_I18N::translate_c('LOCATIVE', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ WT_I18N::translate_c('LOCATIVE', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ WT_I18N::translate_c('LOCATIVE', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ WT_I18N::translate_c('LOCATIVE', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ WT_I18N::translate_c('LOCATIVE', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ WT_I18N::translate_c('LOCATIVE', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ WT_I18N::translate_c('LOCATIVE', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ WT_I18N::translate_c('LOCATIVE', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ WT_I18N::translate_c('LOCATIVE', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameInstrumentalCase($month_number, $leap_year) {
		static $translated_month_names;

		if ($translated_month_names === null) {
			$translated_month_names = array(
				0  => '',
				1  => /* I18N: http://en.wikipedia.org/wiki/Muharram                     */ WT_I18N::translate_c('INSTRUMENTAL', 'Muharram'),
				2  => /* I18N: http://en.wikipedia.org/wiki/Safar                        */ WT_I18N::translate_c('INSTRUMENTAL', 'Safar'),
				3  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-awwal             */ WT_I18N::translate_c('INSTRUMENTAL', 'Rabi’ al-awwal'),
				4  => /* I18N: http://en.wikipedia.org/wiki/Rabi%27_al-thani             */ WT_I18N::translate_c('INSTRUMENTAL', 'Rabi’ al-thani'),
				5  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-awwal              */ WT_I18N::translate_c('INSTRUMENTAL', 'Jumada al-awwal'),
				6  => /* I18N: http://en.wikipedia.org/wiki/Jumada_al-thani              */ WT_I18N::translate_c('INSTRUMENTAL', 'Jumada al-thani'),
				7  => /* I18N: http://en.wikipedia.org/wiki/Rajab                        */ WT_I18N::translate_c('INSTRUMENTAL', 'Rajab'),
				8  => /* I18N: http://en.wikipedia.org/wiki/Sha%27aban                   */ WT_I18N::translate_c('INSTRUMENTAL', 'Sha’aban'),
				9  => /* I18N: http://en.wikipedia.org/wiki/Ramadan_%28calendar_month%29 */ WT_I18N::translate_c('INSTRUMENTAL', 'Ramadan'),
				10 => /* I18N: http://en.wikipedia.org/wiki/Shawwal                      */ WT_I18N::translate_c('INSTRUMENTAL', 'Shawwal'),
				11 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Qi%27dah              */ WT_I18N::translate_c('INSTRUMENTAL', 'Dhu al-Qi’dah'),
				12 => /* I18N: http://en.wikipedia.org/wiki/Dhu_al-Hijjah                */ WT_I18N::translate_c('INSTRUMENTAL', 'Dhu al-Hijjah'),
			);
		}

		return $translated_month_names[$month_number];
	}

	/**
	 * {@inheritdoc}
	 */
	static function monthNameAbbreviated($month_number, $leap_year) {
		return self::monthNameNominativeCase($month_number, $leap_year);
	}
}
