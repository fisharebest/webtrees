<?php
/**
 * Media Link Assistant Control module for webtrees
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage GEDFact_assistant
 * @version $Id$
 */

 if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $summary, $TEXT_DIRECTION, $censyear, $censdate;

$pid = safe_get('pid');

// echo $pid;

$year = "1901";
$censevent  = new WT_Event("1 CENS\n2 DATE 03 MAR".$year."");
$censdate   = $censevent->getDate();
$censyear   = $censdate->date1->y;
$ctry       = "UK";
// $married    = WT_Date::Compare($censdate, $marrdate);
$married=-1;


// Test to see if Base pid is filled in ============================
if ($pid=="") {
	echo "<br /><br />";
	echo "<b><font color=\"red\">YOU MUST enter a Base individual ID to be able to \"ADD\" Individual Links</font></b>";
	echo "<br /><br />";
} else {

	$person=WT_Person::getInstance($pid);
	// var_dump($person->getAllNames());
	$nam = $person->getAllNames();
	if (PrintReady($person->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($person->getDeathYear()); }
	if (PrintReady($person->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($person->getBirthYear()); }
	if ($married>=0 && isset($nam[1])) {
		$wholename = rtrim($nam[1]['fullNN']);
	} else {
		$wholename = rtrim($nam[0]['fullNN']);
	}
	$currpid=$pid;

	echo '<table width=400 class="facts_table center ', $TEXT_DIRECTION, '">';
	echo '<tr><td class="topbottombar" colspan="1">';
	echo '<b>', WT_I18N::translate('Family Navigator'), '</b>';
	echo '</td></tr>';
	echo '<tr>';
	//echo '<td class="optionbox wrap" valign="top" align="left" width="50%" >';
	//echo WT_I18N::translate('Add Family, and Search links');
	//echo '</td>';
	echo '<td valign="top" width=400>';
	//-- Search  and Add Family Members Area =========================================
	require WT_MODULES_DIR.'GEDFact_assistant/_MEDIA/media_3_search_add.php';
	echo '</td>';
	echo '</tr>';
	echo '</table>';

} // End IF test for Base pid
