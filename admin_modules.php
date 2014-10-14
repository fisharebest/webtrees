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

define('WT_SCRIPT_NAME', 'admin_modules.php');
require 'includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Module administration'));

$modules = WT_Module::getInstalledModules('disabled');

$module_status = WT_DB::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

switch (WT_Filter::post('action')) {
case 'update_mods':
	if (WT_Filter::checkCsrf()) {
		foreach ($modules as $module_name=>$status) {
			$new_status=WT_Filter::post("status-{$module_name}", '[01]');
			if ($new_status!==null) {
				$new_status=$new_status ? 'enabled' : 'disabled';
				if ($new_status!=$status) {
					WT_DB::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($new_status, $module_name));
					$module_status[$module_name]=$new_status;
				}
			}
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin_modules.php');
	exit;
}

switch (WT_Filter::get('action')) {
case 'delete_module':
	$module_name=WT_Filter::get('module_name');
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
	unset($modules[$module_name]);
	unset($module_status[$module_name]);

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin_modules.php');
	exit;
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_JQUERY_DATATABLES_URL)
	->addInlineJavascript('
	  function reindexMods(id) {
			jQuery("#"+id+" input").each(
				function (index, value) {
					value.value = index+1;
				});
	  }

		jQuery("#installed_table").dataTable( {
			dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
			'.WT_I18N::datatablesI18N().',
			jQueryUI: true,
			autoWidth: false,
			sorting: [[ 1, "asc" ]],
			pageLength: 10,
			pagingType: "full_numbers",
			stateSave: true,
			stateDuration: 180,
			columns : [
				{ sortable: false, class: "center" },
				null,
				null,
				{ class: "center" },
				{ class: "center" },
				{ class: "center" },
				{ class: "center" },
				{ class: "center", visible: false }, // The WT_Module system does not yet include charts
				{ class: "center" },
				{ class: "center", visible: false } // The WT_Module system does not yet include themes
			]
		});
	');

?>
<div align="center">
	<div id="tabs">
		<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
			<input type="hidden" name="action" value="update_mods">
			<?php echo WT_Filter::getCsrf(); ?>
			<table id="installed_table" border="0" cellpadding="0" cellspacing="1">
				<thead>
					<tr>
					<th><?php echo WT_I18N::translate('Enabled'); ?></th>
					<th width="100px"><?php echo WT_I18N::translate('Module'); ?></th>
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
					foreach ($module_status as $module_name=>$status) {
						if (array_key_exists($module_name, $modules)) {
							$module=$modules[$module_name];
							echo
								'<tr><td>', two_state_checkbox('status-'.$module_name, $status=='enabled'), '</td>',
								'<td>', $module->getTitle(), '</td>',
								'<td>', $module->getDescription(), '</td>',
								'<td>', $module instanceof WT_Module_Menu    ? WT_I18N::translate('Menu') : '-', '</td>',
								'<td>', $module instanceof WT_Module_Tab     ? WT_I18N::translate('Tab') : '-', '</td>',
								'<td>', $module instanceof WT_Module_Sidebar ? WT_I18N::translate('Sidebar') : '-', '</td>',
								'<td>', $module instanceof WT_Module_Block   ? (($module->isUserBlock() ? '<div>'.WT_I18N::translate('My page').'</div>' : '').($module->isGedcomBlock() ? '<div>'.WT_I18N::translate('Home page').'</div>' : '')) : '-', '</td>',
								'<td>', $module instanceof WT_Module_Chart   ? WT_I18N::translate('Chart') : '-', '</td>',
								'<td>', $module instanceof WT_Module_Report  ? WT_I18N::translate('Report') : '-', '</td>',
								'<td>', $module instanceof WT_Module_Theme   ? WT_I18N::translate('Theme') : '-', '</td>',
								'</tr>';
						} else {
							// Module can’t be found on disk?
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
			<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
		</form>
	</div>
</div>
