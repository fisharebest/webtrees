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

use Fisharebest\ExtCalendar\JulianCalendar;

/**
 * Class JulianDate - Definitions for the Julian Proleptic calendar
 * (Proleptic means we extend it backwards, prior to its introduction in 46BC)
 */
class JulianDate extends CalendarDate {
	const CALENDAR_ESCAPE = '@#DJULIAN@';

	/** @var boolean True for dates recorded in new-style/old-style format, e.g. 2 FEB 1743/44 */
	private $new_old_style = false;

	/** {@inheritdoc} */
	public function __construct($date) {
		$this->calendar = new JulianCalendar;
		parent::__construct($date);
	}

	/** {@inheritdoc} */
	public static function calendarName() {
		return /* I18N: The julian calendar */
			I18N::translate('Julian');
	}

	/** {@inheritdoc} */
	protected static function nextYear($year) {
		if ($year == -1) {
			return 1;
		} else {
			return $year + 1;
		}
	}

	/**
	 * Process new-style/old-style years and years BC
	 *
	 * {@inheritdoc}
	 */
	protected function extractYear($year) {
		if (preg_match('/^(\d\d\d\d)\/\d{1,4}$/', $year, $match)) {
			// Assume the first year is correct
			$this->new_old_style = true;

			return $match[1] + 1;
		} else if (preg_match('/^(\d+) B\.C\.$/', $year, $match)) {
			return -$match[1];
		} else {
			return (int) $year;
		}
	}

	/** {@inheritdoc} */
	protected function formatLongYear() {
		if ($this->y < 0) {
			return /*  I18N: BCE=Before the Common Era, for Julian years < 0.  See http://en.wikipedia.org/wiki/Common_Era */
				I18N::translate('%s&nbsp;BCE', I18N::digits(-$this->y));
		} else {
			if ($this->new_old_style) {
				return I18N::translate('%s&nbsp;CE', I18N::digits(sprintf('%d/%02d', $this->y - 1, $this->y % 100)));
			} else {
				return /* I18N: CE=Common Era, for Julian years > 0.  See http://en.wikipedia.org/wiki/Common_Era */
					I18N::translate('%s&nbsp;CE', I18N::digits($this->y));
			}
		}
	}

	/** {@inheritdoc} */
	protected function formatGedcomYear() {
		if ($this->y < 0) {
			return sprintf('%04d B.C.', -$this->y);
		} else {
			if ($this->new_old_style) {
				return sprintf('%04d/%02d', $this->y - 1, $this->y % 100);
			} else {
				return sprintf('%04d', $this->y);
			}
		}
	}
}
