<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\DescendancyController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'descendancy.php');
require './includes/session.php';

$controller = new DescendancyController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<div id="descendancy-page"><h2><?php echo $controller->getPageTitle(); ?></h2>
	<form method="get" name="people" action="?">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<table class="list_table">
			<tbody>
				<tr>
					<td class="descriptionbox">
						<?php	echo I18N::translate('Individual'); ?>
					</td>
					<td class="optionbox">
						<input class="pedigree_form" data-autocomplete-type="INDI" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
						<?php echo FunctionsPrint::printFindIndividualLink('rootid'); ?>
					</td>
					<td rowspan="3" class="descriptionbox">
						<?php echo I18N::translate('Layout'); ?>
					</td>
					<td rowspan="3" class="optionbox">
						<input type="radio" name="chart_style" value="0" <?php echo $controller->chart_style == 0 ? 'checked' : ''; ?>>
						<?php echo  I18N::translate('List'); ?>
						<br>
						<input type="radio" name="chart_style" value="1" <?php echo $controller->chart_style == 1 ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Booklet'); ?>
						<br>
						<input type="radio" name="chart_style" value="2" <?php echo $controller->chart_style == 2 ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Individuals'); ?>
						<br>
						<input type="radio" name="chart_style" value="3" <?php echo $controller->chart_style == 3 ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Families'); ?>
					</td>
					<td rowspan="3" class="topbottombar">
						<input type="submit" value="<?php echo I18N::translate('View'); ?>">
					</td>
				</tr>
				<tr>
					<td class="descriptionbox">
						<?php echo I18N::translate('Generations'); ?>
					</td>
					<td class="optionbox">
						<?php echo FunctionsEdit::editFieldInteger('generations', $controller->generations, 2, $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS')); ?>
					</td>
				</tr>
				<tr>
					<td class="descriptionbox">
						<?php echo I18N::translate('Show details'); ?>
					</td>
					<td class="optionbox">
						<?php echo FunctionsEdit::twoStateCheckbox('show_full', $controller->showFull()); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>

<?php
if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
} else {
	switch ($controller->chart_style) {
	case 0: // List
		echo '<ul id="descendancy_chart" class="chart_common">';
		$controller->printChildDescendancy($controller->root, $controller->generations);
		echo '</ul>';
		break;
	case 1: // Booklet
		$show_cousins = true;
		echo '<div id="descendancy_booklet">';
		$controller->printChildFamily($controller->root, $controller->generations);
		echo '</div>';
		break;
	case 2: // Individual list
		$descendants = $controller->individualDescendancy($controller->root, $controller->generations, array());
		echo '<div id="descendancy-list">', FunctionsPrintLists::individualTable($descendants), '</div>';
		break;
	case 3: // Family list
		$descendants = $controller->familyDescendancy($controller->root, $controller->generations, array());
		echo '<div id="descendancy-list">', FunctionsPrintLists::familyTable($descendants), '</div>';
		break;
	}
}
?>
</div>
