<?php
namespace Fisharebest\ExtCalendar;

/**
 * class Calendar - generic base class for specific calendars.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2014 Greg Roach
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 3 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
abstract class Calendar {
	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NAME = 'Undefined';

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_NUMBER = -1;

	/** Same as PHP’s ext/calendar extension */
	const PHP_CALENDAR_SYMBOL = 'CAL_UNDEFINED';

	/** See the GEDCOM specification */
	const GEDCOM_CALENDAR_ESCAPE = '@#DUNKNOWN@';

	/** The earliest Julian Day number that can be converted into this calendar. */
	const JD_START = 1;

	/** The latest Julian Day number that can be converted into this calendar. */
	const JD_END = 2147483647;

	/** The maximum number of months in any year */
	const MAX_MONTHS_IN_YEAR = 12;

	/** The maximum number of days in any month */
	const MAX_DAYS_IN_MONTH = 31;

	/**
	 * English names for the days of the week.
	 *
	 * @return string[]
	 */
	protected function dayNames() {
		return array(
			'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
		);
	}

	/**
	 * Abbreviated English names for the days of the week.
	 *
	 * @return string[]
	 */
	protected function dayNamesAbbreviated() {
		return array(
			'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat',
		);
	}

	/**
	 * Convert a Julian Day number into a calendar date.
	 *
	 * @param  $jd
	 * @return int[] Array of month, day and year
	 */
	public function calFromJd($jd) {
		$dow = $this->dayOfWeek($jd);

		if ($jd >= static::JD_START && $jd <= static::JD_END) {
			list($year, $month, $day) = $this->jdToYmd($jd);

			return array(
				'date' => $month . '/' . $day . '/' . $year,
				'month' => $month,
				'day' => $day,
				'year' => $year,
				'dow' => $dow,
				'abbrevdayname' => $this->dayNameAbbreviated($dow),
				'dayname' => $this->dayName($dow),
				'abbrevmonth' => $this->jdMonthNameAbbreviated($jd),
				'monthname' => $this->jdMonthName($jd),
			);
		} else {
			return array(
				'date' => '0/0/0',
				'month' => 0,
				'day' => 0,
				'year' => 0,
				'dow' => $dow,
				'abbrevdayname' => $this->dayNameAbbreviated($dow),
				'dayname' => $this->dayName($dow),
				'abbrevmonth' => '',
				'monthname' => '',
			);
		}
	}

	/**
	 * Provide information about this calendar.
	 *
	 * @return array
	 */
	public function phpCalInfo() {
		return array(
			'months' => $this->monthNames(),
			'abbrevmonths' => $this->monthNamesAbbreviated(),
			'maxdaysinmonth' => static::MAX_DAYS_IN_MONTH,
			'calname' => static::PHP_CALENDAR_NAME,
			'calsymbol' => static::PHP_CALENDAR_SYMBOL,
		);
	}

	/**
	 * Calculate the day of the week for a given Julian Day number.
	 *
	 * @param int $jd
	 *
	 * @return int 0=Sunday ... 6=Saturday
	 */
	public function dayOfWeek($jd) {
		$dow = ($jd + 1) % 7;
		if ($dow < 0) {
			return $dow + 7;
		} else {
			return $dow;
		}
	}

	/**
	 * English name for a day of the week.
	 *
	 * @param int $dow Day of the week
	 *
	 * @return string
	 */
	public function dayName($dow) {
		$days = $this->dayNames();

		return $days[$dow];
	}

	/**
	 * Abbreviated English name for a day of the week.
	 *
	 * @param int $dow Day of the week
	 *
	 * @return string
	 */
	public function dayNameAbbreviated($dow) {
		$days = $this->dayNamesAbbreviated();

		return $days[$dow];
	}

	/**
	 * Calculate the name of a month, for a specified Julian Day number.
	 *
	 * @param  $jd
	 *
	 * @return string
	 */
	public function jdMonthName($jd) {
		list(, $month) = $this->jdToYmd($jd);
		$months = $this->monthNames();

		return $months[$month];
	}

	/**
	 * Calculate the name of a month, for a specified Julian Day number.
	 *
	 * @param  int $jd
	 *
	 * @return string
	 */
	public function jdMonthNameAbbreviated($jd) {
		list(, $month) = $this->jdToYmd($jd);
		$months = $this->monthNamesAbbreviated();

		return $months[$month];
	}

	/**
	 * Unless otherwise defined, abbreviated month names are the same as full names.
	 *
	 * @return string[]
	 */
	public function monthNamesAbbreviated() {
		return $this->monthNames();
	}
}
