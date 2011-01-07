<?php
// Controller for the Family Page
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
require_once WT_ROOT.'includes/functions/functions_charts.php';

class WT_Controller_Family extends WT_Controller_Base {
	var $famid = '';
	var $family = null;
	var $difffam = null;
	var $accept_success = false;
	var $user = null;
	var $showLivingHusb = true;
	var $showLivingWife = true;
	var $parents = '';
	var $display = false;
	var $show_changes = true;
	var $famrec = '';
	var $title = '';

	function init() {
		global $Dbwidth, $bwidth, $pbwidth, $pbheight, $bheight, $GEDCOM;
		$bwidth = $Dbwidth;
		$pbwidth = $bwidth + 12;
		$pbheight = $bheight + 14;

		$this->famid = safe_GET_xref('famid');

		$gedrec = find_family_record($this->famid, WT_GED_ID);

		if (empty($gedrec)) {
			$gedrec = "0 @".$this->famid."@ FAM\n";
		}

		if (find_family_record($this->famid, WT_GED_ID) || find_updated_record($this->famid, WT_GED_ID)!==null) {
			$this->family = new WT_Family($gedrec);
			$this->family->ged_id=WT_GED_ID; // This record is from a file
		} else if (!$this->family) {
			return false;
		}

		$this->famid=$this->family->getXref(); // Correct upper/lower case mismatch

		//-- perform the desired action
		switch($this->action) {
		case 'addfav':
			if (WT_USER_ID && !empty($_REQUEST['gid']) && array_key_exists('user_favorites', WT_Module::getActiveModules())) {
				$favorite = array(
					'username' => WT_USER_NAME,
					'gid'      => $_REQUEST['gid'],
					'type'     => 'FAM',
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
				accept_all_changes($this->famid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				//-- check if we just deleted the record and redirect to index
				$gedrec = find_family_record($this->famid, WT_GED_ID);
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->family = new WT_Family($gedrec);
			}
			unset($_GET['action']);
			break;
		case 'undo':
			if (WT_USER_CAN_ACCEPT) {
				reject_all_changes($this->famid, WT_GED_ID);
				$this->show_changes=false;
				$this->accept_success=true;
				$gedrec = find_family_record($this->famid, WT_GED_ID);
				//-- check if we just deleted the record and redirect to index
				if (empty($gedrec)) {
					header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH);
					exit;
				}
				$this->family = new WT_Family($gedrec);
			}
			unset($_GET['action']);
			break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT) {
			$newrec = find_updated_record($this->famid, WT_GED_ID);
			if (!empty($newrec)) {
				$this->difffam = new WT_Family($newrec);
				$this->difffam->setChanged(true);
			}
		}

		if ($this->show_changes) {
			$this->family->diffMerge($this->difffam);
		}

		$this->parents = array('HUSB'=>$this->family->getHusbId(), 'WIFE'=>$this->family->getWifeId());

		//-- check if we can display both parents
		if ($this->display == false) {
			$this->showLivingHusb = showLivingNameById($this->parents['HUSB']);
			$this->showLivingWife = showLivingNameById($this->parents['WIFE']);
		}

		if ($this->showLivingHusb == false && $this->showLivingWife == false) {
			print_header(WT_I18N::translate('Family'));
			print_privacy_error();
			print_footer();
			exit;
		}
	}

	function getFamilyID() {
		return $this->famid;
	}

	function getHusband() {
		if (!is_null($this->difffam)) return $this->difffam->getHusbId();
		if ($this->family) return $this->parents['HUSB'];
		return null;
	}

	function getWife() {
		if (!is_null($this->difffam)) return $this->difffam->getWifeId();
		if ($this->family) return $this->parents['WIFE'];
		return null;
	}

	// $tags is an array of HUSB/WIFE/CHIL
	function getTimelineIndis($tags) {
		preg_match_all('/\n1 (?:'.implode('|', $tags).') @('.WT_REGEX_XREF.')@/', $this->family->getGedcomRecord(), $matches);
		foreach ($matches[1] as &$match) {
			$match='pids[]='.$match;
		}
		return implode('&amp;', $matches[1]);
	}

	/**
	* return the title of this page
	* @return string the title of the page to go in the <title> tags
	*/
	function getPageTitle() {
		if ($this->family) {
			return $this->family->getFullName();
		} else {
			return WT_I18N::translate('Unable to find record with ID');
		}
	}

	/**
	* get edit menu
	*/
	function getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $GEDCOM, $SHOW_GEDCOM_RECORD;

		if (!$this->family) return null;
		if ($TEXT_DIRECTION=="rtl") {
			$ff="_rtl";
		} else {
			$ff="";
		}
		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'));
		$menu->addIcon('edit_fam');
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}", 'icon_large_gedcom');

		if (WT_USER_CAN_EDIT) {
			// edit_fam / members
			$submenu = new WT_Menu(WT_I18N::translate('Change Family Members'));
			$submenu->addOnclick("return change_family_members('".$this->getFamilyID()."');");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			// edit_fam / add child
			$submenu = new WT_Menu(WT_I18N::translate('Add a child to this family'));
			$submenu->addOnclick("return addnewchild('".$this->getFamilyID()."');");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			// edit_fam / reorder_children
			if ($this->family->getNumberOfChildren() > 1) {
				$submenu = new WT_Menu(WT_I18N::translate('Re-order children'));
				$submenu->addOnclick("return reorder_children('".$this->getFamilyID()."');");
				$submenu->addIcon('edit_fam');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// show/hide changes
		if (find_updated_record($this->getFamilyID(), WT_GED_ID)!==null) {
			if (!$this->show_changes) {
				$label = WT_I18N::translate('This record has been updated.  Click here to show changes.');
				$link = $this->family->getHtmlUrl().'&amp;show_changes=yes';
			} else {
				$label = WT_I18N::translate('Click here to hide changes.');
				$link = $this->family->getHtmlUrl().'&amp;show_changes=no';
			}
			$submenu = new WT_Menu($label, $link);
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new WT_Menu(WT_I18N::translate('Undo all changes'), "family.php?famid={$this->famid}&amp;action=undo");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$submenu->addIcon('edit_fam');
				$menu->addSubmenu($submenu);
				$submenu = new WT_Menu(WT_I18N::translate('Approve all changes'), "family.php?famid={$this->famid}&amp;action=accept");
				$submenu->addIcon('edit_fam');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->getFamilyID()."');");
			$submenu->addIcon('gedcom');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('View GEDCOM Record'));
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
			$submenu = new WT_Menu(WT_I18N::translate('Delete family'));
			$submenu->addOnclick("if (confirm('".WT_I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?')."')) return delete_family('".$this->getFamilyID()."'); else return false;");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		$submenu = new WT_Menu(WT_I18N::translate('Add to My Favorites'), 'family.php?action=addfav&amp;famid='.$this->getFamilyID().'&gamp;id='.$this->getFamilyID());
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
