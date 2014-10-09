<?php
// Change the preferences for a block on the index pages.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

use WT\Auth;

define('WT_SCRIPT_NAME', 'block_edit.php');
require './includes/session.php';

$block_id = WT_Filter::getInteger('block_id');
$block = WT_DB::prepare(
	"SELECT SQL_CACHE * FROM `##block` WHERE block_id=?"
)->execute(array($block_id))->fetchOneRow();

// Check access.  (1) the block must exist and be enabled, (2) gedcom blocks require
// managers, (3) user blocks require the user or an admin
if (
	!$block ||
	!array_key_exists($block->module_name, WT_Module::getActiveBlocks(WT_GED_ID)) ||
	$block->gedcom_id && !Auth::isManager(WT_Tree::get($block->gedcom_id)) ||
	$block->user_id && $block->user_id != Auth::id() && !Auth::isAdmin()
) {
	exit;
}

$class_name=$block->module_name.'_WT_Module';
$block=new $class_name;

$controller = new WT_Controller_Ajax();
$controller->pageHeader();

if (array_key_exists('ckeditor', WT_Module::getActiveModules())) {
	ckeditor_WT_Module::enableEditor($controller);
}

?>
<form name="block" method="post" action="block_edit.php?block_id=<?php echo $block_id; ?>" onsubmit="return modalDialogSubmitAjax(this);" >
	<input type="hidden" name="save" value="1">
	<?php echo WT_Filter::getCsrf(); ?>
	<p>
		<?php echo $block->getDescription(); ?>
	</p>
	<table class="facts_table">
		<?php echo $block->configureBlock($block_id); ?>
		<tr>
			<td colspan="2" class="topbottombar">
				<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
			</td>
		</tr>
	</table>
</form>
