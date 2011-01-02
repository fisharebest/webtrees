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

define('WT_SCRIPT_NAME', 'admin_modules.php');

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
		$status=safe_POST("status-{$module_name}");
		if ($status!==null) {
			WT_DB::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($status ? 'enabled' : 'disabled', $module_name));
		}
	}
}

print_header(WT_I18N::translate('Module administration'));
?>
<style type="text/css">
<!--
.sortme {
	cursor: move;
}
.sortme img {
	cursor: pointer;
}
//-->
</style>
<script type="text/javascript">
//<![CDATA[

  function reindexMods(id) {
		jQuery('#'+id+' input').each(
			function (index, value) {
				value.value = index+1;
			});
  }

  jQuery(document).ready(function() {
  
  		var oTable = jQuery('#installed_table').dataTable( {
			"oLanguage": {
				"sLengthMenu": 'Display <select><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="-1">All</option></select> records'
			},
			"bJQueryUI": true,
			"bAutoWidth":false,
			"aaSorting": [[ 1, "asc" ]],
			"iDisplayLength": 15,
			"sPaginationType": "full_numbers",
			"aoColumnDefs": [
				{ "bSortable": false, "aTargets": [ 0 ] }
			]
		});
});
//]]>
</script>

<div align="center">
	<div id="tabs">
	<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
			<input type="hidden" name="action" value="update_mods" />
			<table id="installed_table" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
				<thead>
					<tr>
					<th><?php echo WT_I18N::translate('Enabled'); ?></th>
					<th width="100px"><?php echo WT_I18N::translate('Module Name'); ?></th>
					<th><?php echo WT_I18N::translate('Description'); ?></th>
					<th><?php echo WT_I18N::translate('Menu'); ?></th>
					<th><?php echo WT_I18N::translate('Tab'); ?></th>
					<th><?php echo WT_I18N::translate('Sidebar'); ?></th>
					<th><?php echo WT_I18N::translate('Block'); ?></th>
					<th><?php echo WT_I18N::translate('Chart'); ?></th>
					<th><?php echo WT_I18N::translate('Report'); ?></th>
					<th><?php echo WT_I18N::translate('Theme'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach (WT_Module::getInstalledModules() as $module) {
						$status=WT_DB::prepare(
							"SELECT status FROM `##module` WHERE module_name=?"
						)->execute(array($module->getName()))->fetchOne();
						echo '<tr><td>', two_state_checkbox('status-'.$module->getName(), $status=='enabled'), '</td>';
						?>
						<td><?php echo $module->getTitle(); ?></td>
						<td><?php echo $module->getDescription(); ?></td>
						<td><?php if ($module instanceof WT_Module_Menu) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Tab) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Sidebar) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Block) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Chart) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Report) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						<td><?php if ($module instanceof WT_Module_Theme) echo WT_I18N::translate('Yes'); else echo WT_I18N::translate('No'); ?></td>
						</tr>
					<?php
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
