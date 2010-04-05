<?php
/**
 * Turkish Date Functions that can be used by any page in PGV
 * Other functions that are specific to Turkish can be added here too
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2003  John Finlay and Others
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

define('WT_FUNCTIONS_TR_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Localise a date. "[qualifier] date [qualifier date] [qualifier]"
////////////////////////////////////////////////////////////////////////////////
function date_localisation_tr(&$q1, &$d1, &$q2, &$d2, &$q3) {
	global $pgv_lang;

	switch ($q1) {
	case 'from':
	case 'bef':
	case 'aft':
		if (preg_match('/(\d{3,4})/', $d1, $match)) { // Extract year
			switch ($match[1] % 10) {
			case 0:
				switch ($match[1] % 100) {
				case 0:
				case 20:
				case 50:
				case 70:
				case 80: $q1=str_replace('#EXT#', 'den', $pgv_lang[$q1]); break;
				default: $q1=str_replace('#EXT#', 'dan', $pgv_lang[$q1]); break;
				}
				break;
			case 6:
			case 9: $q1=str_replace('#EXT#', 'dan', $pgv_lang[$q1]); break;
			default: $q1=str_replace('#EXT#', 'den', $pgv_lang[$q1]); break;
			}
		} else {
			$q1=str_replace('#EXT#', 'den', $pgv_lang[$q1]);
		}
		break;
	case 'to':
		if (preg_match('/(\d{3,4})/', $d1, $match)) { // Extract year
			switch ($match[1]) {
			case '0':
			case '9': $q1=str_replace('#EXT#', 'a', $pgv_lang[$q1]); break;
			case '2':
			case '7': $q1=str_replace('#EXT#', 'ye', $pgv_lang[$q1]); break;
			case '6': $q1=str_replace('#EXT#', 'ya', $pgv_lang[$q1]); break;
			default: $q1=str_replace('#EXT#', 'e', $pgv_lang[$q1]); break;
			}
		} else {
			$q1=str_replace('#EXT#', 'e', $pgv_lang[$q1]);
		}
		break;
	default:
		if (isset($pgv_lang[$q1]))
			$q1=$pgv_lang[$q1];
		break;
	}

	switch ($q2) {
	case 'to':
		if (preg_match('/(\d{3,4})/', $d2, $match)) { // Extract year
			switch ($match[1] % 10) {
			case 0:
			case 9: $q2=str_replace('#EXT#', 'a', $pgv_lang[$q2]); break;
			case 2:
			case 7: $q2=str_replace('#EXT#', 'ye', $pgv_lang[$q2]); break;
			case 6: $q2=str_replace('#EXT#', 'ya', $pgv_lang[$q2]); break;
			default: $q2=str_replace('#EXT#', 'e', $pgv_lang[$q2]); break;
			}
		} else {
			$q2=str_replace('#EXT#', 'e', $pgv_lang[$q2]);
		}
		break;
	default:
		if (isset($pgv_lang[$q2]))
			$q2=$pgv_lang[$q2];
		break;
	}
}

?>
