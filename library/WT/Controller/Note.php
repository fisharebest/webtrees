<?php
// Controller for the Shared Note Page
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2009 PGV Development Team.  All rights reserved.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class WT_Controller_Note extends WT_Controller_Base {
	var $nid;
	var $note = null;
	var $diffnote = null;
	var $accept_success = false;

	function init() {
		$this->nid = safe_GET_xref('nid');

		$gedrec = find_other_record($this->nid, WT_GED_ID);

		if (find_other_record($this->nid, WT_GED_ID) || find_updated_record($this->nid, WT_GED_ID)!==null) {
			$this->note = new WT_Note($gedrec);
			$this->note->ged_id=WT_GED_ID; // This record is from a file
		} else if (!$gedrec) {
			return false;
		}

		$this->nid=$this->note->getXref(); // Correct upper/lower case mismatch

		if (!$this->note->canDisplayDetails()) {
			print_header(i18n::translate('Shared note'));
			print_privacy_error();
			print_footer();
			exit;
		}

		//-- perform the desired action
		switch($this->action) {
		case 'addfav':
			if (WT_USER_ID && !empty($_REQUEST['gid']) && array_key_exists('user_favorites', WT_Module::getActiveModules())) {
				$favorite = array(
					'username' => WT_USER_NAME,
					'gid'      => $_REQUEST['gid'],
					'type'     => 'NOTE',
					'file'     => WT_GEDCOM,
					'url'      => '',
					'note'     => '',
					'title'    => ''
				);
				user_favorites_WT_Module::addFavorite($favorite);
			}
			unset($_GET['action']);
			break;
		case 'accept':
			if (WT_USER_CAN_ACCEPT) {
				accept_all_changes($this->nid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				//-- check if we just deleted the record and redirect to index
				$gedrec = find_other_record($this->nid, WT_GED_ID);
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->note = new WT_Note($gedrec);
			}
			unset($_GET['action']);
			break;
		case 'undo':
			if (WT_USER_CAN_ACCEPT) {
				reject_all_changes($this->nid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				$gedrec = find_other_record($this->nid, WT_GED_ID);
				//-- check if we just deleted the record and redirect to index
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->note = new WT_Note($gedrec);
			}
			unset($_GET['action']);
			break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT) {
			$newrec = find_updated_record($this->nid, WT_GED_ID);
			if (!empty($newrec)) {
				$this->diffnote = new WT_Note($newrec);
				$this->diffnote->setChanged(true);
			}
		}

		if ($this->show_changes) {
			$this->note->diffMerge($this->diffnote);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		if ($this->note) {
			return $this->note->getFullName()." - ".i18n::translate('Shared Note Information');
		} else {
			return i18n::translate('Unable to find record with ID');
		}
	}

	/**
	* get edit menu
	*/
	function getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $GEDCOM, $SHOW_GEDCOM_RECORD;

		if (!$this->note) return null;
		if ($TEXT_DIRECTION=="rtl") {
			$ff="_rtl";
		} else {
			$ff="";
		}
		// edit menu
		$menu = new Menu(i18n::translate('Edit'));
		$menu->addIcon('edit_note');
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}", 'icon_large_gedcom');

		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(i18n::translate('Edit note'));
			$submenu->addOnclick('return edit_note(\''.$this->nid.'\');');
			$submenu->addIcon('edit_note');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			$menu->addSeparator();
		}

		// show/hide changes
		if (find_updated_record($this->nid, WT_GED_ID)!==null) {
			if (!$this->show_changes) {
				$submenu = new Menu(i18n::translate('This record has been updated.  Click here to show changes.'), "note.php?nid={$this->nid}&amp;show_changes=yes");
				$submenu->addIcon('edit_note');
			} else {
				$submenu = new Menu(i18n::translate('Click here to hide changes.'), "note.php?nid={$this->nid}&amp;show_changes=no");
				$submenu->addIcon('edit_note');
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new Menu(i18n::translate('Undo all changes'), "note.php?nid={$this->nid}&amp;action=undo");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$submenu->addIcon('notes');
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Approve all changes'), "note.php?nid={$this->nid}&amp;action=accept");
				$submenu->addIcon('notes');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->nid."');");
			$submenu->addIcon('gedcom');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new Menu(i18n::translate('View GEDCOM Record'));
			$submenu->addIcon('gedcom');
			if ($this->show_changes && WT_USER_CAN_EDIT) {
				$submenu->addOnclick("return show_gedcom_record('new');");
			} else {
				$submenu->addOnclick("return show_gedcom_record();");
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(i18n::translate('Delete this Shared Note'));
			$submenu->addOnclick("if (confirm('".i18n::translate('Are you sure you want to delete this Shared Note?')."')) return deletenote('".$this->nid."'); else return false;");
			$submenu->addIcon('edit_note');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		$submenu = new Menu(i18n::translate('Add to My Favorites'), "note.php?action=addfav&amp;nid={$this->nid}&amp;gid={$this->nid}");
		$submenu->addIcon('favorites');
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}
		return $menu;
	}
}
