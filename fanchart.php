<?php
// View for the fan chart.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

$controller = new WT_Controller_Fanchart();

if (WT_Filter::getBool('img')) {
	Zend_Session::writeClose();
	header('Content-Type: image/png');
	echo $controller->generateFanChart('png', $fanChart); // $fanChart comes from the theme
	return;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('
		autocomplete();
		var WT_FANCHART = (function() {
			jQuery("area")
				.click(function (e) {
					e.stopPropagation();
					e.preventDefault();
					var target = jQuery(this.hash);
					target
						// position the menu centered immediately above the mouse click position and
						// make sure it doesn’t end up off the screen
						.css({
							left: Math.max(0 ,e.pageX - (target.outerWidth()/2)),
							top:  Math.max(0, e.pageY - target.outerHeight())
						})
						.toggle()
						.siblings(".fan_chart_menu").hide();
				});
			jQuery(".fan_chart_menu")
				.on("click", "a", function(e) {
					e.stopPropagation();
				});
			jQuery("#fan_chart")
				.click(function(e) {
					jQuery(".fan_chart_menu").hide();
				});
			return "' . strip_tags($controller->root->getFullName()) . '";
		})();
	');

?>
<div id="page-fan">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<label for="rootid">
						<?php echo WT_I18N::translate('Individual'); ?>
					</label>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="descriptionbox">
					<label for="fan_style">
						<?php echo WT_I18N::translate('Layout'); ?>
						</label>
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
					<label for="generations">
						<?php echo WT_I18N::translate('Generations'); ?>
					</label>
				</td>
				<td class="optionbox">
					<?php echo edit_field_integers('generations', $controller->generations, 2, 9); ?>
				</td>
				<td class="descriptionbox">
					<label for="fan_width">
						<?php echo WT_I18N::translate('Width'), help_link('fan_width'); ?>
						</label>
				</td>
				<td class="optionbox">
					<input type="text" size="3" id="fan_width" name="fan_width" value="<?php echo $controller->fan_width; ?>"> %
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
	echo '<div id="fan_chart">', $controller->generateFanChart('html', $fanChart), '</div>';
}
echo '</div>';
