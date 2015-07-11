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

use Fisharebest\Webtrees\Controller\HourglassController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'hourglass.php');
require './includes/session.php';

$controller = new HourglassController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();')
	->setupJavascript();

?>
<div id="hourglass-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form method="get" name="people" action="?">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<table class="list_table">
			<tbody>
				<tr>
					<td class="descriptionbox">
						<?php echo I18N::translate('Individual'); ?>
					</td>
					<td class="optionbox">
						<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
						<?php echo FunctionsPrint::printFindIndividualLink('pid'); ?>
					</td>
					<td class="descriptionbox">
						<?php echo I18N::translate('Show details'); ?>
					</td>
					<td class="optionbox">
						<?php echo FunctionsEdit::twoStateCheckbox('show_full', $controller->showFull()); ?>
					</td>
					<td rowspan="3" class="topbottombar vmiddle">
						<input type="submit" value="<?php echo I18N::translate('View'); ?>">
					</td>
				</tr>
				<tr>
					<td class="descriptionbox" >
						<?php echo I18N::translate('Generations'); ?>
					</td>
					<td class="optionbox">
						<?php echo FunctionsEdit::editFieldInteger('generations', $controller->generations, 2, $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS')); ?>
					</td>
					<td class="descriptionbox">
						<?php echo I18N::translate('Show spouses'); ?>
					</td>
					<td class="optionbox">
						<input type="checkbox" value="1" name="show_spouse" <?php echo $controller->show_spouse ? 'checked' : ''; ?>>
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<div id="hourglass_chart" style="width:98%; z-index:1;">
		<table>
			<tr>
				<td style="vertical-align:middle">
					<?php $controller->printDescendency($controller->root, 1); ?>
				</td>
				<td style="vertical-align:middle">
					<?php $controller->printPersonPedigree($controller->root, 1); ?>
				</td>
			</tr>
		</table>
	</div>
</div>
