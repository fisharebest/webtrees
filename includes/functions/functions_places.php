<?php
// Functions for places selection (clickable maps, autocompletion...)
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PLACE_PHP', '');

/**
 * get the URL to link to a place
 * @string a url that can be used to link to placelist
 */
function get_place_url($gedcom_place) {
	global $GEDCOM;
	$exp = explode(', ', $gedcom_place);
	$level = count($exp);
	$url = "placelist.php?action=show&amp;level=".$level;
	for ($i=0; $i<$level; $i++) {
		$url .= "&amp;parent[".$i."]=".rawurlencode($exp[$level-$i-1]);
	}
	$url .= "&amp;ged=".rawurlencode($GEDCOM);
	return $url;
}

/**
 * get the first part of a place record
 * @param string $gedcom_place The original place to shorten
 * @return string  a shortened version of the place
 */
function get_place_short($gedcom_place) {
	global $GEDCOM, $SHOW_LIST_PLACES;
	if ($SHOW_LIST_PLACES==9) {
		return $gedcom_place;
	}
	$gedcom_place = trim($gedcom_place, " ,");
	$exp = explode(",", $gedcom_place);
	$place = "";
	for ($i=0; $i<$SHOW_LIST_PLACES && $i<count($exp); $i++) {
		if ($i>0) $place .= ", ";
		$place.=trim($exp[$i]);
	}
	return $place;
}

/**
 * get the last part of a place record
 * @param string $gedcom_place The original place to country
 * @return string  a country version of the place
 */
function getPlaceCountry($gedcom_place) {
	global $GEDCOM;
	$gedcom_place = trim($gedcom_place, " ,");
	$exp = explode(",", $gedcom_place);
	$place = trim($exp[count($exp)-1]);
	return $place;
}
