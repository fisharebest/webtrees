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

define('WT_SCRIPT_NAME', 'admin_module_tabs.php');
require 'includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Module administration') . ' — ' . WT_I18N::translate('Tabs'))
	->pageHeader()
	->addInlineJavascript('
	jQuery("#tabs_table").sortable({items: ".sortme", forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});

	//-- update the order numbers after drag-n-drop sorting is complete
	jQuery("#tabs_table").bind("sortupdate", function(event, ui) {
			jQuery("#"+jQuery(this).attr("id")+" input").each(
				function (index, value) {
					value.value = index+1;
				}
			);
		});
	');

$modules=WT_Module::getActiveTabs(WT_GED_ID, WT_PRIV_HIDE);

$action = WT_Filter::post('action');

if ($action=='update_mods' && WT_Filter::checkCsrf()) {
	foreach ($modules as $module_name=>$module) {
		foreach (WT_Tree::getAll() as $tree) {
			$access_level = WT_Filter::post("tabaccess-{$module_name}-{$tree->tree_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
			WT_DB::prepare(
				"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
			)->execute(array($module_name, $tree->tree_id, $access_level));
		}
		$order = WT_Filter::post('taborder-'.$module_name);
		WT_DB::prepare(
			"UPDATE `##module` SET tab_order=? WHERE module_name=?"
		)->execute(array($order, $module_name));
		$module->order=$order; // Make the new order take effect immediately
	}
	uasort($modules, function(WT_Module $x, WT_Module $y) { return $x->order - $y->order; });
}

?>
<h2><?php echo $controller->getPageTitle(); ?></h2>

<div id="tabs" align="center">
	<form method="post">
		<input type="hidden" name="action" value="update_mods">
		<?php echo WT_Filter::getCsrf(); ?>
		<table id="tabs_table" class="modules_table">
			<thead>
				<tr>
					<th><?php echo WT_I18N::translate('Tab'); ?></th>
					<th><?php echo WT_I18N::translate('Description'); ?></th>
					<th><?php echo WT_I18N::translate('Order'); ?></th>
					<th><?php echo WT_I18N::translate('Access level'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$order = 1;
				foreach ($modules as $module_name=>$module) {
					?>
					<tr class="sortme">
					<td><?php echo $module->getTitle(); ?></td>
					<td><?php echo $module->getDescription(); ?></td>
					<td><input type="text" size="3" value="<?php echo $order; ?>" name="taborder-<?php echo $module->getName(); ?>">
					</td>
					<td>
					<table class="modules_table2">
						<?php
						foreach (WT_Tree::getAll() as $tree) {
							$varname = 'tabaccess-'.$module_name.'-'.$tree->tree_id;
							$access_level=WT_DB::prepare(
								"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='tab'"
							)->execute(array($tree->tree_id, $module_name))->fetchOne();
							if ($access_level===null) {
								$access_level=$module->defaultAccessLevel();
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
		<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
	</form>
</div>
