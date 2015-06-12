<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;

define('WT_SCRIPT_NAME', 'admin_modules.php');
require 'includes/session.php';

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(I18N::translate('Module administration'));

$modules       = Module::getInstalledModules('disabled');
$module_status = Database::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

if (Filter::post('action') === 'update_mods' && Filter::checkCsrf()) {
	foreach ($modules as $module) {
		$new_status = Filter::post('status-' . $module->getName(), '[01]');
		if ($new_status !== null) {
			$new_status = $new_status ? 'enabled' : 'disabled';
			$old_status = $module_status[$module->getName()];
			if ($new_status !== $old_status) {
				Database::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute(array($new_status, $module->getName()));
				if ($new_status === 'disabled') {
					FlashMessages::addMessage(I18N::translate('The module “%s” has been disabled.', $module->getTitle()), 'success');
				} else {
					FlashMessages::addMessage(I18N::translate('The module “%s” has been enabled.', $module->getTitle()), 'success');
				}
			}
		}
	}

	header('Location: ' . WT_BASE_URL . 'admin_modules.php');

	return;
}

if (Filter::post('action') === 'delete' && Filter::checkCsrf()) {
	$module_name = Filter::post('module_name');
	Database::prepare(
		"DELETE `##block_setting`" .
		" FROM `##block_setting`" .
		" JOIN `##block` USING (block_id)" .
		" JOIN `##module` USING (module_name)" .
		" WHERE module_name=?"
	)->execute(array($module_name));
	Database::prepare(
		"DELETE `##block`" .
		" FROM `##block`" .
		" JOIN `##module` USING (module_name)" .
		" WHERE module_name=?"
	)->execute(array($module_name));
	Database::prepare("DELETE FROM `##module_setting` WHERE module_name=?")->execute(array($module_name));
	Database::prepare("DELETE FROM `##module_privacy` WHERE module_name=?")->execute(array($module_name));
	Database::prepare("DELETE FROM `##module`         WHERE module_name=?")->execute(array($module_name));

	FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

	header('Location: ' . WT_BASE_URL . 'admin_modules.php');

	return;
}

// The module can’t be found on disk?
// Don't delete it automatically.  It may be temporarily missing, after a re-installation, etc.
foreach ($module_status as $module_name => $status) {
	if (!array_key_exists($module_name, $modules)) {
		$html =
			I18N::translate('Preferences exist for the module “%s”, but this module no longer exists.', '<span dir="ltr">' . $module_name . '</span>') .
			'<form method="post" class="form-inline">' .
			Filter::getCsrf() .
			'<input type="hidden" name="action" value="delete">' .
			'<input type="hidden" name="module_name" value="' . $module_name . '">' .
			'<button type="submit" class="btn btn-link">' . I18N::translate('Delete the preferences for this module.') . '</button>' .
			'</form>';
		FlashMessages::addMessage($html, 'warning');
	}
}

$controller
	->pageHeader()
	->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
	->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
	->addInlineJavascript('
	  function reindexMods(id) {
			jQuery("#" + id + " input").each(
				function (index, value) {
					value.value = index+1;
				});
	  }
		jQuery("#installed_table").dataTable( {
			paging: false,
			' . I18N::datatablesI18N() . ',
			sorting: [[ 1, "asc" ]],
			columns : [
				{ sortable: false, class: "center" },
				null,
				null,
				{ class: "center" },
				{ class: "center" },
				{ class: "center" },
				{ class: "center" },
				{ class: "center", visible: false }, // The Module system does not yet include charts
				{ class: "center" },
				{ class: "center", visible: false } // The Module system does not yet include themes
			]
		});
	');

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<form method="post" action="<?php echo WT_SCRIPT_NAME; ?>">
	<input type="hidden" name="action" value="update_mods">
	<?php echo Filter::getCsrf(); ?>
	<table class="table table-bordered table-hover table-condensed table-module-administration">
		<caption class="sr-only">
			<?php echo I18N::translate('Module administration'); ?>
		</caption>
		<thead>
		<tr>
			<th><?php echo I18N::translate('Enabled'); ?></th>
			<th><?php echo I18N::translate('Module'); ?></th>
			<th><?php echo I18N::translate('Description'); ?></th>
			<th class="hidden-xs"><a href="admin_module_menus.php"><?php echo I18N::translate('Menus'); ?></a></th>
			<th class="hidden-xs"><a href="admin_module_tabs.php"><?php echo I18N::translate('Tabs'); ?></a></th>
			<th class="hidden-xs"><a href="admin_module_sidebar.php"><?php echo I18N::translate('Sidebars'); ?></a></th>
			<th class="hidden-xs"><a href="admin_module_blocks.php"><?php echo I18N::translate('Blocks'); ?></a></th>
			<th class="hidden"><?php echo I18N::translate('Charts'); ?></th>
			<th class="hidden-xs"><a href="admin_module_reports.php"><?php echo I18N::translate('Reports'); ?></a></th>
			<th class="hidden"><?php echo I18N::translate('Themes'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($modules as $module_name => $module) {
			$status = $module_status[$module_name];
			echo
			'<tr><td class="text-center">', FunctionsEdit::twoStateCheckbox('status-' . $module->getName(), $status === 'enabled'), '</td>',
			'<td>';
			if ($module instanceof ModuleConfigInterface) {
				echo '<a href="', $module->getConfigLink(), '">';
			}
			echo $module->getTitle();
			if ($module instanceof ModuleConfigInterface) {
				echo ' <i class="fa fa-cogs"></i></a>';
			}
			echo
			'</td>',
			'<td>', $module->getDescription(), '</td>',
			'<td class="text-center text-muted hidden-xs">', $module instanceof ModuleMenuInterface ? '<i class="fa fa-list-ul" title="' . I18N::translate('Menu') . '"></i>' : '-', '</td>',
			'<td class="text-center text-muted hidden-xs">', $module instanceof ModuleTabInterface ? '<i class="fa fa-folder" title="' . I18N::translate('Tab') . '"></i>' : '-', '</td>',
			'<td class="text-center text-muted hidden-xs">', $module instanceof ModuleSidebarInterface ? '<i class="fa fa-th-large" title="' . I18N::translate('Sidebar') . '"></i>' : '-', '</td>',
			'<td class="text-center text-muted hidden-xs">', $module instanceof ModuleBlockInterface ? (($module->isUserBlock() ? '<i class="fa fa-user" title="' . I18N::translate('My page') . '"></i>' : '') . ($module->isGedcomBlock() ? '<i class="fa fa-tree" title="' . I18N::translate('Home page') . '"></i>' : '')) : '-', '</td>',
			'<td class="text-center text-muted hidden">', $module instanceof ModuleChartInterface ? '<i class="fa fa-check" title="' . I18N::translate('Chart') . '"></i>' : '-', '</td>',
			'<td class="text-center text-muted hidden-xs">', $module instanceof ModuleReportInterface ? '<i class="fa fa-file" title="' . I18N::translate('Report') . '"></i>' : '-', '</td>',
			'<td class="text-center text-muted hidden">', $module instanceof ModuleThemeInterface ? '<i class="fa fa-check" title="' . I18N::translate('Theme') . '"></i>' : '-', '</td>',
			'</tr>';
		}
		?>
		</tbody>
	</table>
	<button class="btn btn-primary" type="submit">
		<i class="fa fa-check"></i>
		<?php echo I18N::translate('save'); ?></button>
</form>
