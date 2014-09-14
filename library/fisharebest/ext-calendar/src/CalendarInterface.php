<?php
namespace Fisharebest\ExtCalendar;

/**
 * interface CalendarInterface - each calendar implementation needs to provide
 * these methods.
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
interface CalendarInterface {
	public function daysInMonth($year, $month);
	public function dayOfWeek($jd);
	public function jdToYmd($jd);
	public function leapYear($year);
	public function ymdToJd($year, $month, $day);
}
