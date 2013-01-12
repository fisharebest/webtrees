<?php
// Controller for the repository page
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class WT_Controller_Repository extends WT_Controller_GedcomRecord {
	public function __construct() {
		$xref = safe_GET_xref('rid');

		$gedrec=find_other_record($xref, WT_GED_ID);
		if (WT_USER_CAN_EDIT) {
			$newrec=find_updated_record($xref, WT_GED_ID);
		} else {
			$newrec=null;
		}

		if ($gedrec===null) {
			if ($newrec===null) {
				// Nothing to see here.
				parent::__construct();
				return;
			} else {
				// Create a dummy record from the first line of the new record.
				// We need it for diffMerge(), getXref(), etc.
				list($gedrec)=explode("\n", $newrec);
			}
		}

		$this->record = new WT_Repository($gedrec);

		// If there are pending changes, merge them in.
		if ($newrec!==null) {
			$diff_record=new WT_Repository($newrec);
			$diff_record->setChanged(true);
			$this->record->diffMerge($diff_record);
		}

		parent::__construct();
	}

	/**
	* get edit menu
	*/
	function getEditMenu() {
		$SHOW_GEDCOM_RECORD=get_gedcom_setting(WT_GED_ID, 'SHOW_GEDCOM_RECORD');

		if (!$this->record || $this->record->isMarkedDeleted()) {
			return null;
		}

		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-repo');

		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit repository'), '#', 'menu-repo-edit');
			$submenu->addOnclick('return edit_source(\''.$this->record->getXref().'\');');
			$menu->addSubmenu($submenu);
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM record'), '#', 'menu-repo-editraw');
			$submenu->addOnclick("return edit_raw('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('View GEDCOM Record'), '#', 'menu-repo-viewraw');
			if (WT_USER_CAN_EDIT) {
				$submenu->addOnclick("return show_gedcom_record('new');");
			} else {
				$submenu->addOnclick("return show_gedcom_record();");
			}
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-repo-del');
			$submenu->addOnclick("if (confirm('".WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($this->record->getFullName()))."')) jQuery.post('action.php',{action:'delete-repository',xref:'".$this->record->getXref()."'},function(){location.reload();})");
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
