<?php
/**
* Parses gedcom file and gives access to information about a family.
*
* You must supply a $famid value with the identifier for the family.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @package webtrees
* @subpackage Controllers
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FAMILY_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/classes/class_family.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class FamilyRoot extends BaseController {
	var $user = null;
	var $showLivingHusb = true;
	var $showLivingWife = true;
	var $parents = '';
	var $display = false;
	var $accept_success = false;
	var $show_changes = true;
	var $famrec = '';
	var $link_relation = 0;
	var $title = '';
	var $famid = '';
	var $family = null;
	var $difffam = null;

	/**
	* constructor
	*/
	function FamilyRoot() {
		parent::BaseController();
	}

	function init() {
		global $Dbwidth, $bwidth, $pbwidth, $pbheight, $bheight, $GEDCOM, $CONTACT_EMAIL, $show_famlink;
		$bwidth = $Dbwidth;
		$pbwidth = $bwidth + 12;
		$pbheight = $bheight + 14;

		$show_famlink = $this->view!='preview';

		$this->famid = safe_GET_xref('famid');

		$this->family = Family::getInstance($this->famid);

		if (empty($this->famrec)) {
			$ct = preg_match('/(\w+):(.+)/', $this->famid, $match);
			if ($ct>0) {
				$servid = trim($match[1]);
				$remoteid = trim($match[2]);
				require_once WT_ROOT.'includes/classes/class_serviceclient.php';
				$service = ServiceClient::getInstance($servid);
				if (!is_null($service)) {
					$newrec= $service->mergeGedcomRecord($remoteid, "0 @".$this->famid."@ FAM\n1 RFN ".$this->famid, false);
					$this->famrec = $newrec;
				}
			}
			//-- if no record was found create a default empty one
			if (find_updated_record($this->famid, WT_GED_ID)!==null){
				$this->famrec = "0 @".$this->famid."@ FAM\n";
				$this->family = new Family($this->famrec);
			} else if (empty($this->family)){
				return false;
			}
		}

		$this->famrec = $this->family->getGedcomRecord();
		$this->display = displayDetailsById($this->famid, 'FAM');

		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT && find_updated_record($this->famid, WT_GED_ID)!==null) {
			$newrec = find_gedcom_record($this->famid, WT_GED_ID, true);
			$this->difffam = new Family($newrec);
			$this->difffam->setChanged(true);
			$this->family->diffMerge($this->difffam);
			//$this->famrec = $newrec;
			//$this->family = new Family($this->famrec);
		}
		$this->parents = array('HUSB'=>$this->family->getHusbId(), 'WIFE'=>$this->family->getWifeId());

		//-- check if we can display both parents
		if ($this->display == false) {
			$this->showLivingHusb = showLivingNameById($this->parents['HUSB']);
			$this->showLivingWife = showLivingNameById($this->parents['WIFE']);
		}

		//-- add favorites action
		if ($this->action=='addfav' && !empty($_REQUEST['gid']) && WT_USER_NAME) {
			$_REQUEST['gid'] = strtoupper($_REQUEST['gid']);
			$indirec = find_family_record($_REQUEST['gid'], WT_GED_ID);
			if ($indirec) {
				$favorite = array(
					'username' => WT_USER_NAME,
					'gid' => $_REQUEST['gid'],
					'type' => 'FAM',
					'file' => $GEDCOM,
					'url' => '',
					'note' => '',
					'title' => ''
				);
				addFavorite($favorite);
			}
		}

		if (WT_USER_CAN_ACCEPT) {
			if ($this->action=='accept') {
				accept_all_changes($this->famid, WT_GED_ID);
				$this->show_changes = false;
				$this->accept_success = true;
				//-- check if we just deleted the record and redirect to index
				$famrec = find_family_record($this->famid, WT_GED_ID);
				if (empty($famrec)) {
					header("Location: index.php?ctype=gedcom");
					exit;
				}
				$this->family = new Family($famrec);
				$this->parents = find_parents($_REQUEST['famid']);
			}

			if ($this->action=='undo') {
				reject_all_changes($this->famid, WT_GED_ID);
				$this->show_changes = false;
				$this->accept_success = true;
				$this->family = new Family($famrec);
				$this->parents = find_parents($this->famid);
			}
		}

		//-- make sure we have the true id from the record
		$ct = preg_match("/0 @(.*)@/", $this->famrec, $match);
		if ($ct > 0) {
			$this->famid = trim($match[1]);
		}

		if ($this->showLivingHusb == false && $this->showLivingWife == false) {
			print_header(i18n::translate('Private')." ".i18n::translate('Family Information'));
			print_privacy_error();
			print_footer();
			exit;
		}

		$this->title=$this->family->getFullName();

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
		return $this->parents['HUSB'];
	}

	function getWife() {
		if (!is_null($this->difffam)) return $this->difffam->getWifeId();
		return $this->parents['WIFE'];
	}

	function getChildren() {
		return find_children_in_record($this->famrec);
	}

	function getChildrenUrlTimeline($start=0) {
		$children = $this->getChildren();
		$c = count($children);
		for ($i = 0; $i < $c; $i++) {
			$children[$i] = 'pids['.($i + $start).']='.$children[$i];
		}
		return join('&amp;', $children);
	}

	function getPageTitle() {
		if ($this->family) {
			return PrintReady($this->title);
		} else {
			return i18n::translate('Unable to find record with ID');
		}
	}

	/**
	* get the family page charts menu
	* @return Menu
	*/
	function &getChartsMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
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
				$link .= '&pids[1]='.$wife;
				$c++;
			}
		} else if ($wife) {
			$link .= 'pids[0]='.$wife;
			$c++;
		}

		// charts menu
		$menu = new Menu(i18n::translate('Charts'), encode_url('timeline.php?'.$link));
		if (!empty($WT_IMAGES["timeline"]["small"])) {
			$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["parentTimeLine"] = i18n::translate('Show couple on timeline chart');
		$menuList["childTimeLine"] = i18n::translate('Show children on timeline chart');
		$menuList["familyTimeLine"] = i18n::translate('Show family on timeline chart');
		asort($menuList);

		// Produce the submenus in localized name order

		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "parentTimeLine":
				// charts / parents_timeline
				$submenu = new Menu(i18n::translate('Show couple on timeline chart'), encode_url('timeline.php?'.$link));
				if (!empty($WT_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "childTimeLine":
				// charts / children_timeline
				$submenu = new Menu(i18n::translate('Show children on timeline chart'), encode_url('timeline.php?'.$this->getChildrenUrlTimeline()));
				if (!empty($WT_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			case "familyTimeLine":
				// charts / family_timeline
				$submenu = new Menu(i18n::translate('Show family on timeline chart'), encode_url('timeline.php?'.$link.'&'.$this->getChildrenUrlTimeline($c)));
				if (!empty($WT_IMAGES["timeline"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
				break;

			}
		}

		return $menu;
	}

	/**
	* get the family page reports menu
	* @deprecated This function has been deprecated by the getReportsMenu function in menu.php
	* @return Menu
	*/
	function &getReportsMenu() {
	/**
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		$menu = new Menu(i18n::translate('Reports'), encode_url('reportengine.php?action=setup&report=reports/familygroup.xml&famid='.$this->getFamilyID()));
		if (!empty($WT_IMAGES["reports"]["small"])) {
			$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['reports']['small']}");
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// reports / family_group_report
		$submenu = new Menu(i18n::translate('Family Group Report'), encode_url('reportengine.php?action=setup&report=reports/familygroup.xml&famid='.$this->getFamilyID()));
		if (!empty($WT_IMAGES["reports"]["small"])) {
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['reports']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		return $menu;
		**/
	}

	/**
	* get the family page edit menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		global $SHOW_GEDCOM_RECORD;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		// edit_fam menu
		$menu = new Menu(i18n::translate('Edit Family'));
		$menu->addOnclick("return edit_family('".$this->getFamilyID()."');");
		if (!empty($WT_IMAGES["edit_fam"]["large"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["edit_fam"]["large"]);
		}
		else if (!empty($WT_IMAGES["edit_fam"]["small"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["edit_fam"]["small"]);
		}
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit_fam / edit_fam
		$submenu = new Menu(i18n::translate('Edit Family'));
		$submenu->addOnclick("return edit_family('".$this->getFamilyID()."');");
		if (!empty($WT_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		// edit_fam / members
		$submenu = new Menu(i18n::translate('Change Family Members'));
		$submenu->addOnclick("return change_family_members('".$this->getFamilyID()."');");
		if (!empty($WT_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		// edit_fam / add child
		$submenu = new Menu(i18n::translate('Add a child to this family'));
		$submenu->addOnclick("return addnewchild('".$this->getFamilyID()."');");
		if (!empty($WT_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		// edit_fam / reorder_children
		if ($this->family->getNumberOfChildren() > 1) {
			$submenu = new Menu(i18n::translate('Re-order children'));
			$submenu->addOnclick("return reorder_children('".$this->getFamilyID()."');");
			if (!empty($WT_IMAGES["edit_fam"]["small"])) {
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		if (find_updated_record($this->getFamilyID(), WT_GED_ID)!==null) {
			// edit_fam / separator
			$menu->addSeparator();

			// edit_fam / show/hide changes
			if (!$this->show_changes) {
				$submenu = new Menu(i18n::translate('This record has been updated.  Click here to show changes.'), encode_url('family.php?famid='.$this->getFamilyID().'&show_changes=yes'));
				if (!empty($WT_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			} else {
				$submenu = new Menu(i18n::translate('Click here to hide changes.'), encode_url('family.php?famid='.$this->getFamilyID().'&show_changes=no'));
				if (!empty($WT_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (WT_USER_CAN_ACCEPT) {
				// edit_fam / accept_all
				$submenu = new Menu(i18n::translate('Undo all changes'), encode_url("family.php?famid={$this->famid}&action=undo"));
				if (!empty($WT_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Accept all changes'), encode_url("family.php?famid={$this->famid}&action=accept"));
				if (!empty($WT_IMAGES["edit_fam"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
				}
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
			}
		}

		// edit_fam / separator
		$menu->addSeparator();

		// edit_fam / edit_raw
		if ($SHOW_GEDCOM_RECORD || WT_USER_IS_ADMIN) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->getFamilyID()."');");
			if (!empty($WT_IMAGES["edit_fam"]["small"])) {
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
			}
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit_fam / delete_family
		$submenu = new Menu(i18n::translate('Delete family'));
		$submenu->addOnclick("if (confirm('".i18n::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?')."')) return delete_family('".$this->getFamilyID()."'); else return false;");
		if (!empty($WT_IMAGES["edit_fam"]["small"])) {
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_fam']['small']}");
		}
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}

		return $menu;
	}

	/**
	* get the other menu
	* @return Menu
	*/
	function &getOtherMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		global $SHOW_GEDCOM_RECORD, $ENABLE_CLIPPINGS_CART;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

			// other menu
		$menu = new Menu(i18n::translate('Other'));
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		if ($SHOW_GEDCOM_RECORD) {
			$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
			if ($this->show_changes && WT_USER_CAN_EDIT) {
				$menu->addLink("javascript:show_gedcom_record('new');");
			} else {
				$menu->addLink("javascript:show_gedcom_record();");
			}
		} else {
			if (!empty($WT_IMAGES["clippings"]["small"])) {
				$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['clippings']['small']}");
			}
			$menu->addLink(encode_url('clippings.php?action=add&id='.$this->getFamilyID().'&type=fam'));
		}
		if ($SHOW_GEDCOM_RECORD) {
				// other / view_gedcom
				$submenu = new Menu(i18n::translate('View GEDCOM Record'));
				if ($this->show_changes && WT_USER_CAN_EDIT) {
					$submenu->addLink("javascript:show_gedcom_record('new');");
				} else {
					$submenu->addLink("javascript:show_gedcom_record();");
				}
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($ENABLE_CLIPPINGS_CART >= WT_USER_ACCESS_LEVEL) {
				// other / add_to_cart
				$submenu = new Menu(i18n::translate('Add to Clippings Cart'), encode_url('clippings.php?action=add&id='.$this->getFamilyID().'&type=fam'));
				if (!empty($WT_IMAGES["clippings"]["small"])) {
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['clippings']['small']}");
				}
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->display && WT_USER_ID) {
			// other / add_to_my_favorites
			$submenu = new Menu(i18n::translate('Add to My Favorites'), encode_url('family.php?action=addfav&famid='.$this->getFamilyID().'&gid='.$this->getFamilyID()));
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}

if (file_exists(WT_ROOT.'includes/controllers/family_ctrl_user.php')) {
	require_once WT_ROOT.'includes/controllers/family_ctrl_user.php';
} else {
	class FamilyController extends FamilyRoot {
	}
}

?>
