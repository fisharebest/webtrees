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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Module\CkeditorModule;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$block_id = Filter::getInteger('block_id');
$block    = Database::prepare(
	"SELECT SQL_CACHE * FROM `##block` WHERE block_id=?"
)->execute([$block_id])->fetchOneRow();

// Check access. (1) the block must exist and be enabled, (2) gedcom blocks require
// managers, (3) user blocks require the user or an admin
$blocks = Module::getActiveBlocks($WT_TREE);
if (
	!$block ||
	!array_key_exists($block->module_name, $blocks) ||
	$block->gedcom_id && !Auth::isManager(Tree::findById($block->gedcom_id)) ||
	$block->user_id && $block->user_id != Auth::id() && !Auth::isAdmin()
) {
	header('Location: ' . WT_BASE_URL);

	return;
}

$block = $blocks[$block->module_name];

if (Filter::post('save')) {
	$ctype = Filter::post('ctype', 'user', 'gedcom');
	header('Location: ' . WT_BASE_URL . 'index.php?ctype=' . $ctype . '&ged=' . $WT_TREE->getNameUrl());
	$block->configureBlock($block_id);

	return;
}

$ctype = Filter::get('ctype', 'user', 'gedcom');

$controller = new PageController;
$controller
	->setPageTitle($block->getTitle() . ' — ' . I18N::translate('Preferences'))
	->pageHeader();

if (Module::getModuleByName('ckeditor')) {
	CkeditorModule::enableEditor($controller);
}

?>
<h2><?= $controller->getPageTitle() ?></h2>
<p><?= $block->getDescription() ?></p>

<form name="block" method="post" action="?block_id=<?= $block_id ?>">
	<input type="hidden" name="save" value="1">
	<input type="hidden" name="ged" value="<?= $WT_TREE->getNameHtml() ?>">
	<input type="hidden" name="ctype" value="<?= $ctype ?>">
	<?= Filter::getCsrf() ?>
	<?= $block->configureBlock($block_id) ?>
	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>
			<a class="btn btn-link" href="index.php?ctype=<?= $ctype ?>&amp;ged=<?= $WT_TREE->getNameHtml() ?>"><?= I18N::translate('cancel') ?></a>
		</div>
	</div>
</form>
