<?php
/**
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 Greg Roach
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

define('WT_FUNCTIONS_FR_PHP', '');

////////////////////////////////////////////////////////////////////////////////
// Create an ordinal suffix for a number.
////////////////////////////////////////////////////////////////////////////////
function ordinal_suffix_fr($n) {
	if ($n==1)
		return 'er';
	return 'éme';
}

////////////////////////////////////////////////////////////////////////////////
// Localise a date. "[qualifier] date [qualifier date] [qualifier]"
////////////////////////////////////////////////////////////////////////////////
function date_localisation_fr(&$q1, &$d1, &$q2, &$d2, &$q3) {
	// Years in the french republican calendar are displayed in roman numerals.
	// They need a prefix of "an"
	$d1=preg_replace("/(\b[IVX]+$)/", "an $1", $d1);
	$d2=preg_replace("/(\b[IVX]+$)/", "an $1", $d2);
}

?>
