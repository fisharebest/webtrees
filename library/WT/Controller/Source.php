<?php
// Controller for the Source Page
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class WT_Controller_Source extends WT_Controller_Base {
	var $sid;
	var $source = null;
	var $diffsource = null;
	var $accept_success = false;

	function init() {
		$this->sid = safe_GET_xref('sid');

		$gedrec = find_source_record($this->sid, WT_GED_ID);

		if (find_source_record($this->sid, WT_GED_ID) || find_updated_record($this->sid, WT_GED_ID)!==null) {
			$this->source = new WT_Source($gedrec);
			$this->source->ged_id=WT_GED_ID; // This record is from a file
		} else if (!$gedrec) {
			return false;
		}

		$this->rid=$this->source->getXref(); // Correct upper/lower case mismatch

		if (!$this->source->canDisplayDetails()) {
			print_header(WT_I18N::translate('Source'));
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
					'type'     => 'SOUR',
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
				accept_all_changes($this->sid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				//-- check if we just deleted the record and redirect to index
				$gedrec = find_source_record($this->sid, WT_GED_ID);
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->source = new WT_Source($gedrec);
			}
			unset($_GET['action']);
			break;
		case 'undo':
			if (WT_USER_CAN_ACCEPT) {
				reject_all_changes($this->sid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				$gedrec = find_source_record($this->sid, WT_GED_ID);
				//-- check if we just deleted the record and redirect to index
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->source = new WT_Source($gedrec);
			}
			unset($_GET['action']);
			break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT) {
			$newrec = find_updated_record($this->sid, WT_GED_ID);
			if (!empty($newrec)) {
				$this->diffsource = new WT_Source($newrec);
				$this->diffsource->setChanged(true);
			}
		}

		if ($this->show_changes) {
			$this->source->diffMerge($this->diffsource);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		if ($this->source) {
			return $this->source->getFullName()." - ".WT_I18N::translate('Source Information');
		} else {
			return WT_I18N::translate('Unable to find record with ID');
		}
	}

	/**
	* get edit menu
	*/
	function getEditMenu() {
		global $SHOW_GEDCOM_RECORD;

		if (!$this->source) return null;

		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'));
		$menu->addIcon('edit_sour');
		$menu->addClass('submenuitem', 'submenuitem_hover', 'submenu', 'icon_large_gedcom');

		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit Source'));
			$submenu->addOnclick('return edit_source(\''.$this->sid.'\');');
			$submenu->addIcon('edit_sour');
			$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
			$menu->addSubmenu($submenu);

			$menu->addSeparator();
		}

		// show/hide changes
		if (find_updated_record($this->sid, WT_GED_ID)!==null) {
			if (!$this->show_changes) {
				$submenu = new WT_Menu(WT_I18N::translate('This record has been updated.  Click here to show changes.'), "source.php?sid={$this->sid}&amp;show_changes=yes");
				$submenu->addIcon('edit_sour');
			} else {
				$submenu = new WT_Menu(WT_I18N::translate('Click here to hide changes.'), "source.php?sid={$this->sid}&amp;show_changes=no");
				$submenu->addIcon('edit_sour');
			}
			$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new WT_Menu(WT_I18N::translate('Undo all changes'), "source.php?sid={$this->sid}&amp;action=undo");
				$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
				$submenu->addIcon('edit_sour');
				$menu->addSubmenu($submenu);
				$submenu = new WT_Menu(WT_I18N::translate('Approve all changes'), "source.php?sid={$this->sid}&amp;action=accept");
				$submenu->addIcon('edit_sour');
				$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->sid."');");
			$submenu->addIcon('gedcom');
			$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('View GEDCOM Record'));
			$submenu->addIcon('gedcom');
			if ($this->show_changes && WT_USER_CAN_EDIT) {
				$submenu->addOnclick("return show_gedcom_record('new');");
			} else {
				$submenu->addOnclick("return show_gedcom_record();");
			}
			$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete this Source'));
			$submenu->addOnclick("if (confirm('".WT_I18N::translate('Are you sure you want to delete this Source?')."')) return deletesource('".$this->sid."'); else return false;");
			$submenu->addIcon('remove');
			$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		$submenu = new WT_Menu(WT_I18N::translate('Add to My Favorites'), "source.php?action=addfav&amp;sid={$this->sid}&amp;gid={$this->sid}");
		$submenu->addIcon('favorites');
		$submenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}
		return $menu;
	}
}
