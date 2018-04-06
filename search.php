<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

require 'includes/session.php';

$controller = new SearchController;
$controller->pageHeader();

?>
<script>
function checknames(frm) {
	action = "<?= $controller->action ?>";
	if (action === "general") {
		if (frm.query.value.length<2) {
			alert("<?= I18N::translate('Please enter more than one character.') ?>");
			frm.query.focus();
			return false;
		}
	} else if (action === "soundex") {
		year = frm.year.value;
		fname = frm.firstname.value;
		lname = frm.lastname.value;
		place = frm.place.value;

		if (year === "") {
			if (fname.length < 2 && lname.length < 2 && place.length < 2) {
				alert("<?= I18N::translate('Please enter more than one character.') ?>");
				return false;
			}
		}

		if (year !== "") {
			if (fname === "" && lname === "" && place === "") {
				alert("<?= I18N::translate('Please enter a given name, surname, or place in addition to the year') ?>");
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
	<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>
	<?php if ($controller->action === 'general'): ?>
	<form class="wt-page-options wt-page-options-search hidden-print" name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="general">
		<input type="hidden" name="isPostBack" value="true">
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="query">
			<?= I18N::translate('Search for') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="input-group input-group-sm">
					<input id="query" class="form-control form-control-sm" type="text" name="query" value="<?= e($controller->query) ?>" autofocus>
					<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsPrint::printSpecialCharacterLink('query') ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<fieldset class="form-group">
			<div class="row">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Records') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value">
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input" checked value="checked" name="srindi" type="checkbox">
						<?= I18N::translate('Individuals') ?>
						</label>
					</div>
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input"  value="checked" name="srfams" type="checkbox">
						<?= I18N::translate('Families') ?>
						</label>
					</div>
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input" value="checked" name="srsour" type="checkbox">
						<?= I18N::translate('Sources') ?>
						</label>
					</div>
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input" value="checked" name="srnote" type="checkbox">
						<?= I18N::translate('Shared notes') ?>
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label">
			<?= I18N::translate('Associates') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="form-check">
					<label class="form-check-label">
					<input class="form-check-input" name="showasso" value="checked" type="checkbox">
					<?= I18N::translate('Show related individuals/families') ?>
					</label>
				</div>
			</div>
		</div>

		<?php if (count(Tree::getAll()) > 1 && Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1'): ?>
		<fieldset class="form-group">
			<div class="row">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Family trees') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value pt-2">
					<div class="d-flex justify-content-between">
						<div id="search-trees" class="form-check">
							<?php foreach (Tree::getAll() as $tree): ?>
							<div class="col px-0">
								<label class="form-check-label">
									<input class="form-check form-check-input" type="checkbox" <?= in_array($tree, $controller->search_trees) ? 'checked' : '' ?> value="yes" id="tree_<?= $tree->getTreeId() ?>" name="tree_<?= $tree->getTreeId() ?>">
									<?= e($tree->getTitle()) ?>
								</label>
							</div>
							<?php endforeach ?>
						</div>
						<?php if (count(Tree::getAll()) > 3): ?>
						<div class="d-row align-self-end mb-2">
							<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select all (of the family trees) */ I18N::translate('select all') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', true);});return false;">
							<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select none (of the family trees) */ I18N::translate('select none') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', false);});return false;">
							<?php if (count(Tree::getAll()) > 10): ?>
								<input type="button" value="<?= I18N::translate('invert selection') ?>" onclick="$('#search_trees :checkbox').each(function(){$(this).attr('checked', !$(this).attr('checked'));});return false;">
							<?php endif ?>
						</div>
						<?php endif ?>
					</div>
				</div>
			</div>
		</fieldset>
		<?php endif ?>

		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label"></label>
			<div class="col-sm-9 wt-page-options-value">
				<input type="submit" class="btn btn-primary" value="<?=  /* I18N: A button label. */ I18N::translate('search') ?>">
			</div>
		</div>
	</form>

<?php endif ?>
<?php if ($controller->action === 'replace'): ?>
	<form class="wt-page-options wt-page-options-ancestors-chart hidden-print" method="post" name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="replace">
		<input type="hidden" name="isPostBack" value="true">
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Search for') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<input class="form-control form-control-sm" name="query" value="<?= e($controller->query) ?>" type="text" autofocus>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Replace with') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<input class="form-control form-control-sm" name="replace" value="<?= e($controller->replace) ?>" type="text">
			</div>
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
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label">
			<?= /* I18N: A button label. */ I18N::translate('Search') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="form-check mt-2">
					<label class="form-check-label">
						<input class="form-check-input" <?= $controller->replaceAll ?> onclick="checkAll(this);" value="checked" name="replaceAll" type="checkbox">
						<?= I18N::translate('Entire record') ?>
					</label>
				</div>
				<hr>
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" <?= $controller->replaceNames ?> <?= $controller->replaceAll ? 'disabled' : '' ?> value="checked" name="replaceNames" type="checkbox">
						<?= I18N::translate('Names') ?>
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" <?= $controller->replacePlaces ?> <?= $controller->replaceAll ? 'disabled' : '' ?> value="checked" name="replacePlaces" type="checkbox">
						<?= I18N::translate('Places') ?>
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input class="form-check-input" <?= $controller->replacePlacesWord ?> <?= $controller->replaceAll ? 'disabled' : '' ?> value="checked" name="replacePlacesWord" type="checkbox">
						<?= I18N::translate('Whole words only') ?>
					</label>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label"></label>
			<div class="col-sm-9 wt-page-options-value">
				<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('replace') ?>">
			</div>
		</div>
	</form>
<?php endif ?>
<?php if ($controller->action === 'soundex'): ?>
	<form class="wt-page-options wt-page-options-ancestors-chart hidden-print" name="searchform" onsubmit="return checknames(this);">
		<input type="hidden" name="action" value="soundex">
		<input type="hidden" name="isPostBack" value="true">
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="firstname">
				<?= I18N::translate('Given name') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="input-group input-group-sm">
					<input class= "form-control form-control-sm" type="text" name="firstname" id="firstname" value="<?= e($controller->firstname) ?>" autofocus>
					<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsPrint::printSpecialCharacterLink('query') ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label"  for="lastname">
				<?= I18N::translate('Surname') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="input-group input-group-sm">
					<input class="form-control form-control-sm" type="text" name="lastname" id="lastname" value="<?= e($controller->lastname) ?>">
					<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsPrint::printSpecialCharacterLink('query') ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="place">
				<?= I18N::translate('Place') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<input class="form-control form-control-sm" type="text" name="place" id="place" value="<?= e($controller->place) ?>">
			</div>
		</div>
		<div class="row form-group">
			<label class="col-sm-3 col-form-label wt-page-options-label" for="year">
				<?= I18N::translate('Year') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<input class="form-control form-control-sm" type="text" name="year" id="year" value="<?= e($controller->year) ?>">
			</div>
		</div>
		<fieldset class="form-group">
			<div class="row">
				<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Phonetic algorithm') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value">
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input" type="radio" name="soundex" value="Russell" <?= $controller->soundex === 'Russell' ? 'checked' : '' ?>>
						<?= I18N::translate('Russell') ?>
						</label>
					</div>
					<div class="form-check form-check-inline">
						<label class="form-check-label">
						<input class="form-check-input" type="radio" name="soundex" value="DaitchM" <?= $controller->soundex === 'DaitchM' || $controller->soundex === '' ? 'checked' : '' ?>>
						<?= I18N::translate('Daitch-Mokotoff') ?>
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Associates') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value">
					<div class="form-check">
						<label class="form-check-label">
						<input class="form-check-input" id="showasso" name="showasso" value="1" type="checkbox">
						<?= I18N::translate('Show related individuals/families') ?>
						</label>
					</div>
				</div>
			</div>

			<?php if (count(Tree::getAll()) > 1 && Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1'): ?>
			<fieldset class="form-group">
				<div class="row">
					<label class="col-sm-3 col-form-label wt-page-options-label">
						<?= I18N::translate('Family trees') ?>
					</label>
					<div class="col-sm-9 wt-page-options-value pt-2">
						<div class="d-flex justify-content-between">
							<div id="search-trees" class="form-check">
								<?php foreach (Tree::getAll() as $tree): ?>
								<div class="col px-0">
									<label class="form-check-label">
										<input class="form-check form-check-input" type="checkbox" <?= in_array($tree, $controller->search_trees) ? 'checked' : '' ?> value="yes" id="tree_<?= $tree->getTreeId() ?>" name="tree_<?= $tree->getTreeId() ?>">
										<?= e($tree->getTitle()) ?>
									</label>
								</div>
								<?php endforeach ?>
							</div>
							<?php if (count(Tree::getAll()) > 3): ?>
							<div class="d-row align-self-end mb-2">
								<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select all (of the family trees) */ I18N::translate('select all') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', true);});return false;">
								<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select none (of the family trees) */ I18N::translate('select none') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', false);});return false;">
								<?php if (count(Tree::getAll()) > 10): ?>
									<input type="button" value="<?= I18N::translate('invert selection') ?>" onclick="$('#search_trees :checkbox').each(function(){$(this).attr('checked', !$(this).attr('checked'));});return false;">
								<?php endif ?>
							</div>
							<?php endif ?>
						</div>
					</div>
				</div>
			</fieldset>
			<?php endif ?>
			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label"></label>
				<div class="col-sm-9 wt-page-options-value">
					<input type="submit" class="btn btn-primary" value="<?=  /* I18N: A button label. */ I18N::translate('search') ?>">
				</div>
			</div>
	</form>
<?php endif ?>
<?php $controller->printResults() ?>
</div>
