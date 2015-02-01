<?php
namespace Webtrees;

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

/**
 * Class batch_update_WT_Module
 */
class batch_update_WT_Module extends Module implements ModuleConfigInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Batch update');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Batch update” module */ I18N::translate('Apply automatic corrections to your genealogy data.');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin_batch_update':
			$controller = new PageController;
			$controller
				->setPageTitle(I18N::translate('Batch update'))
				->restrictAccess(Auth::isAdmin())
				->pageHeader();

			// TODO: these files should be methods in this class
			require WT_ROOT . WT_MODULES_DIR . $this->getName() . '/' . $mod_action . '.php';
			$mod = new batch_update;
			echo $mod->main();
			break;
		default:
			http_response_code(404);
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_batch_update';
	}
}
