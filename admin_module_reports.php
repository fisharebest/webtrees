<?php
// Module Administration User Interface.
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

define('WT_SCRIPT_NAME', 'admin_module_reports.php');
require 'includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Module administration') . ' — ' . WT_I18N::translate('Reports'));

$modules = WT_Module::getActiveReports(WT_GED_ID, WT_PRIV_HIDE);
$action  = WT_Filter::post('action');

if ($action === 'update_mods' && WT_Filter::checkCsrf()) {
	foreach ($modules as $module) {
		foreach (WT_Tree::getAll() as $tree) {
			$access_level = WT_Filter::post("access-{$module_name}-{$tree->tree_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
			WT_DB::prepare(
				"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'report', ?)"
			)->execute(array($module->getName(), $tree->tree_id, $access_level));
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;
}

$controller
	->pageHeader();

?>
<h2><?php echo $controller->getPageTitle(); ?></h2>

<form method="post">
	<input type="hidden" name="action" value="update_mods">
	<?php echo WT_Filter::getCsrf(); ?>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="col-xs-2"><?php echo WT_I18N::translate('Report'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Description'); ?></th>
			<th class="col-xs-5"><?php echo WT_I18N::translate('Access level'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$order = 1;
		foreach ($modules as $module_name=>$module) {
			?>
			<tr>
				<td class="col-xs-2"><?php echo $module->getTitle(); ?></td>
				<td class="col-xs-5"><?php echo $module->getDescription(); ?></td>
				<td class="col-xs-5">
					<table class="table">
						<?php
						foreach (WT_Tree::getAll() as $tree) {
							$varname = 'access-' . $module->getName() . '-' . $tree->tree_id;
							$access_level = WT_DB::prepare(
								"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='report'"
							)->execute(array($tree->tree_id, $module->getName()))->fetchOne();
							if ($access_level === null) {
								$access_level = $module->defaultAccessLevel();
							}
							echo '<tr><td>', $tree->tree_title_html, '</td><td>';
							echo edit_field_access_level($varname, $access_level);
						}
						?>
					</table>
				</td>
			</tr>
			<?php
			$order++;
		}
		?>
		</tbody>
	</table>
	<input class="btn btn-primary" type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
</form>
