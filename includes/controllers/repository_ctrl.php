<?php
/**
* Controller for the repository page view
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @subpackage Charts
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_REPOSITORY_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_repository.php';
require_once WT_ROOT.'includes/classes/class_menu.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

$nonfacts = array();
/**
* Main controller class for the repository page.
*/
class RepositoryControllerRoot extends BaseController {
	var $rid;
	var $repository = null;
	var $uname = "";
	var $diffrepository = null;
	var $accept_success = false;
	var $canedit = false;

	/**
	* constructor
	*/
	function RepositoryRootController() {
		parent::BaseController();
	}

	/**
	* initialize the controller
	*/
	function init() {
		$this->rid = safe_GET_xref('rid');

		$repositoryrec = find_other_record($this->rid, WT_GED_ID);

		if (find_updated_record($this->rid, WT_GED_ID)!==null) {
			$repositoryrec = "0 @".$this->rid."@ REPO\n";
		} else if (!$repositoryrec) {
			return false;
		}

		$this->repository = new Repository($repositoryrec);
		$this->repository->ged_id=WT_GED_ID; // This record is from a file

		if (!$this->repository->canDisplayDetails()) {
			print_header(i18n::translate('Private')." ".i18n::translate('Repository information'));
			print_privacy_error();
			print_footer();
			exit;
		}

		$this->uname = WT_USER_NAME;

		//-- perform the desired action
		switch($this->action) {
			case "addfav":
				$this->addFavorite();
				break;
			case "accept":
				if (WT_USER_CAN_ACCEPT) {
					accept_all_changes($this->rid, WT_GED_ID);
					$this->show_changes=false;
					$this->accept_success=true;
					$indirec = find_other_record($this->rid, WT_GED_ID);
					//-- check if we just deleted the record and redirect to index
					if (empty($indirec)) {
						header("Location: index.php?ctype=gedcom");
						exit;
					}
					$this->repository = new Repository($indirec);
				}
				break;
			case "undo":
				if (WT_USER_CAN_ACCEPT) {
					reject_all_changes($this->rid, WT_GED_ID);
					$this->show_changes=false;
					$this->accept_success=true;
					$indirec = find_other_record($this->rid, WT_GED_ID);
					//-- check if we just deleted the record and redirect to index
					if (empty($indirec)) {
						header("Location: index.php?ctype=gedcom");
						exit;
					}
					$this->repository = new Repository($indirec);
				}
				break;
		}

		//-- check for the user
		//-- if the user can edit and there are changes then get the new changes
		if ($this->show_changes && WT_USER_CAN_EDIT && ($newrec = find_updated_record($this->rid, WT_GED_ID))!==null) {
			$this->diffrepository = new Repository($newrec);
			$this->diffrepository->setChanged(true);
			$repositoryrec = $newrec;
		}

		if ($this->repository->canDisplayDetails()) {
			$this->canedit = WT_USER_CAN_EDIT;
		}

		if ($this->show_changes && $this->canedit) {
			$this->repository->diffMerge($this->diffrepository);
		}
	}

	/**
	* Add a new favorite for the action user
	*/
	function addFavorite() {
		global $GEDCOM;
		if (empty($this->uname)) return;
		if (!empty($_REQUEST["gid"])) {
			$gid = strtoupper($_REQUEST["gid"]);
			$indirec = find_other_record($gid, WT_GED_ID);
			if ($indirec) {
				$favorite = array();
				$favorite["username"] = $this->uname;
				$favorite["gid"] = $gid;
				$favorite["type"] = "REPO";
				$favorite["file"] = $GEDCOM;
				$favorite["url"] = "";
				$favorite["note"] = "";
				$favorite["title"] = "";
				addFavorite($favorite);
			}
		}
	}

	/**
	* get the title for this page
	* @return string
	*/
	function getPageTitle() {
		if ($this->repository) {
			return $this->repository->getFullName()." - ".$this->rid." - ".i18n::translate('Repository information');
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
	* get edit menut
	* @return Menu
	*/
	function &getEditMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $GEDCOM;
		global $SHOW_GEDCOM_RECORD;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl";
		else $ff="";

		if (!$this->userCanEdit()) {
			$tempvar = false;
			return $tempvar;
		}

		// edit repository menu
		$menu = new Menu(i18n::translate('Edit Repository'));
		if ($SHOW_GEDCOM_RECORD || WT_USER_IS_ADMIN)
			$menu->addOnclick('return edit_raw(\''.$this->rid.'\');');
		if (!empty($WT_IMAGES["edit_repo"]["small"]))
			$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");

		// edit repository / edit_raw
		if ($SHOW_GEDCOM_RECORD || WT_USER_IS_ADMIN) {
			$submenu = new Menu(i18n::translate('Edit raw GEDCOM record'));
			$submenu->addOnclick("return edit_raw('".$this->rid."');");
			if (!empty($WT_IMAGES["edit_repo"]["small"]))
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
			$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
			$menu->addSubmenu($submenu);
		}

		// edit repository / delete_repository
		$submenu = new Menu(i18n::translate('Delete repository'));
		$submenu->addOnclick("if (confirm('".i18n::translate('Are you sure you want to delete this Repository?')."')) return deleterepository('".$this->rid."'); else return false;");
		if (!empty($WT_IMAGES["edit_repo"]["small"]))
			$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
		$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
		$menu->addSubmenu($submenu);

		if (find_updated_record($this->rid, WT_GED_ID)!==null) {
			// edit_repo / separator
			$submenu = new Menu();
			$submenu->isSeparator();
			$menu->addSubmenu($submenu);

			// edit_repo / show/hide changes
			if (!$this->show_changes)
			{
				$submenu = new Menu(i18n::translate('This record has been updated.  Click here to show changes.'), encode_url("repo.php?rid={$this->rid}&show_changes=yes"));
				if (!empty($WT_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
			else
			{
				$submenu = new Menu(i18n::translate('Click here to hide changes.'), encode_url("repo.php?rid={$this->rid}&show_changes=no"));
				if (!empty($WT_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}

			if (WT_USER_CAN_ACCEPT)
			{
				// edit_repository / accept_all
				$submenu = new Menu(i18n::translate('Undo all changes'), encode_url("repo.php?rid={$this->rid}&action=undo"));
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				if (!empty($WT_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
				$menu->addSubmenu($submenu);
				$submenu = new Menu(i18n::translate('Accept all changes'), encode_url("repo.php?rid={$this->rid}&action=accept"));
				if (!empty($WT_IMAGES["edit_repo"]["small"]))
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['edit_repo']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
			}
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

		if (!$this->repository->canDisplayDetails() || (!$SHOW_GEDCOM_RECORD && $ENABLE_CLIPPINGS_CART < WT_USER_ACCESS_LEVEL)) {
			$tempvar = false;
			return $tempvar;
		}

			// other menu
		$menu = new Menu(i18n::translate('Other'));
		$menu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}", "submenu{$ff}");
		if ($SHOW_GEDCOM_RECORD)
		{
			$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
			if ($this->show_changes && $this->userCanEdit())
			{
				$menu->addLink("javascript:show_gedcom_record('new');");
			}
			else
			{
				$menu->addLink("javascript:show_gedcom_record();");
			}
		}
		else
		{
			if (!empty($WT_IMAGES["clippings"]["small"]))
				$menu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['clippings']['small']}");
			$menu->addLink(encode_url("clippings.php?action=add&id={$this->rid}&type=repo"));
		}
		if ($SHOW_GEDCOM_RECORD)
		{
				// other / view_gedcom
				$submenu = new Menu(i18n::translate('View GEDCOM Record'));
				if ($this->show_changes && $this->userCanEdit())
				{
					$submenu->addLink("javascript:show_gedcom_record('new');");
				}
				else
				{
					$submenu->addLink("javascript:show_gedcom_record();");
				}
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($ENABLE_CLIPPINGS_CART >= WT_USER_ACCESS_LEVEL)
		{
				// other / add_to_cart
				$submenu = new Menu(i18n::translate('Add to Clippings Cart'), encode_url("clippings.php?action=add&id={$this->rid}&type=repo"));
				if (!empty($WT_IMAGES["clippings"]["small"]))
					$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['clippings']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		if ($this->repository->canDisplayDetails() && !empty($this->uname))
		{
				// other / add_to_my_favorites
				$submenu = new Menu(i18n::translate('Add to My Favorites'), encode_url("repo.php?action=addfav&rid={$this->rid}&gid={$this->rid}"));
				$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['gedcom']['small']}");
				$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
				$menu->addSubmenu($submenu);
		}
		return $menu;
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/repository_ctrl_user.php')) {
	require_once WT_ROOT.'includes/controllers/repository_ctrl_user.php';
} else {
	class RepositoryController extends RepositoryControllerRoot
	{
	}
}

?>
