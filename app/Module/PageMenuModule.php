<?php
namespace Fisharebest\Webtrees;

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
 * Class PageMenuModule
 */
class PageMenuModule extends Module implements ModuleMenuInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/menu */ I18N::translate('Edit');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Edit” module */ I18N::translate('An edit menu for individuals, families, sources, etc.');
	}

	/** {@inheritdoc} */
	public function defaultMenuOrder() {
		return 10;
	}

	/** {@inheritdoc} */
	public function getMenu() {
		global $controller;

		$menu = null;
		if (empty($controller)) {
			return null;
		}

		if (WT_USER_CAN_EDIT && method_exists($controller, 'getEditMenu')) {
			$menu = $controller->getEditMenu();
		}
		return $menu;
	}
}
