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

use Fisharebest\Webtrees\Controller\BranchesController;

define('WT_SCRIPT_NAME', 'branches.php');
require './includes/session.php';

$controller = new BranchesController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<div id="branches-page">
	<h2 class="center"><?php echo $controller->getPageTitle(); ?></h2>
	<form name="surnlist" id="surnlist" action="branches.php">
		<table class="facts_table width50">
			<tbody>
				<tr>
					<td class="descriptionbox">
						<?php echo GedcomTag::getLabel('SURN'); ?>
					</td>
					<td class="optionbox">
						<input data-autocomplete-type="SURN" type="text" name="surname" id="SURN" value="<?php echo Filter::escapeHtml($controller->getSurname()); ?>" dir="auto">
						<input type="hidden" name="ged" id="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
						<input type="submit" value="<?php echo I18N::translate('View'); ?>">
						<p>
							<?php echo I18N::translate('Phonetic search'); ?>
						</p>
						<p>
							<input type="checkbox" name="soundex_std" id="soundex_std" value="1" <?php echo $controller->getSoundexStd() ? 'checked' : ''; ?>>
							<label for="soundex_std"><?php echo I18N::translate('Russell'); ?></label>
							<input type="checkbox" name="soundex_dm" id="soundex_dm" value="1" <?php echo $controller->getSoundexDm() ? 'checked' : ''; ?>>
							<label for="soundex_dm"><?php echo I18N::translate('Daitch-Mokotoff'); ?></label>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<ol>
		<?php echo $controller->getPatriarchsHtml(); ?>
	</ol>
</div>
