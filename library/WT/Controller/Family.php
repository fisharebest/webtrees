<?php
// Controller for the family page
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

class WT_Controller_Family extends WT_Controller_GedcomRecord {
	var $diff_record;
	var $record = null;
	var $user = null;
	var $display = false;
	var $famrec = '';
	var $title = '';

	public function __construct() {
		global $Dbwidth, $bwidth, $pbwidth, $pbheight, $bheight;
		$bwidth = $Dbwidth;
		$pbwidth = $bwidth + 12;
		$pbheight = $bheight + 14;

		$xref = safe_GET_xref('famid');

		$gedrec = find_family_record($xref, WT_GED_ID);

		if (empty($gedrec)) {
			$gedrec = "0 @".$xref."@ FAM\n";
		}

		if (find_family_record($xref, WT_GED_ID) || find_updated_record($xref, WT_GED_ID)!==null) {
			$this->record = new WT_Family($gedrec);
			$this->record->ged_id=WT_GED_ID; // This record is from a file
		} else if (!$this->record) {
			return false;
		}

		$xref=$this->record->getXref(); // Correct upper/lower case mismatch

		//-- if the user can edit and there are changes then get the new changes
		if (WT_USER_CAN_EDIT) {
			$newrec = find_updated_record($xref, WT_GED_ID);
			if (!empty($newrec)) {
				$this->diff_record = new WT_Family($newrec);
				$this->diff_record->setChanged(true);
				$this->record->diffMerge($this->diff_record);
			}
		}

		parent::__construct();
	}

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantIndividual() {
		if ($this->record) {
			foreach ($this->record->getSpouses() as $individual) {
				return $individual;
			}
			foreach ($this->record->getChildren() as $individual) {
				return $individual;
			}
		}
		return parent::getSignificantIndividual();
	}
	public function getSignificantFamily() {
		if ($this->record) {
			return $this->record;
		}
		return parent::getSignificantFamily();
	}

	// $tags is an array of HUSB/WIFE/CHIL
	function getTimelineIndis($tags) {
		preg_match_all('/\n1 (?:'.implode('|', $tags).') @('.WT_REGEX_XREF.')@/', $this->record->getGedcomRecord(), $matches);
		foreach ($matches[1] as &$match) {
			$match='pids[]='.$match;
		}
		return implode('&amp;', $matches[1]);
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
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-fam');
		$menu->addLabel($menu->label, 'down');

		if (WT_USER_CAN_EDIT) {
			// edit_fam / members
			$submenu = new WT_Menu(WT_I18N::translate('Change Family Members'), '#', 'menu-fam-change');
			$submenu->addOnclick("return change_family_members('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);

			// edit_fam / add child
			$submenu = new WT_Menu(WT_I18N::translate('Add a child to this family'), '#', 'menu-fam-addchil');
			$submenu->addOnclick("return addnewchild('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);

			// edit_fam / reorder_children
			if ($this->record->getNumberOfChildren() > 1) {
				$submenu = new WT_Menu(WT_I18N::translate('Re-order children'), '#', 'menu-fam-orderchil');
				$submenu->addOnclick("return reorder_children('".$this->record->getXref()."');");
				$menu->addSubmenu($submenu);
			}
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM record'), '#', 'menu-fam-editraw');
			$submenu->addOnclick("return edit_raw('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('View GEDCOM Record'), '#', 'menu-fam-viewraw');
			if (WT_USER_CAN_EDIT) {
				$submenu->addOnclick("return show_gedcom_record('new');");
			} else {
				$submenu->addOnclick("return show_gedcom_record();");
			}
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-fam-del');
			$submenu->addOnclick("if (confirm('".WT_I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?')."')) jQuery.post('action.php',{action:'delete-family',xref:'".$this->record->getXref()."'},function(){location.reload();})");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$submenu = new WT_Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ WT_I18N::translate('Add to favorites'),
				'#',
				'menu-fam-addfav'
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

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantSurname() {
		if ($this->record && $this->record->getHusband()) {
			list($surn, $givn)=explode(',', $this->record->getHusband()->getSortname());
			return $surn;
		} else {
			return '';
		}
	}
}
