<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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

class page_menu_WT_Module extends WT_Module implements WT_Module_Menu {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/menu */ WT_I18N::translate('Edit');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Edit” module */ WT_I18N::translate('An edit menu for individuals, families, sources, etc.');
	}

	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 10;
	}

	// Implement WT_Module_Menu
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
