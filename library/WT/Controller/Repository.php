<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_print_facts.php';

/**
 * Class WT_Controller_Repository - Controller for the repository page
 */
class WT_Controller_Repository extends WT_Controller_GedcomRecord {
	/**
	 * Startup activity
	 */
	public function __construct() {
		$xref         = WT_Filter::get('rid', WT_REGEX_XREF);
		$this->record = WT_Repository::getInstance($xref);

		parent::__construct();
	}

	/**
	 * get edit menu
	 */
	function getEditMenu() {
		global $WT_TREE;

		$SHOW_GEDCOM_RECORD = $WT_TREE->getPreference('SHOW_GEDCOM_RECORD');

		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-repo');

		if (WT_USER_CAN_EDIT) {
			$fact = $this->record->getFirstFact('NAME');
			$submenu = new WT_Menu(WT_I18N::translate('Edit repository'), '#', 'menu-repo-edit');
			if ($fact) {
				// Edit existing name
				$submenu->addOnclick('return edit_record(\'' . $this->record->getXref() . '\', \'' . $fact->getFactId() . '\');');
			} else {
				// Add new name
				$submenu->addOnclick('return add_fact(\'' . $this->record->getXref() . '\', \'NAME\');');
			}
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-repo-del');
			$submenu->addOnclick("return delete_repository('" . WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($this->record->getFullName()))."', '".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || WT_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM'), '#', 'menu-repo-editraw');
			$submenu->addOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$submenu = new WT_Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ WT_I18N::translate('Add to favorites'),
				'#',
				'menu-repo-addfav'
			);
			$submenu->addOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'".$this->record->getXref()."'},function(){location.reload();})");
			$menu->addSubmenu($submenu);
		}

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}
		return $menu;
	}
}
