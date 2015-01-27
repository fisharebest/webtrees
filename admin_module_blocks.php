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

define('WT_SCRIPT_NAME', 'admin_module_blocks.php');
require 'includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Blocks'));

$modules = WT_Module::getActiveBlocks(WT_GED_ID, WT_PRIV_HIDE);
$action  = WT_Filter::post('action');

if ($action === 'update_mods' && WT_Filter::checkCsrf()) {
	foreach ($modules as $module) {
		foreach (WT_Tree::getAll() as $tree) {
			$access_level = WT_Filter::post('access-' . $module->getName() . '-' . $tree->tree_id, WT_REGEX_INTEGER, $module->defaultAccessLevel());
			WT_DB::prepare(
				"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'block', ?)"
			)->execute(array($module->getName(), $tree->tree_id, $access_level));
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;
}

$controller
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
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="col-xs-2"><?php echo WT_I18N::translate('Block'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Description'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Access level'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($modules as $module_name => $module): ?>
			<tr>
				<td class="col-xs-2">
					<?php if ($module instanceof WT_Module_Config): ?>
						<a href="<?php echo $module->getConfigLink(); ?>"><?php echo $module->getTitle(); ?> <i class="fa fa-cogs"></i></a>
					<?php else: ?>
						<?php echo $module->getTitle(); ?>
					<?php endif; ?>
				</td>
				<td class="col-xs-5"><?php echo $module->getDescription(); ?></td>
				<td class="col-xs-5">
					<table class="table">
						<tbody>
							<?php foreach (WT_Tree::getAll() as $tree): ?>
								<tr>
									<td>
										<?php echo $tree->tree_title_html; ?>
									</td>
									<td>
										<?php echo edit_field_access_level('access-' . $module->getName() . '-' . $tree->tree_id, $module->getAccessLevel($tree, 'block')); ?>
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
