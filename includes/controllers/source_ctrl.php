<?php
// Controller for the Source Page
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
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

define('WT_SOURCE_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class SourceController extends BaseController {
	var $sid;
	var $source = null;
	var $uname = "";
	var $diffsource = null;
	var $accept_success = false;
	var $canedit = false;

	function init() {
		$this->sid = safe_GET_xref('sid');

		$sourcerec = find_source_record($this->sid, WT_GED_ID);

		if (find_updated_record($this->sid, WT_GED_ID)!==null) {
			$sourcerec = "0 @".$this->sid."@ SOUR\n";
		} else if (!$sourcerec) {
			return false;
		}

		$this->source = new Source($sourcerec);
		$this->source->ged_id=WT_GED_ID; // This record is from a file

		if (!$this->source->canDisplayDetails()) {
			print_header(i18n::translate('Private')." ".i18n::translate('Source Information'));
			print_privacy_error();
			print_footer();
			exit;
		}

		$this->uname = WT_USER_NAME;

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
			break;
		case 'accept':
			if (WT_USER_CAN_ACCEPT) {
				accept_all_changes($this->sid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				//-- check if we just deleted the record and redirect to index
				$gedrec = find_source_record($this->sid, WT_GED_ID);
				if (empty($gedrec)) {
					header("Location: index.php?ctype=gedcom");
					exit;
				}
				$this->source = new Source($gedrec);
			}
			break;
		case 'undo':
			if (WT_USER_CAN_ACCEPT) {
				reject_all_changes($this->sid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				$gedrec = find_source_record($this->sid, WT_GED_ID);
				//-- check if we just deleted the record and redirect to index
				if (empty($gedrec)) {
					header("Location: index.php?ctype=gedcom");
					exit;
				}
				$this->source = new Source($gedrec);
			}
			break;
		}

		//-- check for the user
		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT && ($newrec = find_updated_record($this->sid, WT_GED_ID))!==null) {
			$this->diffsource = new Source($newrec);
			$this->diffsource->setChanged(true);
			$sourcerec = $newrec;
		}

		if ($this->source->canDisplayDetails()) {
			$this->canedit = WT_USER_CAN_EDIT;
		}

		if ($this->show_changes && $this->canedit) {
			$this->source->diffMerge($this->diffsource);
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		if ($this->source) {
			return $this->source->getFullName()." - ".$this->sid." - ".i18n::translate('Source Information');
		} else {
			return i18n::translate('Unable to find record with ID');
		}
	}
	/**
	* check if use can edit this person
	* @return boolean
	*/
	function userCanEdit() {
		return $this->canedit;
	}

	/**
	* get edit menu
	*/
	function getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $GEDCOM, $SHOW_GEDCOM_RECORD;

		if ($TEXT_DIRECTION=="rtl") {
			$ff="_rtl";
		} else {
			$ff="";
		}
		// edit menu
		$menu = new Menu(i18n::translate('Edit'));
		$menu->addIcon('edit_sour');
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}", 'icon_large_gedcom');

		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(i18n::translate('Edit Source'));
			$submenu->addOnclick('return edit_source(\''.$this->sid.'\');');
			$submenu->addIcon('edit_sour');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			$menu->addSeparator();
		}

		// show/hide changes
		if (find_updated_record($this->sid, WT_GED_ID)!==null) {
			if (!$this->show_changes) {
				$submenu = new Menu(i18n::translate('This record has been updated.  Click here to show changes.'), encode_url("source.php?sid={$this->sid}&show_changes=yes"));
				$submenu->addIcon('edit_sour');
			} else {
				$submenu = new Menu(i18n::translate('Click here to hide changes.'), encode_url("source.php?sid={$this->sid}&show_changes=no"));
				$submenu->addIcon('edit_sour');
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new Menu(i18n::translate('Undo all changes'), encode_url("source.php?sid={$this->sid}&action=undo"));
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$submenu->addIcon('edit_sour');
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Accept all changes'), encode_url("source.php?sid={$this->sid}&action=accept"));
				$submenu->addIcon('edit_sour');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->sid."');");
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
			$submenu = new Menu(i18n::translate('Delete this Source'));
			$submenu->addOnclick("if (confirm('".i18n::translate('Are you sure you want to delete this Source?')."')) return deletesource('".$this->sid."'); else return false;");
			$submenu->addIcon('edit_sour');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		$submenu = new Menu(i18n::translate('Add to My Favorites'), encode_url("source.php?action=addfav&sid={$this->sid}&gid={$this->sid}"));
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
