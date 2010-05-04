<?php
/**
* Date Functions that can be used by any page in PGV
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @package webtrees
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_DATE_PHP', '');

require_once WT_ROOT.'includes/classes/class_date.php';

/**
* translate gedcom age string
*
* Examples:
* 4y 8m 10d.
* Chi
* INFANT
*
* @param string $agestring gedcom AGE field value
* @param bool $show_years;
* @return string age in user language
* @see http://homepages.rootsweb.com/~pmcbride/gedcom/55gcch2.htm#AGE_AT_EVENT
*/
function get_age_at_event($agestring, $show_years) {
	return preg_replace(
		array(
			'/\bchi(ld)?\b/i',
			'/\binf(ant)?\b/i',
			'/\bsti(llborn)?\b/i',
			'/(\d+)y/ie',
			'/(\d+)m/ie',
			'/(\d+)d/ie',
			'/(\d+)w/ie'
		),
		array(
			i18n::translate('Child'),
			i18n::translate('Infant'),
			i18n::translate('Stillborn'),
			($show_years || preg_match('/[dm]/', $agestring)) ? "i18n::plural('%d year', '%d years', '$1' , '$1')" : '$1',
			"i18n::plural('%d month', '%d months', '$1' , '$1')",
			"i18n::plural('%d day', '%d days', '$1' , '$1')",
			"i18n::plural('%d week', '%d weeks', '$1' , '$1')"
		),
		$agestring
	);
}

/**
* Parse a time string into its different parts
* @param string $timestr the time as it was taken from the TIME tag
* @return array returns an array with the hour, minutes, and seconds
*/
function parse_time($timestr)
{
	$time = explode(':', $timestr.':0:0');
	$time[0] = min(((int) $time[0]), 23); // Hours: integer, 0 to 23
	$time[1] = min(((int) $time[1]), 59); // Minutes: integer, 0 to 59
	$time[2] = min(((int) $time[2]), 59); // Seconds: integer, 0 to 59
	$time["hour"] = $time[0];
	$time["minutes"] = $time[1];
	$time["seconds"] = $time[2];

	return $time;
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix timestamp into a formated date-time value, for logs, etc.
// We can't just use date("$DATE_FORMAT- $TIME_FORMAT") as this doesn't
// support internationalisation.
// Don't attempt to convert into other calendars, as not all days start at
// midnight, and we can only get it wrong.
////////////////////////////////////////////////////////////////////////////////
function format_timestamp($time) {
	global $DATE_FORMAT, $TIME_FORMAT;

	// PHP time formatting is english only.
/* TODO: cannot use this yet - the "a" in "a.m." gets refomatted by date()
 * Need to handle all of the time codes ourselves.
	if (strpos('%a', $TIME_FORMAT)!==false || strpos('%A', $TIME_FORMAT)!==false) {
		$fmt=date('His', $time);
		if ($fmt=='000000') {
			$TIME_FORMAT=str_replace('%A', i18n::translate('MIDNIGHT'), $TIME_FORMAT);
			$TIME_FORMAT=str_replace('%a', i18n::translate('midnight'), $TIME_FORMAT);
		} elseif ($fmt=='120000') {
			$TIME_FORMAT=str_replace('%A', i18n::translate('NOON'), $TIME_FORMAT);
			$TIME_FORMAT=str_replace('%a', i18n::translate('noon'), $TIME_FORMAT);
		} elseif ($fmt>'120000') {
			$TIME_FORMAT=str_replace('%A', i18n::translate('P.M.'), $TIME_FORMAT);
			$TIME_FORMAT=str_replace('%a', i18n::translate('p.m.'), $TIME_FORMAT);
		} else {
			$TIME_FORMAT=str_replace('%A', i18n::translate('A.M.'), $TIME_FORMAT);
			$TIME_FORMAT=str_replace('%a', i18n::translate('a.m.'), $TIME_FORMAT);
		}
	}
*/

	return
		PrintReady(timestamp_to_gedcom_date($time)->Display(false, $DATE_FORMAT).
		'<span class="date"> - '.date(str_replace('%', '', $TIME_FORMAT), $time).'</span>');
}

////////////////////////////////////////////////////////////////////////////////
// Get the current julian day on the server
////////////////////////////////////////////////////////////////////////////////
function server_jd() {
	return timestamp_to_jd(time());
}

////////////////////////////////////////////////////////////////////////////////
// Get the current julian day on the client
////////////////////////////////////////////////////////////////////////////////
function client_jd() {
	return timestamp_to_jd(client_time());
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix-style timestamp into a julian-day
////////////////////////////////////////////////////////////////////////////////
function timestamp_to_jd($time) {
	return timestamp_to_gedcom_date($time)->JD();
}

////////////////////////////////////////////////////////////////////////////////
// Convert a unix-style timestamp into a GedcomDate object
////////////////////////////////////////////////////////////////////////////////
function timestamp_to_gedcom_date($time) {
	return new GedcomDate(strtoupper(date('j M Y', $time)));
}

////////////////////////////////////////////////////////////////////////////////
// Get the current timestamp of the client, not the server
////////////////////////////////////////////////////////////////////////////////
function client_time() {
	if (isset($_SESSION["timediff"])) {
		return time()-$_SESSION["timediff"];
	} else {
		return time();
	}
}

?>
