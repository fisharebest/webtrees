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

$modules       = WT_Module::getInstalledModules('disabled');
$module_status = WT_DB::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

if (WT_Filter::post('action') === 'update_mods' && WT_Filter::checkCsrf()) {
	foreach ($modules as $module) {
		$new_status = WT_Filter::post('status-' . $module->getName(), '[01]');
		if ($new_status !== null) {
			$new_status = $new_status ? 'enabled' : 'disabled';
			$old_status = $module_status[$module->getName()];
			if ($new_status !== $old_status) {
				WT_DB::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($new_status, $module->getName()));
				if ($new_status === 'disabled') {
					WT_FlashMessages::addMessage(WT_I18N::translate('The module “%s” has been disabled.', $module->getTitle()), 'success');
				} else {
					WT_FlashMessages::addMessage(WT_I18N::translate('The module “%s” has been enabled.', $module->getTitle()), 'success');
				}
			}
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin_modules.php');

	return;
}

if (WT_Filter::post('action') === 'delete' && WT_Filter::checkCsrf()) {
	$module_name = WT_Filter::post('module_name');
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

	WT_FlashMessages::addMessage(WT_I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . 'admin_modules.php');
	exit;
}

// Module can’t be found on disk?
// Don't delete it automatically.  It may be temporarily missing, after a re-installation, etc.
foreach ($module_status as $module_name => $status) {
	if (!array_key_exists($module_name, $modules)) {
		$html =
			WT_I18N::translate('Preferences exist for the module “%s”, but this module no longer exists.', '<span dir="ltr">' . $module_name . '</span>') .
			'<form method="post" class="form-inline">' .
			WT_Filter::getCsrf() .
			'<input type="hidden" name="action" value="delete">' .
			'<input type="hidden" name="module_name" value="' . $module_name . '">' .
			'<button type="submit" class="btn btn-link">' . WT_I18N::translate('Delete the preferences for this module.') . '</button>' .
			'</form>';
		WT_FlashMessages::addMessage($html, 'warning');
	}
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
			paging: false,
			'.WT_I18N::datatablesI18N().',
			sorting: [[ 1, "asc" ]],
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

<h2><?php echo $controller->getPageTitle(); ?></h2>

<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
	<input type="hidden" name="action" value="update_mods">
	<?php echo WT_Filter::getCsrf(); ?>
	<table class="table table-bordered table-hover table-condensed table-module-administration">
		<caption class="sr-only">
			<?php echo WT_I18N::translate('Module administration'); ?>
		</caption>
		<thead>
		<tr>
			<th><?php echo WT_I18N::translate('Enabled'); ?></th>
			<th><?php echo WT_I18N::translate('Module'); ?></th>
			<th><?php echo WT_I18N::translate('Description'); ?></th>
			<th class="hidden-xs"><?php echo WT_I18N::translate('Menu'); ?></th>
			<th class="hidden-xs"><?php echo WT_I18N::translate('Tab'); ?></th>
			<th class="hidden-xs"><?php echo WT_I18N::translate('Sidebar'); ?></th>
			<th class="hidden-xs"><?php echo WT_I18N::translate('Block'); ?></th>
			<th class="hidden"><?php echo WT_I18N::translate('Chart'); ?></th>
			<th class="hidden-xs"><?php echo WT_I18N::translate('Report'); ?></th>
			<th class="hidden"><?php echo WT_I18N::translate('Theme'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($module_status as $module_name => $status) {
			if (array_key_exists($module_name, $modules)) {
				$module = $modules[$module_name];
				echo
					'<tr><td class="text-center">', two_state_checkbox('status-' . $module_name, $status === 'enabled'), '</td>',
					'<td>';
				if ($module instanceof WT_Module_Config) {
					echo '<a href="', $module->getConfigLink() ,'">';
				}
				echo $module->getTitle();
				if ($module instanceof WT_Module_Config) {
					echo ' <i class="fa fa-cogs"></i></a>';
				}
				echo
					'</td>',
					'<td>', $module->getDescription(), '</td>',
					'<td class="hidden-xs">', $module instanceof WT_Module_Menu    ? WT_I18N::translate('Menu') : '-', '</td>',
					'<td class="hidden-xs">', $module instanceof WT_Module_Tab     ? WT_I18N::translate('Tab') : '-', '</td>',
					'<td class="hidden-xs">', $module instanceof WT_Module_Sidebar ? WT_I18N::translate('Sidebar') : '-', '</td>',
					'<td class="hidden-xs">', $module instanceof WT_Module_Block   ? (($module->isUserBlock() ? '<div>'.WT_I18N::translate('My page').'</div>' : '').($module->isGedcomBlock() ? '<div>'.WT_I18N::translate('Home page').'</div>' : '')) : '-', '</td>',
					'<td class="hidden">', $module instanceof WT_Module_Chart   ? WT_I18N::translate('Chart') : '-', '</td>',
					'<td class="hidden-xs">', $module instanceof WT_Module_Report  ? WT_I18N::translate('Report') : '-', '</td>',
					'<td class="hidden">', $module instanceof WT_Module_Theme   ? WT_I18N::translate('Theme') : '-', '</td>',
					'</tr>';
			}
		}
		?>
		</tbody>
	</table>
	<input class="btn btn-primary" type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
</form>
