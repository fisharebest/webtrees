<?php
/**
 * Parses gedcom file and displays a descendancy tree.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'descendancy.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/descendancy_ctrl.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new DescendancyController();
$controller->init();

print_header($controller->name." ".i18n::translate('Descendancy chart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// LBox =====================================================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// ==========================================================================================

echo '<table><tr><td valign="top"><h2>', i18n::translate('Descendancy chart'), ':<br />', PrintReady($controller->name), '</h2>';
echo WT_JS_START;
echo 'var pastefield; function paste_id(value) {pastefield.value=value;}';
echo WT_JS_END;

$gencount=0;
if ($view!="preview") {
	$show_famlink = true;
	echo '</td><td width="50px">&nbsp;</td><td><form method="get" name="people" action="?">';
	echo '<input type="hidden" name="show_full" value="', $controller->show_full, '" />';
	echo '<table class="list_table ', $TEXT_DIRECTION, '">';
	echo '<tr><td class="descriptionbox">';
	echo i18n::translate('Root Person ID'), help_link('desc_rootid'), '</td>';
	echo '<td class="optionbox">';
	echo '<input class="pedigree_form" type="text" id="pid" name="pid" size="3" value="', $controller->pid, '" />';
	print_findindi_link("pid", "");
	echo '</td>';
	echo '<td class="descriptionbox">';
	echo i18n::translate('Box width'), help_link('box_width'), '</td>';
	echo '<td class="optionbox"><input type="text" size="3" name="box_width" value="', $controller->box_width, '" />';
	echo '<b>%</b></td>';
	echo '<td rowspan="2" class="descriptionbox">';
	echo i18n::translate('Layout'), help_link('chart_style');
	echo '</td><td rowspan="2" class="optionbox">';
	echo '<input type="radio" name="chart_style" value="0"';
	if ($controller->chart_style==0) {
		echo ' checked="checked"';
	}
	echo '/>', i18n::translate('List');
	echo '<br /><input type="radio" name="chart_style" value="1"';
	if ($controller->chart_style==1) {
		echo ' checked="checked"';
	}
	echo '/>', i18n::translate('Booklet');
	echo '<br /><input type="radio" name="chart_style" value="2"';
	if ($controller->chart_style==2) {
		echo ' checked="checked"';
	}
	echo ' />', i18n::translate('Individuals');
	echo '<br /><input type="radio" name="chart_style" value="3"';
	if ($controller->chart_style==3) {
		echo ' checked="checked"';
	}
	echo ' />', i18n::translate('Families');
	echo '</td><td rowspan="2" class="topbottombar">';
	echo '<input type="submit" value="', i18n::translate('View'), '" />';
	echo '</td></tr>';
	echo '<tr><td class="descriptionbox">';
	echo i18n::translate('Generations'), help_link('desc_generations'), '</td>';
	echo '<td class="optionbox"><select name="generations">';
	for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
		echo '<option value="', $i, '"';
		if ($i==$controller->generations) {
			echo ' selected="selected"';
		}
		echo '>', $i, '</option>';
	}
	echo '</select></td><td class="descriptionbox">';
	echo i18n::translate('Show Details'), help_link('show_full');
	echo '</td><td class="optionbox"><input type="checkbox" value="';
	if ($controller->show_full) {
		echo '1" checked="checked" onclick="document.people.show_full.value=\'0\';"';
	} else {
		echo '0" onclick="document.people.show_full.value=\'1\';"';
	}
	echo '/></td></tr></table></form>';
}
echo '</td></tr></table>';
if (is_null($controller->descPerson)) {
	echo '<span class="error">', i18n::translate('The requested GEDCOM record could not be found.  This could be caused by a link to an invalid person or by a corrupt GEDCOM file.'), '</span>';
}
$controller->generations -= 1; // [ 1757792 ] Charts : wrong generations count

switch ($controller->chart_style) {
case 0: //-- list
	if ($show_full==0) {
		echo '<span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br /><br />';
	}
	echo '<ul style="list-style: none; display: block;" id="descendancy_chart', $TEXT_DIRECTION=='rtl' ? '_rtl' : '', '">';
	$controller->print_child_descendancy($controller->descPerson, $controller->generations);
	echo '</ul>';
	break;
case 1: //-- booklet
	if ($show_full==0) {
		echo '<span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br /><br />';
	}
	$show_cousins = true;
	$famids = find_sfamily_ids($controller->pid);
	if (count($famids)) {
		$controller->print_child_family($controller->descPerson, $controller->generations);
	}
	break;
case 2: //-- Individual list
	$descendants=indi_desc($controller->descPerson, $controller->generations, array());
	echo '<div class="center">';
	print_indi_table($descendants, i18n::translate('Descendancy chart').' : '.PrintReady($controller->name));
	echo '</div>';
	break;
case 3: //-- Family list
	$descendants=fam_desc($controller->descPerson, $controller->generations, array());
	echo '<div class="center">';
	print_fam_table($descendants, i18n::translate('Descendancy chart').' : '.PrintReady($controller->name));
	echo '</div>';
	break;
}
print_footer();

function indi_desc($person, $n, $array) {
	if ($n<0) {
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
	if ($n<0) {
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

?>
