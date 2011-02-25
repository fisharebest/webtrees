<?php
// Module Administration User Interface.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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

// Only admin users can access this page
if (!WT_USER_IS_ADMIN) {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

// New modules may have been added...
$installed_modules=WT_Module::getInstalledModules();
foreach ($installed_modules as $module_name=>$module) {
	// New module
	WT_DB::prepare("INSERT IGNORE INTO `##module` (module_name) VALUES (?)")->execute(array($module_name));
}

// Disable modules that no longer exist.  Don't delete the config.  The module
// may have only been removed temporarily, e.g. during an upgrade / migration
$module_names=WT_DB::prepare("SELECT module_name FROM `##module` WHERE status='enabled'")->fetchOneColumn();
foreach ($module_names as $module_name) {
	if (!array_key_exists($module_name, $installed_modules)) {
		WT_DB::prepare(
			"UPDATE `##module` SET status='disabled' WHERE module_name=?"
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
			"sLengthMenu": '<?php echo /* I18N: %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
			"sZeroRecords": '<?php echo WT_I18N::translate('No records to display');?>',
			"sInfo": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'); ?>',
			"sInfoEmpty": '<?php echo /* I18N: %s are placeholders for numbers */ WT_I18N::translate('Showing %1$s to %2$s of %3$s', '0', '0', '0'); ?>',
			"sInfoFiltered": '<?php echo /* I18N: %s is a placeholder for a number */ WT_I18N::translate('(filtered from %s total entries)', '_MAX_'); ?>',
			"sSearch": '<?php echo WT_I18N::translate('Search');?>',
			"oPaginate": {
				"sFirst": '<?php echo /* I18N: button label, first page    */ WT_I18N::translate('first'); ?>',
				"sLast": '<?php echo /* I18N: button label, last page     */ WT_I18N::translate('last'); ?>',
				"sNext": '<?php echo /* I18N: button label, next page     */ WT_I18N::translate('next'); ?>',
				"sPrevious": '<?php echo /* I18N: button label, previous page */ WT_I18N::translate('previous'); ?>'
			}
		},
		"sDom": '<"H"prf>t<"F"li>',
		"bJQueryUI": true,
		"bAutoWidth":false,
		"aaSorting": [[ 1, "asc" ]],
		"iDisplayLength": 10,
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
						<td class="<?php echo $TEXT_DIRECTION; ?>" ><?php echo $module->getDescription(); ?></td>
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
