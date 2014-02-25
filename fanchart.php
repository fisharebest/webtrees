<?php
// View for the fan chart.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'fanchart.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Fanchart();

if (WT_Filter::getBool('img')) {
	Zend_Session::writeClose();
	$controller->generate_fan_chart('png');
	exit;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js');

?>
<div id="page-fan">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Individual'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->rootid; ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Layout'); ?>
				</td>
				<td class="optionbox">
					<?php echo select_edit_control('fan_style', $controller->getFanStyles(), null, $controller->fan_style); ?>
				</td>
				<td rowspan="2" class="topbottombar vmiddle">
					<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Generations'); ?>
				</td>
				<td class="optionbox">
					<?php echo edit_field_integers('generations', $controller->generations, 2, 9); ?>
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Width'), help_link('fan_width'); ?>
				</td>
				<td class="optionbox">
					<input type="text" size="3" name="fan_width" value="<?php echo $controller->fan_width; ?>"> %
				</td>
			</tr>
		</table>
	</form>
<?php

if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

if ($controller->root) {
	echo '<div id="fan_chart">', $controller->generate_fan_chart('html'), '</div>';
}
echo '</div>';
