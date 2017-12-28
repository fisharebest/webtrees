<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\BranchesController;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new BranchesController;
$controller->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-branches d-print-none">
	<input type="hidden" name="ged" id="ged" value="<?= $WT_TREE->getNameHtml() ?>">
	<div class="form-group row">
		<label class="col-form-label col-sm-3 wt-page-options-label" for="surname">
			<?= I18N::translate('Surname') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control" data-autocomplete-type="SURN" type="text" name="surname" id="surname" value="<?= Html::escape($controller->getSurname()) ?>" dir="auto">
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Phonetic search') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::checkbox(I18N::translate('Russell'), true, ['name' => 'soundex_std', 'checked' => $controller->getSoundexStd()]) ?>
				<?= Bootstrap4::checkbox(I18N::translate('Daitch-Mokotoff'), true, ['name' => 'soundex_dm', 'checked' => $controller->getSoundexDm()]) ?>
			</div>
		</div>
	</fieldset>

	<div class="form-group row">
		<div class="col-sm-3 wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button type="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('view') ?>
			</button>
		</div>
	</div>
</form>

<ol>
	<?= $controller->getPatriarchsHtml() ?>
</ol>
