<?php
// Module Administration User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin_module_menus.php');
require 'includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Menus'));

$modules = WT_Module::getActiveMenus(WT_GED_ID, WT_PRIV_HIDE);
$action  = WT_Filter::post('action');

if ($action === 'update_mods' && WT_Filter::checkCsrf()) {
	foreach ($modules as $module) {
		foreach (WT_Tree::getAll() as $tree) {
			$access_level = WT_Filter::post('access-' . $module->getName() . '-' . $tree->id(), WT_REGEX_INTEGER, $module->defaultAccessLevel());
			WT_DB::prepare(
				"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
			)->execute(array($module->getName(), $tree->id(), $access_level));
		}
		$order = WT_Filter::post('order-' . $module->getName());
		WT_DB::prepare(
			"UPDATE `##module` SET menu_order = ? WHERE module_name = ?"
		)->execute(array($order, $module->getName()));
	}

	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

$controller
	->addInlineJavascript('
		jQuery("#module_table").sortable({
			items: ".sortme",
			forceHelperSize: true,
			forcePlaceholderSize: true,
			opacity: 0.7,
			cursor: "move",
			axis: "y",
			update: function(event, ui) {
				jQuery("input", jQuery(this)).each(
					function (index, element) {
						element.value = index + 1;
					}
				);
			}
		});
	')
	->pageHeader();

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
	<li><a href="admin_modules.php"><?php echo WT_I18N::translate('Module administration'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form method="post">
	<input type="hidden" name="action" value="update_mods">
	<?php echo WT_Filter::getCsrf(); ?>
	<table id="module_table" class="table table-bordered">
		<thead>
		<tr>
			<th class="col-xs-1"><?php echo WT_I18N::translate('Menu'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Description'); ?></th>
			<th class="col-xs-1"><?php echo WT_I18N::translate('Order'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Access level'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php $order = 0; ?>
		<?php foreach ($modules as $module): ?>
			<?php $order++; ?>
			<tr class="sortme">
				<td class="col-xs-1">
					<?php if ($module instanceof WT_Module_Config): ?>
					<a href="<?php echo $module->getConfigLink(); ?>"><?php echo $module->getTitle(); ?> <i class="fa fa-cogs"></i></a>
					<?php else: ?>
					<?php echo $module->getTitle(); ?>
					<?php endif; ?>
				</td>
				<td class="col-xs-5"><?php echo $module->getDescription(); ?></td>
				<td class="col-xs-1"><input type="text" size="3" value="<?php echo $order; ?>" name="order-<?php echo $module->getName(); ?>"></td>
				<td class="col-xs-5">
					<table class="table">
						<tbody>
							<?php foreach (WT_Tree::getAll() as $tree): ?>
								<tr>
									<td>
										<?php echo $tree->titleHtml(); ?>
									</td>
									<td>
										<?php echo edit_field_access_level('access-' . $module->getName() . '-' . $tree->id(), $module->getAccessLevel($tree, 'menu')); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<button class="btn btn-primary" type="submit">
		<i class="fa fa-check"></i>
		<?php echo WT_I18N::translate('save'); ?>
	</button>
</form>
