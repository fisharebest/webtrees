<?php
// Displays a descendancy tree.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

define('WT_SCRIPT_NAME', 'descendancy.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Descendancy();
$controller
	->pageHeader()
	->addInlineJavaScript('var pastefield; function paste_id(value) { pastefield.value=value; }') // For the "find indi" link
	->addExternalJavaScript('js/autocomplete.js');

if (WT_USE_LIGHTBOX) {
	$album = new lightbox_WT_Module();
	$album->getPreLoadContent();
}

echo '<table><tr><td valign="top"><h2>', $controller->getPageTitle(), '</h2>';
echo '</td><td width="50px">&nbsp;</td><td><form method="get" name="people" action="?">';
echo '<input type="hidden" name="ged" value="', WT_GEDCOM, '">';
echo '<input type="hidden" name="show_full" value="', $controller->show_full, '">';
echo '<table class="list_table">';
echo '<tr><td class="descriptionbox">';
echo WT_I18N::translate('Individual'), '</td>';
echo '<td class="optionbox">';
echo '<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="', $controller->rootid, '"> ';
echo print_findindi_link('rootid');
echo '</td>';
echo '<td class="descriptionbox">';
echo WT_I18N::translate('Box width'), '</td>';
echo '<td class="optionbox"><input type="text" size="3" name="box_width" value="', $controller->box_width, '">';
echo '<b>%</b></td>';
echo '<td rowspan="2" class="descriptionbox">';
echo WT_I18N::translate('Layout');
echo '</td><td rowspan="2" class="optionbox">';
echo '<input type="radio" name="chart_style" value="0"';
if ($controller->chart_style==0) {
	echo ' checked="checked"';
}
echo '>', WT_I18N::translate('List');
echo '<br><input type="radio" name="chart_style" value="1"';
if ($controller->chart_style==1) {
	echo ' checked="checked"';
}
echo '>', WT_I18N::translate('Booklet');
echo '<br><input type="radio" name="chart_style" value="2"';
if ($controller->chart_style==2) {
	echo ' checked="checked"';
}
echo '>', WT_I18N::translate('Individuals');
echo '<br><input type="radio" name="chart_style" value="3"';
if ($controller->chart_style==3) {
	echo ' checked="checked"';
}
echo '>', WT_I18N::translate('Families');
echo '</td><td rowspan="2" class="topbottombar">';
echo '<input type="submit" value="', WT_I18N::translate('View'), '">';
echo '</td></tr>';
echo '<tr><td class="descriptionbox">';
echo WT_I18N::translate('Generations'), '</td>';
echo '<td class="optionbox"><select name="generations">';
for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
	echo '<option value="', $i, '"';
	if ($i==$controller->generations) {
		echo ' selected="selected"';
	}
	echo '>', WT_I18N::number($i), '</option>';
}
echo '</select></td><td class="descriptionbox">';
echo WT_I18N::translate('Show Details');
echo '</td><td class="optionbox"><input type="checkbox" value="';
if ($controller->show_full) {
	echo '1" checked="checked" onclick="document.people.show_full.value=\'0\';"';
} else {
	echo '0" onclick="document.people.show_full.value=\'1\';"';
}
echo '></td></tr></table></form>';
echo '</td></tr></table>';

if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

switch ($controller->chart_style) {
case 0: //-- list
	echo '<ul style="list-style: none; display: block;" id="descendancy_chart">';
	$controller->print_child_descendancy($controller->root, $controller->generations);
	echo '</ul>';
	break;
case 1: //-- booklet
	echo '<div id="descendancy_chart">';
	$show_cousins = true;
	$controller->print_child_family($controller->root, $controller->generations);
	echo '</div>';
	break;
case 2: //-- Individual list
	$descendants=indi_desc($controller->root, $controller->generations, array());
	echo '<div id="descendancy-list">';
	echo format_indi_table($descendants, WT_I18N::translate('Descendants of %s', $controller->name));
	echo '</div>';
	break;
case 3: //-- Family list
	$descendants=fam_desc($controller->root, $controller->generations, array());
	echo '<div id="descendancy-list">';
	echo format_fam_table($descendants, WT_I18N::translate('Descendants of %s', $controller->name));
	echo '</div>';
	break;
}

function indi_desc($person, $n, $array) {
	if ($n<1) {
		return $array;
	}
	$array[$person->getXref()]=$person;
	foreach ($person->getSpouseFamilies() as $family) {
		$spouse=$family->getSpouse($person);
		if (isset($spouse)) $array[$spouse->getXref()]=$spouse;
		foreach ($family->getChildren() as $child) {
			$array=indi_desc($child, $n-1, $array);
		}
	}
	return $array;
}

function fam_desc($person, $n, $array) {
	if ($n<1) {
		return $array;
	}
	foreach ($person->getSpouseFamilies() as $family) {
		$array[$family->getXref()]=$family;
		foreach ($family->getChildren() as $child) {
			$array=fam_desc($child, $n-1, $array);
		}
	}
	return $array;
}
