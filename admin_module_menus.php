<?php
// Module Administration User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// @version $Id$

define('WT_SCRIPT_NAME', 'admin_module_menus.php');

require 'includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (!WT_USER_GEDCOM_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

// Modules may have been added or updated to no longer provide a particular component
$installed_modules=WT_Module::getInstalledModules();
foreach ($installed_modules as $module_name=>$module) {
	// New module
	WT_DB::prepare("INSERT IGNORE INTO `##module` (module_name) VALUES (?)")->execute(array($module_name));

	// Removed component
	if (!$module instanceof WT_Module_Block) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='block'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##block` WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Chart) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='chart'"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Menu) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='menu'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET menu_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Report) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='report'"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Sidebar) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='sidebar'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET sidebar_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Tab) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='tab'"
		)->execute(array($module_name));
		WT_DB::prepare(
			"UPDATE `##module` SET tab_order=NULL WHERE module_name=?"
		)->execute(array($module_name));
	}
	if (!$module instanceof WT_Module_Theme) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=? AND component='theme'"
		)->execute(array($module_name));
	}
}

// Delete config for modules that no longer exist
$module_names=WT_DB::prepare("SELECT module_name FROM `##module`")->fetchOneColumn();
foreach ($module_names as $module_name) {
	if (!array_key_exists($module_name, $installed_modules)) {
		WT_DB::prepare(
			"DELETE FROM `##module_privacy` WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##block` WHERE module_name=?"
		)->execute(array($module_name));
		WT_DB::prepare(
			"DELETE FROM `##module` WHERE module_name=?"
		)->execute(array($module_name));
	}
}

$action = safe_POST('action');

if ($action=='update_mods') {
	foreach (WT_Module::getInstalledModules() as $module) {
		$module_name=$module->getName();
		foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
			if ($module instanceof WT_Module_Menu) {
				$value = safe_POST("menuaccess-{$module_name}-{$ged_id}", WT_REGEX_INTEGER, $module->defaultAccessLevel());
				WT_DB::prepare(
					"REPLACE INTO `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
				)->execute(array($module_name, $ged_id, $value));
				$value = safe_POST('menuorder-'.$module_name);
				WT_DB::prepare(
					"UPDATE `##module` SET menu_order=? WHERE module_name=?"
				)->execute(array($value, $module_name));
			}
		}
	}
}

print_header(WT_I18N::translate('Module administration'));

echo WT_JS_START; ?>

  jQuery(document).ready(function() {
    jQuery("#menus_table").sortable({items: '.sortme', forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: 'move', axis: 'y'});

    //-- update the order numbers after drag-n-drop sorting is complete
    jQuery('#menus_table').bind('sortupdate', function(event, ui) {
			jQuery('#'+jQuery(this).attr('id')+' input').each(
				function (index, value) {
					value.value = index+1;
				}
			);
		});
	});

<?php echo WT_JS_END; ?>

<div align="center">
	<div id="tabs">
		<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
			<input type="hidden" name="action" value="update_mods" />
			<table id="menus_table" class="modules_table">
				<thead>
					<tr>
					<th><?php echo WT_I18N::translate('Menu'); ?></th>
					<th><?php echo WT_I18N::translate('Order'); ?></th>
					<th><?php echo WT_I18N::translate('Access level'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$order = 1;
					foreach (WT_Module::getInstalledMenus() as $module) { ?>
					<tr class="sortme">
						<td><?php echo $module->getTitle(); ?></td>
						<td><input type="text" size="5" value="<?php echo $order; ?>" name="menuorder-<?php echo $module->getName(); ?>" />
						</td>
						<td>
							<table class="modules_table2">
								<?php
									foreach (get_all_gedcoms() as $ged_id=>$ged_name) {
										$varname = 'menuaccess-'.$module->getName().'-'.$ged_id;
										$access_level=WT_DB::prepare(
											"SELECT access_level FROM `##module_privacy` WHERE gedcom_id=? AND module_name=? AND component='menu'"
										)->execute(array($ged_id, $module->getName()))->fetchOne();
										if ($access_level===null) {
											$access_level=$module->defaultAccessLevel();
										}
										echo '<tr><td>', htmlspecialchars($ged_name), '</td><td>';
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
			<input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>" />
		</form>
	</div>
</div>
<?php
print_footer();
