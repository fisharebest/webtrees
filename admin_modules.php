<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\AdminController;

require 'includes/session.php';

$controller = new AdminController;
$controller
	->restrictAccess(Auth::isAdmin());

$modules       = Module::getInstalledModules('disabled');
$module_status = Database::prepare("SELECT module_name, status FROM `##module`")->fetchAssoc();

if (Filter::post('action') === 'update_mods' && Filter::checkCsrf()) {
	foreach ($modules as $module) {
		$new_status = Filter::postBool('status-' . $module->getName()) ? 'enabled' : 'disabled';
		$old_status = $module_status[$module->getName()];
		if ($new_status !== $old_status) {
			Database::prepare("UPDATE `##module` SET status=? WHERE module_name=?")->execute([$new_status, $module->getName()]);
			if ($new_status === 'disabled') {
				FlashMessages::addMessage(I18N::translate('The module “%s” has been disabled.', $module->getTitle()), 'success');
			} else {
				FlashMessages::addMessage(I18N::translate('The module “%s” has been enabled.', $module->getTitle()), 'success');
			}
		}
	}

	header('Location: admin_modules.php');

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
	)->execute([$module_name]);
	Database::prepare(
		"DELETE `##block`" .
		" FROM `##block`" .
		" JOIN `##module` USING (module_name)" .
		" WHERE module_name=?"
	)->execute([$module_name]);
	Database::prepare("DELETE FROM `##module_setting` WHERE module_name=?")->execute([$module_name]);
	Database::prepare("DELETE FROM `##module_privacy` WHERE module_name=?")->execute([$module_name]);
	Database::prepare("DELETE FROM `##module` WHERE module_name=?")->execute([$module_name]);

	FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been deleted.', $module_name), 'success');

	header('Location: admin_modules.php');

	return;
}

$controller->modules();
