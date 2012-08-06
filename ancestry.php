<?php
// Displays pedigree tree as a printable booklet
// with Sosa-Stradonitz numbering system
// ($rootid=1, father=2, mother=3 ...)
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

define('WT_SCRIPT_NAME', 'ancestry.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Ancestry();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
	->addInlineJavascript('var pastefield; function paste_id(value) { pastefield.value=value; }'); // For the 'find indi' link

// LightBox
if (WT_USE_LIGHTBOX) {
	$album = new lightbox_WT_Module();
	$album->getPreLoadContent();
}
echo '<div id="ancestry-page">';
echo '<table><tr><td valign="middle">';
echo '<h2>', $controller->getPageTitle(), '</h2>';
?>
</td><td width="50px">&nbsp;</td><td><form name="people" id="people" method="get" action="?">
<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>">
<input type="hidden" name="show_cousins" value="<?php echo $controller->show_cousins; ?>">
<table class="list_table">

	<!-- // NOTE: Root ID -->
<tr>
	<td class="descriptionbox"><?php echo WT_I18N::translate('Individual'); ?></td>
<td class="optionbox">
<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
<?php echo print_findindi_link('rootid'); ?>
</td>

<!-- // NOTE: Box width -->
<td class="descriptionbox"><?php echo WT_I18N::translate('Box width'); ?></td>
<td class="optionbox"><input type="text" size="3" name="box_width" value="<?php echo htmlspecialchars($box_width); ?>"> <b>%</b>
</td>

<!-- // NOTE: chart style -->
<td rowspan="2" class="descriptionbox"><?php echo WT_I18N::translate('Layout'); ?></td>
<td rowspan="2" class="optionbox">
<input type="radio" name="chart_style" value="0"
<?php
if ($controller->chart_style==0) {
	echo ' checked="checked"';
}
echo ' onclick="statusDisable(\'cousins\');';
echo '">', WT_I18N::translate('List');
echo '<br><input type="radio" name="chart_style" value="1"';
if ($controller->chart_style==1) {
	echo ' checked="checked"';
}
echo ' onclick="statusEnable(\'cousins\');';
echo '">', WT_I18N::translate('Booklet');
?>

<!-- // NOTE: show cousins -->
<br>
<?php
echo '<input ';
if ($controller->chart_style==0) {
	echo 'disabled="disabled" ';
}
echo 'id="cousins" type="checkbox" value="';
if ($controller->show_cousins) {
	echo '1" checked="checked" onclick="document.people.show_cousins.value=\'0\';"';
} else {
	echo '0" onclick="document.people.show_cousins.value=\'1\';"';
}
echo '>';
echo WT_I18N::translate('Show cousins');

echo '<br><input type="radio" name="chart_style" value="2"';
if ($controller->chart_style==2) {
	echo ' checked="checked" ';
}
echo ' onclick="statusDisable(\'cousins\');"';
echo '>', WT_I18N::translate('Individuals');
echo '<br><input type="radio" name="chart_style" value="3"';
echo ' onclick="statusDisable(\'cousins\');"';
if ($controller->chart_style==3) {
	echo ' checked="checked" ';
}
echo '>', WT_I18N::translate('Families');
?>
</td>

<!-- // NOTE: submit -->
<td rowspan="2" class="facts_label03">
<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
</td></tr>

<!-- // NOTE: generations -->
<tr><td class="descriptionbox">
<?php
echo WT_I18N::translate('Generations'); ?></td>

<td class="optionbox">
<select name="PEDIGREE_GENERATIONS">
<?php
for ($i=2; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
echo '<option value="', $i, '"';
if ($i==$OLD_PGENS) {
	echo ' selected="selected"';
}
	echo '>', WT_I18N::number($i), '</option>';
}
?>
</select>
</td>
<!-- // NOTE: show full -->
<td class="descriptionbox">
<?php
echo WT_I18N::translate('Show Details');
?>
</td>
<td class="optionbox">
<input type="checkbox" value="<?php
if ($controller->show_full) {
	echo '1" checked="checked" onclick="document.people.show_full.value=\'0\';';
} else {
	echo '0" onclick="document.people.show_full.value=\'1\';';
}
?>"
>
</td></tr>
</table>
</form>
</td></tr></table>

<?php

if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

switch ($controller->chart_style) {
case 0:
	// List
	$pidarr=array();
	echo '<ul id="ancestry_chart">';
	$controller->print_child_ascendancy($controller->root, 1, $OLD_PGENS-1);
	echo '</ul>';
	echo '<br>';
	break;
case 1:
	// TODO: this should be a parameter to a function, not a global
	$show_cousins=$controller->show_cousins;
	echo '<div id="ancestry_chart">';
	// Booklet
	// first page : show indi facts
	print_pedigree_person($controller->root, 1, 1);
	// expand the layer
	echo '<script>expandbox("', $controller->root->getXref(), '.1", 2);</script>';
	// process the tree
	$treeid=ancestry_array($controller->root->getXref(), $PEDIGREE_GENERATIONS-1);
	foreach ($treeid as $i=>$pid) {
		if ($pid) {
			$person=WT_Person::getInstance($pid);
			if ($person) {
				foreach ($person->getChildFamilies() as $family) {
					print_sosa_family($family->getXref(), $pid, $i);
				}
			}
		}
	}
	echo '</div>';
	break;
case 2:
	// Individual list
	$treeid=ancestry_array($controller->root->getXref(), $PEDIGREE_GENERATIONS);
	echo '<div id="ancestry-list">';
	echo format_indi_table($treeid, 'sosa');
	echo '</div>';
	break;
case 3:
	// Family list
	$treeid=ancestry_array($controller->root->getXref(), $PEDIGREE_GENERATIONS-1);
	$famlist=array();
	foreach ($treeid as $pid) {
		$person=WT_Person::getInstance($pid);
		if (is_null($person)) {
			continue;
		}
		foreach ($person->getChildFamilies() as $famc) {
			$famlist[$famc->getXref()]=$famc;
		}
	}
	echo '<div id="ancestry-list">';
	echo format_fam_table($famlist, $controller->getPageTitle());
	echo '</div>';
	break;
}
echo '</div>'; // close div ancestry-page