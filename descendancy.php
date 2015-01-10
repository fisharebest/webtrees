<?php
// Displays a descendancy tree.
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

define('WT_SCRIPT_NAME', 'descendancy.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller = new WT_Controller_Descendancy();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>
<div id="descendancy-page"><h2><?php echo $controller->getPageTitle(); ?></h2>
	<form method="get" name="people" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<?php	echo WT_I18N::translate('Individual'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Box width'); ?>
				</td>
				<td class="optionbox">
				<input type="text" size="3" name="box_width" value="<?php echo $controller->box_width; ?>">
					<b>%</b>
				</td>
				<td rowspan="2" class="descriptionbox">
					<?php echo WT_I18N::translate('Layout'); ?>
				</td>
				<td rowspan="2" class="optionbox">
					<input type="radio" name="chart_style" value="0"<?php echo $controller->chart_style==0 ? ' checked="checked"' : ''; ?>>
					<?php echo  WT_I18N::translate('List'); ?>
					<br>
					<input type="radio" name="chart_style" value="1"<?php echo $controller->chart_style==1 ? ' checked="checked"' : ''; ?>>
					<?php echo WT_I18N::translate('Booklet'); ?>
					<br>
					<input type="radio" name="chart_style" value="2"<?php echo $controller->chart_style==2 ? ' checked="checked"' : ''; ?>>
					<?php echo WT_I18N::translate('Individuals'); ?>
					<br>
					<input type="radio" name="chart_style" value="3"<?php echo $controller->chart_style==3 ? ' checked="checked"' : ''; ?>>
					<?php echo WT_I18N::translate('Families'); ?>
				</td>
				<td rowspan="2" class="topbottombar">
					<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Generations'); ?>
				</td>
				<td class="optionbox">
					<?php echo edit_field_integers('generations', $controller->generations, 2, $MAX_DESCENDANCY_GENERATIONS); ?>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Show details'); ?>
				</td>
					<td class="optionbox">
						<input type="checkbox" value="<?php if ($controller->show_full) { echo '1" checked="checked" onclick="document.people.show_full.value=\'0\';"'; } else { echo '0" onclick="document.people.show_full.value=\'1\';"'; } ?>>
				</td>
			</tr>
		</table>
	</form>

<?php
if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
} else {
	switch ($controller->chart_style) {
	case 0: // List
		echo '<ul style="list-style: none; display: block;" id="descendancy_chart">';
		$controller->printChildDescendancy($controller->root, $controller->generations);
		echo '</ul>';
		break;
	case 1: // Booklet
		$show_cousins = true;
		echo '<div id="descendancy_chart">';
		$controller->printChildFamily($controller->root, $controller->generations);
		echo '</div>';
		break;
	case 2: // Individual list
		$descendants = $controller->individualDescendancy($controller->root, $controller->generations, array());
		echo '<div id="descendancy-list">', format_indi_table($descendants), '</div>';
		break;
	case 3: // Family list
		$descendants = $controller->familyDescendancy($controller->root, $controller->generations, array());
		echo '<div id="descendancy-list">', format_fam_table($descendants), '</div>';
		break;
	}
}
?>
</div>
