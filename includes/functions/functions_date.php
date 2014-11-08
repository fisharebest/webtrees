<?php
// Date Functions that can be used by any page in webtrees
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

/**
 * @param string  $age_string
 * @param boolean $show_years
 *
 * @return string
 */
function get_age_at_event($age_string, $show_years) {
	switch (strtoupper($age_string)) {
	case 'CHILD':
		return WT_I18N::translate('Child');
	case 'INFANT':
		return WT_I18N::translate('Infant');
	case 'STILLBORN':
		return WT_I18N::translate('Stillborn');
	default:
		return preg_replace_callback(
			array(
				'/(\d+)([ymwd])/',
			),
			function ($match) use ($age_string, $show_years) {
				switch ($match[2]) {
				case 'y':
					if ($show_years || preg_match('/[dm]/', $age_string)) {
						return WT_I18N::plural('%s year', '%s years', $match[1], WT_I18N::digits($match[1]));
					} else {
						return WT_I18N::digits($match[1]);
					}
				case 'm':
					return WT_I18N::plural('%s month', '%s months', $match[1], WT_I18N::digits($match[1]));
				case 'w':
					return WT_I18N::plural('%s week', '%s weeks', $match[1], WT_I18N::digits($match[1]));
				case 'd':
					return WT_I18N::plural('%s day', '%s days', $match[1], WT_I18N::digits($match[1]));
				}
			},
			$age_string
		);
	}
}

/**
 * Convert a unix timestamp into a formated date-time value, for logs, etc.
 * We can’t just use date("$DATE_FORMAT- $TIME_FORMAT") as this doesn't
 * support internationalisation.
 * Don't attempt to convert into other calendars, as not all days start at
 * midnight, and we can only get it wrong.
 *
 * @param integer $time
 *
 * @return string
 */
function format_timestamp($time) {
	global $DATE_FORMAT, $TIME_FORMAT;

	$time_fmt = $TIME_FORMAT;
	// PHP::date() doesn't do I18N.  Do it ourselves....
	preg_match_all('/%[^%]/', $time_fmt, $matches);
	foreach ($matches[0] as $match) {
		switch ($match) {
		case '%a':
			$t = gmdate('His', $time);
			if ($t == '000000') {
				$time_fmt = str_replace($match, /* I18N: time format “%a” - exactly 00:00:00 */ WT_I18N::translate('midnight'), $time_fmt);
			} elseif ($t < '120000') {
				$time_fmt = str_replace($match, /* I18N: time format “%a” - between 00:00:01 and 11:59:59 */ WT_I18N::translate('a.m.'), $time_fmt);
			} elseif ($t == '120000') {
				$time_fmt = str_replace($match, /* I18N: time format “%a” - exactly 12:00:00 */ WT_I18N::translate('noon'), $time_fmt);
			} else {
				$time_fmt = str_replace($match, /* I18N: time format “%a” - between 12:00:01 and 23:59:59 */ WT_I18N::translate('p.m.'), $time_fmt);
			}
			break;
		case '%A':
			$t = gmdate('His', $time);
			if ($t == '000000') {
				$time_fmt = str_replace($match, /* I18N: time format “%A” - exactly 00:00:00 */ WT_I18N::translate('Midnight'), $time_fmt);
			} elseif ($t < '120000') {
				$time_fmt = str_replace($match, /* I18N: time format “%A” - between 00:00:01 and 11:59:59 */ WT_I18N::translate('A.M.'), $time_fmt);
			} elseif ($t == '120000') {
				$time_fmt = str_replace($match, /* I18N: time format “%A” - exactly 12:00:00 */ WT_I18N::translate('Noon'), $time_fmt);
			} else {
				$time_fmt = str_replace($match, /* I18N: time format “%A” - between 12:00:01 and 23:59:59 */ WT_I18N::translate('P.M.'), $time_fmt);
			}
				break;
		default:
			$time_fmt = str_replace($match, WT_I18N::digits(gmdate(substr($match, -1), $time)), $time_fmt);
		}
	}

	return timestamp_to_gedcom_date($time)->display() . '<span class="date"> - ' . $time_fmt . '</span>';
}

/**
 * Convert a unix-style timestamp into a WT_Date object
 *
 * @param integer $time
 *
 * @return WT_Date
 */
function timestamp_to_gedcom_date($time) {
	return new WT_Date(strtoupper(gmdate('j M Y', $time)));
}
