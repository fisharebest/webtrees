<?php
// Controller for the Family Page
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

define('WT_FAMILY_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/classes/class_gedcomrecord.php';
require_once WT_ROOT.'includes/functions/functions_import.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

class FamilyController extends BaseController {
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
	var $link_relation = 0;
	var $title = '';

	function init() {
		global $Dbwidth, $bwidth, $pbwidth, $pbheight, $bheight, $GEDCOM;
		$bwidth = $Dbwidth;
		$pbwidth = $bwidth + 12;
		$pbheight = $bheight + 14;

		$this->famid = safe_GET_xref('famid');

		$this->family = Family::getInstance($this->famid);

		if (empty($this->famrec)) {
			//-- if no record was found create a default empty one
			if (find_updated_record($this->famid, WT_GED_ID)!==null) {
				$this->famrec = "0 @".$this->famid."@ FAM\n";
				$this->family = new Family($this->famrec);
			} else if (!$this->family) {
				return false;
			}
		}

		$this->famrec = $this->family->getGedcomRecord();
		$this->display = $this->family->canDisplayName();

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
				$this->family = new Family($gedrec);
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
				$this->family = new Family($gedrec);
			}
			unset($_GET['action']);
			break;
		}

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT && find_updated_record($this->famid, WT_GED_ID)!==null) {
			$newrec = find_gedcom_record($this->famid, WT_GED_ID, true);
			$this->difffam = new Family($newrec);
			$this->difffam->setChanged(true);
			$this->family->diffMerge($this->difffam);
		}
		$this->parents = array('HUSB'=>$this->family->getHusbId(), 'WIFE'=>$this->family->getWifeId());

		//-- check if we can display both parents
		if ($this->display == false) {
			$this->showLivingHusb = showLivingNameById($this->parents['HUSB']);
			$this->showLivingWife = showLivingNameById($this->parents['WIFE']);
		}

		//-- make sure we have the true id from the record
		$ct = preg_match("/0 @(.*)@/", $this->famrec, $match);
		if ($ct > 0) {
			$this->famid = trim($match[1]);
		}

		if ($this->showLivingHusb == false && $this->showLivingWife == false) {
			print_header(i18n::translate('Family'));
			print_privacy_error();
			print_footer();
			exit;
		}

		if (empty($this->parents['HUSB']) || empty($this->parents['WIFE'])) {
			$this->link_relation = 0;
		} else {
			$this->link_relation = 1;
		}
	}

	function getFamilyID() {
		return $this->famid;
	}

	function getFamilyRecord() {
		return $this->famrec;
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

	function getChildren() {
		if (preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', $this->famrec, $match)) {
			return $match[1];
		} else {
			return array();
		}
	}

	function getChildrenUrlTimeline($start=0) {
		$children = $this->getChildren();
		$c = count($children);
		for ($i = 0; $i < $c; $i++) {
			$children[$i] = 'pids['.($i + $start).']='.$children[$i];
		}
		return join('&amp;', $children);
	}

	/**
	* return the title of this page
	* @return string the title of the page to go in the <title> tags
	*/
	function getPageTitle() {
		if ($this->family) {
			return $this->family->getFullName();
		} else {
			return i18n::translate('Unable to find record with ID');
		}
	}

	/**
	* get the family page charts menu
	* @return Menu
	*/
	function getChartsMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $GEDCOM;

		if (!$this->family) return null;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		$husb = $this->getHusband();
		$wife = $this->getWife();
		$link = '';
		$c = 0;
		if ($husb) {
			$link .= 'pids[0]='.$husb;
			$c++;
			if ($wife) {
				$link .= '&amp;pids[1]='.$wife;
				$c++;
			}
		} else if ($wife) {
			$link .= 'pids[0]='.$wife;
			$c++;
		}

		// charts menu
		$menu = new Menu(i18n::translate('Charts'), 'timeline.php?'.$link);
		$menu->addIcon('timeline');
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["parentTimeLine"] = i18n::translate('Show couple on timeline chart');
		$menuList["childTimeLine"] = i18n::translate('Show children on timeline chart');
		$menuList["familyTimeLine"] = i18n::translate('Show family on timeline chart');
		asort($menuList);

		// Produce the submenus in localized name order

		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "parentTimeLine":
				// charts / parents_timeline
				$submenu = new Menu(i18n::translate('Show couple on timeline chart'), 'timeline.php?'.$link);
				$submenu->addIcon('timeline');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "childTimeLine":
				// charts / children_timeline
				$submenu = new Menu(i18n::translate('Show children on timeline chart'), 'timeline.php?'.$this->getChildrenUrlTimeline());
				$submenu->addIcon('timeline');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "familyTimeLine":
				// charts / family_timeline
				$submenu = new Menu(i18n::translate('Show family on timeline chart'), 'timeline.php?'.$link.'&amp;'.$this->getChildrenUrlTimeline($c));
				$submenu->addIcon('timeline');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
				break;

			}
		}

		return $menu;
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
		$menu = new Menu(i18n::translate('Edit'));
		$menu->addIcon('edit_fam');
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}", 'icon_large_gedcom');

		if (WT_USER_CAN_EDIT) {
			// edit_fam / members
			$submenu = new Menu(i18n::translate('Change Family Members'));
			$submenu->addOnclick("return change_family_members('".$this->getFamilyID()."');");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			// edit_fam / add child
			$submenu = new Menu(i18n::translate('Add a child to this family'));
			$submenu->addOnclick("return addnewchild('".$this->getFamilyID()."');");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			// edit_fam / reorder_children
			if ($this->family->getNumberOfChildren() > 1) {
				$submenu = new Menu(i18n::translate('Re-order children'));
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
				$label = i18n::translate('This record has been updated.  Click here to show changes.');
				$link = $this->family->getHtmlUrl().'&amp;show_changes=yes';
			} else {
				$label = i18n::translate('Click here to hide changes.');
				$link = $this->family->getHtmlUrl().'&amp;show_changes=no';
			}
			$submenu = new Menu($label, $link);
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);

			if (WT_USER_CAN_ACCEPT) {
				$submenu = new Menu(i18n::translate('Undo all changes'), "family.php?famid={$this->famid}&amp;action=undo");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$submenu->addIcon('edit_fam');
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Approve all changes'), "family.php?famid={$this->famid}&amp;action=accept");
				$submenu->addIcon('edit_fam');
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
				$menu->addSubmenu($submenu);
			}

			$menu->addSeparator();
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->getFamilyID()."');");
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
			$submenu = new Menu(i18n::translate('Delete family'));
			$submenu->addOnclick("if (confirm('".i18n::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?')."')) return delete_family('".$this->getFamilyID()."'); else return false;");
			$submenu->addIcon('edit_fam');
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		$submenu = new Menu(i18n::translate('Add to My Favorites'), 'family.php?action=addfav&amp;famid='.$this->getFamilyID().'&gamp;id='.$this->getFamilyID());
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
