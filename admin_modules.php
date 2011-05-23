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
$all_modules=WT_DB::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

foreach ($installed_modules as $module_name=>$module) {
	if (!array_key_exists($module_name, $all_modules)) {
		WT_DB::prepare("INSERT INTO `##module` (module_name, status) VALUES (?, 'disabled')")->execute(array($module_name));
		$all_modules[$module_name]='disabled';
	}
}

switch (safe_POST('action')) {
case 'update_mods':
	foreach ($all_modules as $module_name=>$status) {
		$new_status=safe_POST("status-{$module_name}");
		if ($new_status!==null) {
			$new_status=$new_status ? 'enabled' : 'disabled';
			if ($new_status!=$status) {
				WT_DB::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($new_status, $module_name));
				$all_modules[$module_name]=$new_status;
			}
		}
	}
	break;
}

switch (safe_GET('action')) {
case 'delete_module':
	$module_name=safe_GET('module_name');
	WT_DB::prepare(
		"DELETE `##block_setting`".
		" FROM `##block_setting`".
		" JOIN `##block` USING (block_id)".
		" JOIN `##module` USING (module_name)".
		" WHERE module_name=?"
	)->execute(array($module_name));
	WT_DB::prepare(
		"DELETE `##block`".
		" FROM `##block`".
		" JOIN `##module` USING (module_name)".
		" WHERE module_name=?"
	)->execute(array($module_name));
	WT_DB::prepare("DELETE FROM `##module_setting` WHERE module_name=?")->execute(array($module_name));
	WT_DB::prepare("DELETE FROM `##module_privacy` WHERE module_name=?")->execute(array($module_name));
	WT_DB::prepare("DELETE FROM `##module`         WHERE module_name=?")->execute(array($module_name));
	unset($all_modules[$module_name]);
	break;
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
			"sLengthMenu": '<?php echo /* I18N: %s is a placeholder for listbox containing numeric options */ WT_I18N::translate('Display %s', '<select><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="-1">'.WT_I18N::translate('All').'</option></select>'); ?>',
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
		"aoColumns" : [
			{ bSortable: false, sClass: "center" },
			null,
			null,
			{ sClass: "center" },
			{ sClass: "center" },
			{ sClass: "center" },
			{ sClass: "center" },
			{ sClass: "center" },
			{ sClass: "center" },
			{ sClass: "center" }
		]
	});
});
//]]>
</script>

<div align="center">
	<div id="tabs">
	<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
			<input type="hidden" name="action" value="update_mods" />
			<table id="installed_table" border="0" cellpadding="0" cellspacing="1">
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
					foreach ($all_modules as $module_name=>$status) {
						if (array_key_exists($module_name, $installed_modules)) {
							$module=$installed_modules[$module_name];
							echo
								'<tr><td>', two_state_checkbox('status-'.$module->getName(), $status=='enabled'), '</td>',
								'<td>', $module->getTitle(), '</td>',
								'<td>', $module->getDescription(), '</td>',
								'<td>', $module instanceof WT_Module_Menu    ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Tab     ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Sidebar ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Block   ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Chart   ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Report  ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'<td>', $module instanceof WT_Module_Theme   ? WT_I18N::translate('yes') : WT_I18N::translate('no'), '</td>',
								'</tr>';
						} else {
							// Module can't be found on disk?
							// Don't delete it automatically.  It may be temporarily missing, after a re-installation, etc.
							echo
								'<tr class="error"><td>&nbsp;</td><td>', $module_name, '</td><td>',
								'<a href="'.WT_SCRIPT_NAME.'?action=delete_module&amp;module_name='.$module_name.'">',
								WT_I18N::translate('This module cannot be found.  Delete its configuration settings.'),
								'</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
						}
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
