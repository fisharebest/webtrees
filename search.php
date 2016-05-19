<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

use Fisharebest\Webtrees\Controller\SearchController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'search.php');
require './includes/session.php';

$controller = new SearchController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<script>
function checknames(frm) {
	action = "<?php echo $controller->action; ?>";
	if (action === "general") {
		if (frm.query.value.length<2) {
			alert("<?php echo I18N::translate('Please enter more than one character.'); ?>");
			frm.query.focus();
			return false;
		}
	} else if (action === "soundex") {
		year = frm.year.value;
		fname = frm.firstname.value;
		lname = frm.lastname.value;
		place = frm.place.value;

		if (year == "") {
			if (fname.length < 2 && lname.length < 2 && place.length < 2) {
				alert("<?php echo I18N::translate('Please enter more than one character.'); ?>");
				return false;
			}
		}

		if (year != "") {
			if (fname === "" && lname === "" && place === "") {
				alert("<?php echo I18N::translate('Please enter a given name, surname, or place in addition to the year'); ?>");
				frm.firstname.focus();
				return false;
			}
		}
		return true;
	}
	return true;
}
</script>

<div id="search-page">
<h2><?php echo $controller->getPageTitle(); ?></h2>

<?php if ($controller->action === 'general'): ?>

	<form name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="general">
		<input type="hidden" name="isPostBack" value="true">
		<div id="search-page-table">
			<div class="label">
				<?php echo I18N::translate('Search for'); ?>
			</div>
			<div class="value">
				<input id="query" type="text" name="query" value="<?php echo Filter::escapeHtml($controller->query); ?>" size="40" autofocus>
				<?php echo FunctionsPrint::printSpecialCharacterLink('query'); ?>
			</div>
			<div class="label">
				<?php echo I18N::translate('Records'); ?>
			</div>
			<div class="value">
				<label>
					<input type="checkbox" <?php echo $controller->srindi; ?> value="checked" name="srindi">
					<?php echo I18N::translate('Individuals'); ?>
				</label>
				<br>
				<label>
					<input type="checkbox" <?php echo $controller->srfams; ?> value="checked" name="srfams">
					<?php echo I18N::translate('Families'); ?>
				</label>
				<br>
				<label>
					<input type="checkbox" <?php echo $controller->srsour; ?> value="checked" name="srsour">
					<?php echo I18N::translate('Sources'); ?>
				</label>
				<br>
				<label>
					<input type="checkbox" <?php echo $controller->srnote; ?> value="checked" name="srnote">
					<?php echo I18N::translate('Shared notes'); ?>
				</label>
			</div>
			<div class="label">
				<?php echo I18N::translate('Associates'); ?>
			</div>
			<div class="value">
				<input type="checkbox" id="showasso" name="showasso" value="on" <?php echo $controller->showasso === 'on' ? 'checked' : ''; ?>>
				<label for="showasso">
					<?php echo I18N::translate('Show related individuals/families'); ?>
				</label>
			</div>
			<?php if (count(Tree::getAll()) > 1 && Site::getPreference('ALLOW_CHANGE_GEDCOM')): ?>
			<?php if (count(Tree::getAll()) > 3): ?>
			<div class="label"></div>
			<div class="value">
				<input type="button" value="<?php echo /* I18N: select all (of the family trees) */ I18N::translate('select all'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', true);});return false;">
				<input type="button" value="<?php echo /* I18N: select none (of the family trees) */ I18N::translate('select none'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', false);});return false;">
				<?php if (count(Tree::getAll()) > 10): ?>
				<input type="button" value="<?php echo I18N::translate('invert selection'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', !jQuery(this).attr('checked'));});return false;">
				<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="label">
				<?php echo I18N::translate('Family trees'); ?>
			</div>
			<div id="search_trees" class="value">
				<?php foreach (Tree::getAll() as $tree): ?>
				<p>
					<input type="checkbox" <?php echo in_array($tree, $controller->search_trees) ? 'checked' : ''; ?> value="yes" id="tree_<?php echo $tree->getTreeId(); ?>" name="tree_<?php echo $tree->getTreeId(); ?>">
					<label for="tree_'<?php echo $tree->getTreeId(); ?>">
						<?php echo $tree->getTitleHtml(); ?>
					</label>
				</p>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="label"></div>
			<div class="value">
				<input type="submit" value="<?php echo /* I18N: A button label */ I18N::translate('Search'); ?>">
			</div>
		</div>
	</form>

<?php endif; ?>
<?php if ($controller->action === 'replace'): ?>

	<form method="post" name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="replace">
		<input type="hidden" name="isPostBack" value="true">
		<div id="search-page-table">
			<div class="label">
				<?php echo I18N::translate('Search for'); ?>
			</div>
			<div class="value">
				<input name="query" value="<?php echo Filter::escapeHtml($controller->query); ?>" type="text" autofocus>
			</div>
			<div class="label">
				<?php echo I18N::translate('Replace with'); ?>
			</div>
			<div class="value">
				<input name="replace" value="<?php echo Filter::escapeHtml($controller->replace); ?>" type="text">
			</div>
			<script>
				function checkAll(box) {
					if (box.checked) {
						box.form.replaceNames.disabled = true;
						box.form.replacePlaces.disabled = true;
						box.form.replacePlacesWord.disabled = true;
						box.form.replaceNames.checked = false;
						box.form.replacePlaces.checked = false;
						box.form.replacePlacesWord.checked = false;
					} else {
						box.form.replaceNames.disabled = false;
						box.form.replacePlaces.disabled = false;
						box.form.replacePlacesWord.disabled = false;
						box.form.replaceNames.checked = true;
					}
				}
			</script>
			<div class="label">
				<?php echo I18N::translate('Search'); ?>
			</div>
			<div class="value">
				<p>
					<label>
					<input <?php echo $controller->replaceAll; ?> onclick="checkAll(this);" value="checked" name="replaceAll" type="checkbox">
						<?php echo I18N::translate('Entire record'); ?>
					</label>
					<hr>
				</p>
				<p>
					<label>
						<input <?php echo $controller->replaceNames; ?> <?php echo $controller->replaceAll ? 'disabled' : ''; ?> value="checked" name="replaceNames" type="checkbox">
						<?php echo I18N::translate('Names'); ?>
					</label>
				</p>
				<p>
					<label>
						<input <?php echo $controller->replacePlaces; ?> <?php echo $controller->replaceAll ? 'disabled' : ''; ?> value="checked" name="replacePlaces" type="checkbox">
						<?php echo I18N::translate('Places'); ?>
					</label>
				</p>
				<p>
					<label>
					<input <?php echo $controller->replacePlacesWord; ?> <?php echo $controller->replaceAll ? 'disabled' : ''; ?> value="checked" name="replacePlacesWord" type="checkbox">
						<?php echo I18N::translate('Whole words only'); ?>
					</label>
				</p>
			</div>

			<div class="label"></div>
			<div class="value">
				<input type="submit" value="<?php echo /* I18N: A button label */ I18N::translate('Replace'); ?>">
			</div>
		</div>
	</form>

<?php endif; ?>
<?php if ($controller->action == "soundex"): ?>

	<form name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="soundex">
		<input type="hidden" name="isPostBack" value="true">
		<div id="search-page-table">
			<div class="label">
				<?php echo I18N::translate('Given name'); ?>
			</div>
			<div class="value">
				<input type="text" data-autocomplete-type="GIVN" name="firstname" value="<?php echo Filter::escapeHtml($controller->firstname); ?>" autofocus>
			</div>
			<div class="label">
				<?php echo I18N::translate('Surname'); ?>
			</div>
			<div class="value">
				<input type="text" data-autocomplete-type="SURN" name="lastname" value="<?php echo Filter::escapeHtml($controller->lastname); ?>">
			</div>
			<div class="label">
				<?php echo I18N::translate('Place'); ?>
			</div>
			<div class="value">
				<input type="text"  data-autocomplete-type="PLAC2" name="place" value="<?php echo Filter::escapeHtml($controller->place); ?>">
			</div>
			<div class="label">
				<?php echo I18N::translate('Year'); ?>
			</div>
			<div class="value"><input type="text" name="year" value="<?php echo Filter::escapeHtml($controller->year); ?>">
			</div>
			<div class="label">
				<?php echo I18N::translate('Phonetic algorithm'); ?>
			</div>
			<div class="value">
				<p>
					<input type="radio" name="soundex" value="Russell" <?php echo $controller->soundex === 'Russell' ? 'checked' : ''; ?>>
					<?php echo I18N::translate('Russell'); ?>
				</p>
				<p>
					<input type="radio" name="soundex" value="DaitchM" <?php echo $controller->soundex === 'DaitchM' || $controller->soundex === '' ? 'checked' : ''; ?>>
					<?php echo I18N::translate('Daitch-Mokotoff'); ?>
				</p>
			</div>
			<div class="label">
				<?php echo I18N::translate('Associates'); ?>
			</div>
			<div class="value">
				<input type="checkbox" name="showasso" value="on" <?php echo $controller->showasso === 'on' ? 'checked' : ''; ?>>
				<?php echo I18N::translate('Show related individuals/families'); ?>
			</div>
			<?php if (count(Tree::getAll()) > 1 && Site::getPreference('ALLOW_CHANGE_GEDCOM')): ?>
				<?php if (count(Tree::getAll()) > 3): ?>
					<div class="label"></div>
					<div class="value">
						<input type="button" value="<?php echo /* I18N: select all (of the family trees) */ I18N::translate('select all'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', true);});return false;">
						<input type="button" value="<?php echo /* I18N: select none (of the family trees) */ I18N::translate('select none'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', false);});return false;">
						<?php if (count(Tree::getAll()) > 10): ?>
							<input type="button" value="<?php echo I18N::translate('invert selection'); ?>" onclick="jQuery('#search_trees :checkbox').each(function(){jQuery(this).attr('checked', !jQuery(this).attr('checked'));});return false;">
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="label">
					<?php echo I18N::translate('Family trees'); ?>
				</div>
				<div id="search_trees" class="value">
					<?php foreach (Tree::getAll() as $tree): ?>
						<p>
							<input type="checkbox" <?php echo in_array($tree, $controller->search_trees) ? 'checked' : ''; ?> value="yes" id="tree_<?php echo $tree->getTreeId(); ?>" name="tree_<?php echo $tree->getTreeId(); ?>">
							<label for="tree_'<?php echo $tree->getTreeId(); ?>">
								<?php echo $tree->getTitleHtml(); ?>
							</label>
						</p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="label"></div>
			<div class="value">
				<input type="submit" value="<?php echo  /* I18N: A button label */ I18N::translate('Search'); ?>">
			</div>
		</div>
	</form>

<?php endif; ?>

<?php $controller->printResults(); ?>

</div>
