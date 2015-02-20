<?php
namespace Fisharebest\Webtrees;

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

/**
 * Defined in session.php
 *
 * @global Tree   $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'familybook.php');
require './includes/session.php';

$controller = new FamilyBookController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<div id="familybook-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form method="get" name="people" action="?">
		<input type="hidden" name="ged" value="<?php echo Filter::escapeHtml(WT_GEDCOM); ?>">
		<table class="list_table">
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Individual'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="rootid" id="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="descriptionbox">
					<?php echo I18N::translate('Show details'); ?>
				</td>
				<td class="optionbox">
					<?php echo two_state_checkbox("show_full",$controller->showFull());?>
	  			</td>
				<td rowspan="3" class="topbottombar vmiddle">
					<input type="submit" value="<?php echo /* I18N: Submit button, on a form */ I18N::translate('View'); ?>">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Generations'); ?>
				</td>
				<td class="optionbox">
					<select name="generations">
						<?php
						for ($i = 2; $i <= $WT_TREE->getPreference('MAX_DESCENDANCY_GENERATIONS'); $i++) {
							echo '<option value="' . $i . '" ';
							if ($i == $controller->generations) {
								echo 'selected';
							}
							echo '>' . I18N::number($i) . '</option>';
						}
						?>
					</select>
				</td>
				<td rowspan="2" class="descriptionbox">
					<?php echo I18N::translate('Show spouses'), help_link('show_spouse'); ?>
				</td>
				<td rowspan="2" class="optionbox">
					<input type="checkbox" value="1" name="show_spouse" <?php if ($controller->show_spouse) echo " checked"; ?>>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Descent steps'), help_link('fambook_descent'); ?>
				</td>
				<td class="optionbox">
					<input type="text" size="3" name="descent" value="<?php echo $controller->descent; ?>">
				</td>
			</tr>
		</table>
	</form>
<div id="familybook_chart" style="z-index:1;">
<?php
if ($controller->root) {
	$controller->printFamilyBook($controller->root, $controller->descent);
}
?>
</div>
</div> <!-- close #familybook-page -->
