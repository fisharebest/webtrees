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

use Fisharebest\Webtrees\Controller\AncestryController;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'ancestry.php');
require './includes/session.php';

$MAX_PEDIGREE_GENERATIONS = $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS');

$controller = new AncestryController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<div id="ancestry-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="people" id="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<table class="list_table">
			<tbody>
				<tr>
					<td class="descriptionbox">
						<label for="rootid"><?php echo I18N::translate('Individual'); ?></label>
					</td>
					<td class="optionbox">
						<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
						<?php echo FunctionsPrint::printFindIndividualLink('rootid'); ?>
					</td>
					<td rowspan="3" class="descriptionbox">
						<label><?php echo I18N::translate('Layout'); ?></label>
					</td>
					<td rowspan="3" class="optionbox">
						<div>
							<label>
								<input type="radio" name="chart_style" value="0" onclick="statusDisable('cousins');" <?php echo $controller->chart_style == 0 ? 'checked' : ''; ?>>
								<?php echo I18N::translate('List'); ?>
							</label>
						</div>
						<div>
							<label>
								<input type="radio" name="chart_style" value="1" onclick="statusEnable('cousins');" <?php echo $controller->chart_style == 1 ? 'checked' : ''; ?>>
								<?php echo I18N::translate('Booklet'); ?>
							</label>
							<label>
								<?php echo FunctionsEdit::twoStateCheckbox('show_cousins', $controller->show_cousins, "id='cousins' " . ($controller->chart_style === 1 ? '' : 'disabled')); ?>
								<?php echo I18N::translate('Show cousins'); ?>
							</label>
						</div>
						<div>
							<label>
								<input type="radio" name="chart_style" value="2" onclick="statusDisable('cousins');" <?php echo $controller->chart_style == 2 ? 'checked' : ''; ?>>
								<?php echo I18N::translate('Individuals'); ?>
							</label>
						</div>
						<div>
							<label>
								<input type="radio" name="chart_style" value="3" onclick="statusDisable('cousins');" <?php echo $controller->chart_style == 3 ? 'checked' : ''; ?>>
								<?php echo I18N::translate('Families'); ?>
							</label>
						</div>
					</td>
					<td rowspan="3" class="facts_label03">
					<input type="submit" value="<?php echo I18N::translate('View'); ?>">
					</td>
				</tr>
				<tr>
					<td class="descriptionbox">
						<?php echo '<label>', I18N::translate('Generations'), '</label>'; ?>
					</td>
					<td class="optionbox">
						<select name="PEDIGREE_GENERATIONS">
							<?php
								for ($i = 2; $i <= $MAX_PEDIGREE_GENERATIONS; $i++) {
									echo '<option value="', $i, '" ';
									if ($i == $controller->generations) {
										echo 'selected';
									}
									echo '>', I18N::number($i), '</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="descriptionbox">
						<?php echo '<label>', I18N::translate('Show details'), '</label>'; ?>
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

	return;
}
switch ($controller->chart_style) {
case 0:
	// List
	echo '<ul id="ancestry_chart" class="chart_common">';
	$controller->printChildAscendancy($controller->root, 1, $controller->generations - 1);
	echo '</ul>';
	echo '<br>';
	break;
case 1:
	echo '<div id="ancestry_booklet">';
	// Booklet
	// first page : show indi facts
	FunctionsPrint::printPedigreePerson($controller->root, $controller->showFull());
	// process the tree
	$ancestors = $controller->sosaAncestors($controller->generations - 1);
	$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders

	foreach ($ancestors as $sosa => $individual) {
		foreach ($individual->getChildFamilies() as $family) {
			FunctionsCharts::printSosaFamily($family->getXref(), $individual->getXref(), $sosa, '', '', '', $controller->show_cousins, $controller->showFull());
		}
	}
	echo '</div>';
	break;
case 2:
	// Individual list
	$ancestors = $controller->sosaAncestors($controller->generations);
	$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders
	echo '<div id="ancestry-list">', FunctionsPrintLists::individualTable($ancestors, 'sosa'), '</div>';
	break;
case 3:
	// Family list
	$ancestors = $controller->sosaAncestors($controller->generations - 1);
	$ancestors = array_filter($ancestors); // The SOSA array includes empty placeholders
	$families  = array();
	foreach ($ancestors as $individual) {
		foreach ($individual->getChildFamilies() as $family) {
			$families[$family->getXref()] = $family;
		}
	}
	echo '<div id="ancestry-list">', FunctionsPrintLists::familyTable($families), '</div>';
	break;
}
echo '</div>';
