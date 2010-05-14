<?php
/**
* System for generating menus.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
*
* Modifications Copyright (c) 2010 Greg Roach
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*
* @package webtrees
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_MENUBAR_PHP', '');

require_once WT_ROOT.'includes/classes/class_menu.php';
require_once 'includes/classes/class_module.php';

class MenuBar
{
	/**
	* get the home menu
	* @return Menu the menu item
	*/
	static function getHomeMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $HOME_SITE_URL, $HOME_SITE_TEXT;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		//-- main home menu item
		$menu = new Menu($HOME_SITE_TEXT, $HOME_SITE_URL, "down");
		if (!empty($WT_IMAGES["home"]["large"]))
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["home"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_home");
		return $menu;
	}

	/**
	* get the menu with links to the gedcom portals
	* @return Menu the menu item
	*/
	static function getGedcomMenu() {
		global $ALLOW_CHANGE_GEDCOM, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;

		if ($TEXT_DIRECTION=='rtl') $ff='_rtl'; else $ff='';
		//-- main menu
		$menu = new Menu(i18n::translate('Home page'), 'index.php?ctype=gedcom', 'down');
		if (!empty($WT_IMAGES['gedcom']['large']))
			$menu->addIcon($WT_IMAGE_DIR.'/'.$WT_IMAGES['gedcom']['large']);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", 'icon_large_gedcom');
		//-- gedcom list
		$gedcom_titles=get_gedcom_titles();
		if ($ALLOW_CHANGE_GEDCOM && count($gedcom_titles)>1) {
			foreach ($gedcom_titles as $gedcom_title) {
				$submenu = new Menu(PrintReady($gedcom_title->gedcom_title, true), encode_url('index.php?ctype=gedcom&ged='.$gedcom_title->gedcom_name));
				if (!empty($WT_IMAGES['gedcom']['small'])) {
					$submenu->addIcon($WT_IMAGE_DIR.'/'.$WT_IMAGES['gedcom']['small']);
				}
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_gedcom');
				$menu->addSubmenu($submenu);
			}
		}
		//-- Welcome Menu customization
		$filename = WT_ROOT.'includes/extras/custom_welcome_menu.php';
		if (file_exists($filename)) {
			require $filename;
		}

		return $menu;
	}

	/**
	* get the mygedview menu
	* @return Menu the menu item
	*/
	static function getMygedviewMenu() {
		global $MEDIA_DIRECTORY, $MULTI_MEDIA;
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		if (!WT_USER_ID) {
			return new Menu('', '', '');
		}

		//-- main menu
		$menu = new Menu(i18n::translate('My Page'), "index.php?ctype=user", "down");
		if (!empty($WT_IMAGES["mygedview"]["large"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["large"]);
		} elseif (!empty($WT_IMAGES["gedcom"]["large"])) {
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["gedcom"]["large"]);
		}
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_mygedview");

		//-- mygedview submenu
		$submenu = new Menu(i18n::translate('My Page'), "index.php?ctype=user");
		if (!empty($WT_IMAGES["mygedview"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mygedview");
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$submenu = new Menu(i18n::translate('My account'), "edituser.php");
			if (!empty($WT_IMAGES["mygedview"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mygedview");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new Menu(i18n::translate('My Pedigree'), encode_url("pedigree.php?rootid=".WT_USER_GEDCOM_ID."&show_full={$showFull}&talloffset={$showLayout}"));
			if (!empty($WT_IMAGES["pedigree"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["small"]);
			//$submenu->addIcon($WT_IMAGE_DIR."/small/pedigree.gif");
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new Menu(i18n::translate('My Individual Record'), "individual.php?pid=".WT_USER_GEDCOM_ID);
			if (!empty($WT_IMAGES["indis"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_indis");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN){
			$menu->addSeparator();
			//-- admin submenu
			$submenu = new Menu(i18n::translate('Admin'), "admin.php");
			if (!empty($WT_IMAGES["admin"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["admin"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- manage_gedcoms submenu
			$submenu = new Menu(i18n::translate('GEDCOM administration'), "editgedcoms.php");
			if (!empty($WT_IMAGES["admin"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["admin"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- user_admin submenu
			if (WT_USER_IS_ADMIN) {
				$submenu = new Menu(i18n::translate('User administration'), "useradmin.php");
				if (!empty($WT_IMAGES["admin"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["admin"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
				$menu->addSubmenu($submenu);
				//-- manage_media submenu
				if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
					$submenu = new Menu(i18n::translate('Manage multimedia'), "media.php");
					if (!empty($WT_IMAGES["menu_media"]["small"]))
						$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_media"]["small"]);
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_media");
					$menu->addSubmenu($submenu);
				}
			}
		}
		if (WT_USER_CAN_EDIT) {
			//-- upload_media submenu
			if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
				$menu->addSeparator();
				$submenu = new Menu(i18n::translate('Upload media files'), "uploadmedia.php");
				if (!empty($WT_IMAGES["menu_media"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_media"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_media");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the menu for the charts
	* @return Menu the menu item
	*/
	static function getChartsMenu($rootid='') {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;
		global $controller;
		
		$style = "top";
		if ($rootid) $style = "sub";
		if (isset($controller)) {
			if (!$rootid) {
				if (isset($controller->pid)) $rootid = $controller->pid;
				if (isset($controller->rootid)) $rootid = $controller->rootid;
			}
		}

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
			return $menu;
		}

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		//-- main charts menu item
		$link = "pedigree.php?ged=".WT_GEDCOM."&show_full={$showFull}&talloffset={$showLayout}";
		if ($rootid) $link .= "&rootid={$rootid}";
		if ($style=="sub") {
			$menu = new Menu(i18n::translate('Charts'), encode_url($link));
			if (!empty($WT_IMAGES["pedigree"]["small"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "", "icon_small_pedigree");
		}
		else {
			// top menubar
			$menu = new Menu(i18n::translate('Charts'), encode_url($link), "down");
			if (!empty($WT_IMAGES["pedigree"]["large"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_pedigree");
		}

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["pedigree"] = i18n::translate('Pedigree Chart');
		if (file_exists(WT_ROOT.'descendancy.php')) $menuList["descendancy"] = i18n::translate('Descendancy chart');
		if (file_exists(WT_ROOT.'ancestry.php')) $menuList["ancestry"] = i18n::translate('Ancestry chart');
		if (file_exists(WT_ROOT.'compact.php')) $menuList["compact"] = i18n::translate('Compact Chart');
		if (file_exists(WT_ROOT.'fanchart.php') && function_exists("imagettftext")) $menuList["fanchart"] = i18n::translate('Circle diagram');
		if (file_exists(WT_ROOT.'hourglass.php')) $menuList["hourglass"] = i18n::translate('Hourglass chart');
		if (file_exists(WT_ROOT.'familybook.php')) $menuList["familybook"] = i18n::translate('Family book chart');
		if (file_exists(WT_ROOT.'timeline.php')) $menuList["timeline"] = i18n::translate('Timeline chart');
		if (file_exists(WT_ROOT.'lifespan.php')) $menuList["lifespan"] = i18n::translate('Lifespan chart');
		if (file_exists(WT_ROOT.'relationship.php')) $menuList["relationship"] = i18n::translate('Relationship Chart');
		if (file_exists(WT_ROOT.'statistics.php')) $menuList["statistics"] = i18n::translate('Statistics');
		if (file_exists(WT_ROOT.'treenav.php')) $menuList["treenav"] = i18n::translate('Interactive tree');
		if (file_exists(WT_ROOT.'modules/googlemap/pedigree_map.php')) {
			$menuList["pedigree_map"] = i18n::translate('Pedigree Map');//added for pedigree_map
		}
		asort($menuList);

		// Produce the submenus in localized name order
		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "pedigree":
				//-- pedigree
				$link = "pedigree.php?ged=".WT_GEDCOM."&show_full={$showFull}&talloffset={$showLayout}";
				if ($rootid) $link .= "&rootid={$rootid}";
				$submenu = new Menu(i18n::translate('Pedigree Chart'), encode_url($link));
				if (!empty($WT_IMAGES["pedigree"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["pedigree"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
				$menu->addSubmenu($submenu);
				break;

			case "descendancy":
				//-- descendancy
				$link = "descendancy.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Descendancy chart'), encode_url($link));
				if (!empty($WT_IMAGES["descendant"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["descendant"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_descendant");
				$menu->addSubmenu($submenu);
				break;

			case "ancestry":
				//-- ancestry
				$link = "ancestry.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&rootid={$rootid}&show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Ancestry chart'), encode_url($link));
				if (!empty($WT_IMAGES["ancestry"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["ancestry"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "compact":
				//-- compact
				$link = "compact.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Compact Chart'), encode_url($link));
				if (!empty($WT_IMAGES["ancestry"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["ancestry"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "fanchart":
				//-- fan chart
				$link = "fanchart.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Circle diagram'), encode_url($link));
				if (!empty($WT_IMAGES["fanchart"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["fanchart"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fanchart");
				$menu->addSubmenu($submenu);
				break;

			case "hourglass":
				//-- hourglass
				$link = "hourglass.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Hourglass chart'), encode_url($link));
				if (!empty($WT_IMAGES["hourglass"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["hourglass"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_hourglass");
				$menu->addSubmenu($submenu);
				break;

			case "familybook":
				//-- familybook
				$link = "familybook.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&pid={$rootid}&show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Family book chart'), encode_url($link));
				if (!empty($WT_IMAGES["fambook"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["fambook"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fambook");
				$menu->addSubmenu($submenu);
				break;

			case "timeline":
				//-- timeline
				$link = "timeline.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;pids[]=".$rootid;
				$submenu = new Menu(i18n::translate('Timeline chart'), $link);
				if (!empty($WT_IMAGES["timeline"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["timeline"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_timeline");
				$menu->addSubmenu($submenu);
				if (isset($controller) && !empty($controller->famid)) {
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
							$submenu = new Menu(i18n::translate('Show couple on timeline chart'), encode_url('timeline.php?pids[0]='.$controller->getHusband().'&pids[1]='.$controller->getWife()));
							if (!empty($WT_IMAGES["timeline"]["small"])) {
								$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;
			
						case "childTimeLine":
							// charts / children_timeline
							$submenu = new Menu(i18n::translate('Show children on timeline chart'), encode_url('timeline.php?'.$controller->getChildrenUrlTimeline()));
							if (!empty($WT_IMAGES["timeline"]["small"])) {
								$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;
			
						case "familyTimeLine":
							// charts / family_timeline
							$submenu = new Menu(i18n::translate('Show family on timeline chart'), encode_url('timeline.php?pids[0]='.$controller->getHusband().'&pids[1]='.$controller->getWife().'&'.$controller->getChildrenUrlTimeline(2)));
							if (!empty($WT_IMAGES["timeline"]["small"])) {
								$submenu->addIcon("{$WT_IMAGE_DIR}/{$WT_IMAGES['timeline']['small']}");
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;
			
						}
					}
				}
				
				break;

			case "lifespan":
				//-- lifespan
				$link = "lifespan.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&pids[]={$rootid}&addFamily=1";
				$submenu = new Menu(i18n::translate('Lifespan chart'), encode_url($link));
				if (!empty($WT_IMAGES["timeline"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["timeline"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_timeline");
				$menu->addSubmenu($submenu);
				break;

			case "relationship":
				if ($rootid) {
					// Pages focused on a specific person
					$from=array($rootid);
					$to=array('', WT_USER_GEDCOM_ID, WT_USER_ROOT_ID);
					if (WT_USER_ID) {
						foreach (getUserFavorites(WT_USER_NAME) as $favorite) {
							// An indi in this gedcom?
							if ($favorite['type']=='INDI' && $favorite['file']==WT_GEDCOM) {
								$to[]=$favorite['gid'];
							}
						}
					}
				} else {
					// Regular pages
					$from=array(WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID);
					$to=array('');
				}
				foreach (array_unique($from) as $pid1) {
					foreach (array_unique($to) as $pid2) {
						if ($pid1!=$pid2 || $pid1=='' || $pid2=='') {
							$person=Person::getInstance($pid2);
							if ($person instanceof Person) {
								$submenu = new Menu(
									i18n::translate('Relationship Chart').': '.PrintReady($person->getFullName()),
									encode_url("relationship.php?pid1={$pid2}&pid2={$pid1}&pretty=2&followspouse=1&ged=".WT_GEDCOM)
								);
							} else {
								$submenu = new Menu(
									i18n::translate('Relationship Chart'),
									encode_url("relationship.php?pid1={$pid1}&pretty=2&followspouse=1&ged=".WT_GEDCOM)
								);
							}
							if (!empty($WT_IMAGES["relationship"]["small"])) {
								$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["relationship"]["small"]);
							}
							$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_relationship");
							$menu->addSubmenu($submenu);
						}
					}
				}
				break;

			case "statistics":
				//-- statistics plot
				$submenu = new Menu(i18n::translate('Statistics'), encode_url("statistics.php?ged=".WT_GEDCOM));
				if (!empty($WT_IMAGES["statistic"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["statistic"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_statistic");
				$menu->addSubmenu($submenu);
				break;

			case "treenav":
				//-- interactive tree
				$link = "treenav.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Interactive tree'), encode_url($link));
				if (!empty($WT_IMAGES["tree"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["tree"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_gedcom");
				$menu->addSubmenu($submenu);
				break;

			//added for pedigree_map
			case "pedigree_map":
				//-- pedigree map
				$link = "module.php?ged=".WT_GEDCOM."&mod=googlemap&mod_action=pedigree_map";
				if ($rootid) $link .= "&rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Pedigree Map'), encode_url($link));
				$submenu->addIcon('modules/googlemap/images/pedigree_map.gif');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff");
				$menu->addSubmenu($submenu);
				break;
			//end of added for pedigree_map
			}
		}
		return $menu;
	}

	/**
	* get the menu for the lists
	* @return Menu the menu item
	*/
	static function getListsMenu($surname="") {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;
		global $SHOW_SOURCES, $MULTI_MEDIA, $SEARCH_SPIDER;
		global $ALLOW_CHANGE_GEDCOM;
		global $controller;
		
		$style = "top";
		if ($surname) $style = "sub";
		if (isset($controller)) {
			if (!$surname) {
				if (isset($controller->indi)) {
					list($surname)=explode(',', $controller->indi->getSortName());
				}
				if (isset($controller->rootid)) {
					$person = Person::getInstance($controller->rootid);
					list($surname)=explode(',', $person->getSortName());
				}
			}
		}
		
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if (!empty($SEARCH_SPIDER)) { // Only want the indi list for search engines.
			//-- main lists menu item
			$link = "indilist.php?ged=".WT_GEDCOM;
			if ($style=="sub") {
				$link .= "&surname={$surname}";
				$menu = new Menu(i18n::translate('Lists'), encode_url($link));
				if (!empty($WT_IMAGES["indis"]["small"]))
					$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]);
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_small_indis");
			}
			else {
				$menu = new Menu(i18n::translate('Lists'), encode_url($link), "down");
				if (!empty($WT_IMAGES["indis"]["large"]))
					$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["large"]);
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_indis");
			}

			//-- gedcom list
			if ($ALLOW_CHANGE_GEDCOM) {
				foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
					$submenu = new Menu(i18n::translate('Individuals')." - ".PrintReady(get_gedcom_setting($ged_id, 'title')), encode_url('indilist.php?ged='.$gedcom));
					if (!empty($WT_IMAGES["gedcom"]["small"]))
						$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["gedcom"]["small"]);
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_gedcom");
					$menu->addSubmenu($submenu);
				}
			}
			return $menu;
		}
		//-- main lists menu item
		$link = "indilist.php?ged=".WT_GEDCOM;
		if ($style=="sub") {
			$link .= "&surname=".$surname;
			$menu = new Menu(i18n::translate('Lists'), encode_url($link));
			if (!empty($WT_IMAGES["indis"]["small"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_indis");
		}
		else {
			$menu = new Menu(i18n::translate('Lists'), $link, "down");
			if (!empty($WT_IMAGES["indis"]["large"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_indis");
		}

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["individual"] = i18n::translate('Individuals');
		if (file_exists(WT_ROOT.'famlist.php')) $menuList["family"] = i18n::translate('Families');
		if (file_exists(WT_ROOT.'branches.php')) $menuList["branches"] = i18n::translate('Branches');
		if ($style=="top" && file_exists(WT_ROOT.'sourcelist.php') && $SHOW_SOURCES>=WT_USER_ACCESS_LEVEL) $menuList["source"] = i18n::translate('Sources');
		if ($style=="top" && file_exists(WT_ROOT.'notelist.php') && $SHOW_SOURCES>=WT_USER_ACCESS_LEVEL) $menuList["note"] = i18n::translate('Shared Notes');
		if ($style=="top" && file_exists(WT_ROOT.'repolist.php')) $menuList["repository"] = i18n::translate('Repositories');
		if ($style=="top" && file_exists(WT_ROOT.'placelist.php')) $menuList["places"] = i18n::translate('Place hierarchy');
		if ($style=="top" && file_exists(WT_ROOT.'medialist.php') && $MULTI_MEDIA) $menuList["media"] = i18n::translate('Multimedia');
		asort($menuList);

		// Produce the submenus in localized name order

		foreach($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "individual":
				//-- indi list sub menu
				$link = "indilist.php?ged=".WT_GEDCOM;
				if ($surname) $link .= "&surname=".$surname;
				$submenu = new Menu(i18n::translate('Individuals'), encode_url($link));
				if (!empty($WT_IMAGES["indis"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_indis");
				$menu->addSubmenu($submenu);
				break;

			case "family":
				//-- famlist sub menu
				$link = "famlist.php?ged=".WT_GEDCOM;
				if ($surname) $link .= "&amp;surname=".$surname;
				$submenu = new Menu(i18n::translate('Families'), $link);
				if (!empty($WT_IMAGES["cfamily"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["cfamily"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_cfamily");
				$menu->addSubmenu($submenu);
				break;

			case "branches":
				//-- branches sub menu
				$link = "branches.php?ged=".WT_GEDCOM;
				if ($surname) $link .= "&amp;surn=".$surname;
				$submenu = new Menu(i18n::translate('Branches'), $link);
				if (!empty($WT_IMAGES["patriarch"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["patriarch"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_patriarch");
				$menu->addSubmenu($submenu);
				break;

			case "source":
				//-- source
				$submenu = new Menu(i18n::translate('Sources'), encode_url('sourcelist.php?ged='.WT_GEDCOM));
				if (!empty($WT_IMAGES["menu_source"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_source"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_source");
				$menu->addSubmenu($submenu);
				break;

			case "note":
				//-- shared note
				$submenu = new Menu(i18n::translate('Shared Notes'), encode_url('notelist.php?ged='.WT_GEDCOM));
				if (!empty($WT_IMAGES["menu_note"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_note"]["small"]);
				else if (!empty($WT_IMAGES["notes"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["notes"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_notes");
				$menu->addSubmenu($submenu);
				break;

			case "repository":
				//-- repository
				$submenu = new Menu(i18n::translate('Repositories'), "repolist.php");
				if (!empty($WT_IMAGES["menu_repository"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_repository"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_repository");
				$menu->addSubmenu($submenu);
				break;

			case "places":
				//-- places
				$submenu = new Menu(i18n::translate('Place hierarchy'), encode_url('placelist.php?ged='.WT_GEDCOM));
				if (!empty($WT_IMAGES["place"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["place"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_place");
				$menu->addSubmenu($submenu);
				break;

			case "media":
				//-- medialist
				$submenu = new Menu(i18n::translate('Multimedia'), encode_url('medialist.php?ged='.WT_GEDCOM));
				if (!empty($WT_IMAGES["menu_media"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_media"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_media");
				$menu->addSubmenu($submenu);
				break;
			}
		}

		return $menu;
	}

	/**
	* get the menu for the calendar
	* @return Menu the menu item
	*/
	static function getCalendarMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'calendar.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
			}
		//-- main calendar menu item
		$menu = new Menu(i18n::translate('Anniversary calendar'), encode_url('calendar.php?ged='.WT_GEDCOM), "down");
		if (!empty($WT_IMAGES["calendar"]["large"]))
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["calendar"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_calendar");
		//-- viewday sub menu
		$submenu = new Menu(i18n::translate('View Day'), encode_url('calendar.php?ged='.WT_GEDCOM));
		if (!empty($WT_IMAGES["calendar"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new Menu(i18n::translate('View Month'), encode_url("calendar.php?ged=".WT_GEDCOM."&action=calendar"));
		if (!empty($WT_IMAGES["calendar"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new Menu(i18n::translate('View Year'), encode_url("calendar.php?ged=".WT_GEDCOM."&action=year"));
		if (!empty($WT_IMAGES["calendar"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["calendar"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return Menu the menu item
	*/
	static function getReportsMenu($pid="", $famid="") {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;
		global $SEARCH_SPIDER;
		global $controller;

		$style = "top";
		if ($pid || $famid) $style = "sub";
		if (isset($controller)) {
			if (!$pid) {
				if (isset($controller->pid)) $pid = $controller->pid;
				if (isset($controller->rootid)) $pid = $controller->rootid;
			}
			if (!$famid) {
				if (isset($controller->famid)) $famid = $controller->famid;
			}
		}
		
		
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'reportengine.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
			}

		//-- main reports menu item
		if ($style=="sub") {
			$menu = new Menu(i18n::translate('Reports'), "#");
			if (!empty($WT_IMAGES["reports"]["small"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["reports"]["small"]);
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_reports");
		}
		else {
			// top menubar
			$menu = new Menu(i18n::translate('Reports'), encode_url('reportengine.php?ged='.WT_GEDCOM), "down");
			if (!empty($WT_IMAGES["reports"]["large"]))
				$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["reports"]["large"]);
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_reports");
		}

		foreach (WT_Module::getActiveReports() as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the optional site-specific menu
	* @return Menu the menu item
	*/
	static function getOptionalMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!file_exists(WT_ROOT.'includes/extras/optional_menu.php') || !empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
		}
		require WT_ROOT.'includes/extras/optional_menu.php';
		return $menu;
	}

	/**
	* get the print_preview menu
	* @return Menu the menu item
	*/
	static function getPreviewMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $QUERY_STRING, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
			}
		//-- main print_preview menu item
		$menu = new Menu(i18n::translate('Printer-friendly version'), WT_SCRIPT_NAME.normalize_query_string($QUERY_STRING."&amp;view=preview"), "down");
		if (!empty($WT_IMAGES["printer"]["large"]))
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["printer"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_printer");
		return $menu;
	}

	/**
	* get the search menu
	* @return Menu the menu item
	*/
	static function getSearchMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;
		global $SHOW_MULTISITE_SEARCH, $SEARCH_SPIDER;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'search.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
			}
		//-- main search menu item
		$menu = new Menu(i18n::translate('Search'), encode_url('search.php?ged='.WT_GEDCOM), "down");
		if (!empty($WT_IMAGES["search"]["large"]))
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["large"]);
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_search");
		//-- search_general sub menu
		$submenu = new Menu(i18n::translate('General Search'), encode_url("search.php?ged=".WT_GEDCOM."&action=general"));
		if (!empty($WT_IMAGES["search"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new Menu(i18n::translate('Soundex Search'), encode_url("search.php?ged=".WT_GEDCOM."&action=soundex"));
		if (!empty($WT_IMAGES["search"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new Menu(i18n::translate('Advanced search'), encode_url("search_advanced.php?ged=".WT_GEDCOM));
		if (!empty($WT_IMAGES["search"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(i18n::translate('Search and replace'), encode_url("search.php?ged=".WT_GEDCOM."&action=replace"));
			if (!empty($WT_IMAGES["search"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
			$menu->addSubmenu($submenu);
		}

		//-- search_multisite sub menu
		if ($SHOW_MULTISITE_SEARCH >= WT_USER_ACCESS_LEVEL) {
			$sitelist = get_server_list();
			if (count($sitelist)>0) {
				$submenu = new Menu(i18n::translate('Multi Site Search'), encode_url("search.php?ged=".WT_GEDCOM."&action=multisite"));
				if (!empty($WT_IMAGES["search"]["small"]))
					$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["search"]["small"]);
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get an array of module menu objects
	* @return array
	*/
	static function getModuleMenus() {
		$menus=array();
		foreach (WT_Module::getActiveMenus() as $module) {
			$menu=$module->getMenu();
			if ($menu) {
				$menus[] = $menu;
			}
		}
		return $menus;
	}

	/**
	* get the help menu
	* @return Menu the menu item
	*/
	static function getHelpMenu() {
		global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $SEARCH_SPIDER;
		global $SHOW_CONTEXT_HELP, $QUERY_STRING, $helpindex, $action;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			$menu = new Menu("", "", "");
//			$menu->print_menu = null;
			return $menu;
			}
		//-- main help menu item
		$menu = new Menu(i18n::translate('Help'), "#", "down");
		if (!empty($WT_IMAGES["help"]["large"]))
			$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["help"]["large"]);
		if (empty($helpindex))
			$menu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$menu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_help");

		//-- help_for_this_page sub menu
		$submenu = new Menu(i18n::translate('Help with this page'), "#");
		if (!empty($WT_IMAGES["menu_help"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_help"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		if (empty($helpindex))
			$submenu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$submenu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addSubmenu($submenu);
		//-- help_contents sub menu
		$submenu = new Menu(i18n::translate('Help Contents'), "#");
		if (!empty($WT_IMAGES["menu_help"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_help"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$submenu->addOnclick("return helpPopup('help_contents_help');");
		$menu->addSubmenu($submenu);
		//-- faq sub menu
		if (file_exists(WT_ROOT.'faq.php')) {
			$submenu = new Menu(i18n::translate('FAQ list'), "faq.php");
			if (!empty($WT_IMAGES["menu_help"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["menu_help"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
			$menu->addSubmenu($submenu);
		}
		//-- add wiki links
		$menu->addSeparator();
		$submenu = new Menu(i18n::translate('Wiki Main Page'), WT_WEBTREES_WIKI.'" target="_blank');
		if (!empty($WT_IMAGES["wiki"]["small"]))
			$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["wiki"]["small"]);
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_wiki");
		$menu->addSubmenu($submenu);
// These two sub menu items temporarily removed as the WIKI pages do not exist
//		$submenu = new Menu(i18n::translate('Wiki User\'s Guide'), WT_WEBTREES_WIKI.'/en/index.php?title=Users_Guide" target="_blank');
//		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_wiki");
//		$menu->addSubmenu($submenu);
//
		if (WT_USER_GEDCOM_ADMIN) {
//			$submenu = new Menu(i18n::translate('Wiki Administrator\'s Guide'), WT_WEBTREES_WIKI.'/en/index.php?title=Administrators_Guide" target="_blank');
//			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_wiki");
//			$menu->addSubmenu($submenu);
		}

		//-- add contact links to help menu
		$menu->addSeparator();
		$menuitems = contact_menus();
		foreach($menuitems as $menuitem) {
			$submenu = new Menu($menuitem["label"], $menuitem["link"]);
			if (!empty($WT_IMAGES["mygedview"]["small"]))
				$submenu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES["mygedview"]["small"]);
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_contact");
			if (!empty($menuitem["onclick"])) $submenu->addOnclick($menuitem["onclick"]);
			$menu->addSubmenu($submenu);
		}
		//-- add show/hide context_help
		$menu->addSeparator();
		if ($_SESSION["show_context_help"])
			$submenu = new Menu(i18n::translate('Hide contextual help'), WT_SCRIPT_NAME.normalize_query_string($QUERY_STRING."&amp;show_context_help=no"));
		else
			$submenu = new Menu(i18n::translate('Show contextual help'), WT_SCRIPT_NAME.normalize_query_string($QUERY_STRING."&amp;show_context_help=yes"));
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the menu with links change to each theme
	* @return Menu the menu item
	*/
	static function getThemeMenu() {
		global $SEARCH_SPIDER, $ALLOW_THEME_DROPDOWN, $ALLOW_USER_THEMES, $THEME_DIR;

		$current=get_user_setting(WT_USER_ID, 'theme');
		$all_themes=get_theme_names();
		if (!array_key_exists($current, $all_themes)) {
			$current=$THEME_DIR;		
		}

		if ($ALLOW_THEME_DROPDOWN && $ALLOW_USER_THEMES && !$SEARCH_SPIDER) {
			isset($_SERVER["QUERY_STRING"]) == true?$tqstring = "?".$_SERVER["QUERY_STRING"]:$tqstring = "";
			$frompage = WT_SCRIPT_NAME.decode_url($tqstring);
			if (isset($_REQUEST['mod'])) {
				if (!strstr($frompage, "?")) {
					if (!strstr($frompage, "%3F")) ;
					else $frompage .= "?";
				}
				if (!strstr($frompage, "&mod") || !strstr($frompage, "?mod")) $frompage .= "&mod=".$_REQUEST['mod'];
			}
			if (substr($frompage,-1) == "?") $frompage = substr($frompage,0,-1);
			if (substr($frompage,-1) == "&") $frompage = substr($frompage,0,-1);
			// encode frompage address in other case we lost the all variables on theme change
			$frompage = base64_encode($frompage);
			$menu=new Menu(i18n::translate('Change theme'));
			$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
			foreach ($all_themes as $themename=>$themedir) {
				$submenu=new Menu($themename, encode_url("themechange.php?frompage={$frompage}&mytheme={$themedir}"));
				if ($themedir==$current) {
					$submenu->addClass('favsubmenuitem_selected', 'favsubmenuitem_hover');
				} else {
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				}
				$menu->addSubMenu($submenu);
			}
			return $menu;
		} else {
			return new Menu('', '');
		}
	}
	/**
	* get the menu with links change to each color themes subcolor type
	* @return Menu the menu item
	*/
	static function getColorMenu($COLOR_THEME_LIST) {
		isset($_SERVER["QUERY_STRING"]) == true?$tqstring = "?".$_SERVER["QUERY_STRING"]:$tqstring = "";
		$frompage = WT_SCRIPT_NAME.decode_url($tqstring);
		if (isset($_REQUEST['mod'])) {
			if (!strstr($frompage, "?")) {
				if (!strstr($frompage, "%3F")) ;
				else $frompage .= "?";
			}
			if (!strstr($frompage, "&mod") || !strstr($frompage, "?mod")) $frompage .= "&mod=".$_REQUEST['mod'];
		}
		if (substr($frompage,-1) == "?") $frompage = substr($frompage,0,-1);
		if (substr($frompage,-1) == "&") $frompage = substr($frompage,0,-1);
		// encode frompage address in other case we lost the all variables on theme change
		$frompage = base64_encode($frompage);
		$menu=new Menu(i18n::translate('Color Palette'));
		$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
		foreach ($COLOR_THEME_LIST as $colorChoice=>$colorName) {
			//$submenu=new Menu($colorName, encode_url("colorchange.php?frompage={$frompage}&mycolor={$colorChoice}"));
			$submenu=new Menu($colorName, encode_url(WT_SCRIPT_NAME.'?'.$_SERVER['QUERY_STRING']."&themecolor={$colorChoice}"));
			$menu->addSubMenu($submenu);
		}
		return $menu;
	}
	/**
	* get the menu with links to change language
	* @return Menu the menu item
	*/
	static function getLanguageMenu() {
		global $QUERY_STRING, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new Menu(i18n::translate('Change language'), '#', 'down');
		$menu->addClass("langmenuitem$ff", "langmenuitem_hover$ff", "submenu$ff");

		foreach (i18n::installed_languages() as $lang=>$name) {
			$submenu=new Menu($name, WT_SCRIPT_NAME.normalize_query_string($QUERY_STRING.'&amp;lang='.$lang));
			if ($lang==WT_LOCALE) {
				$submenu->addClass('favsubmenuitem_selected', 'favsubmenuitem_hover');
			} else {
				$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
			}
			$menu->addSubMenu($submenu);
		}
		if (count($menu->submenus)>1) {
			return $menu;
		} else {
			return new Menu('', '');
		}
	}
	/**
	* get the menu with links to the user/gedcom favorites
	* @return Menu the menu item
	*/
	static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $GEDCOM, $QUERY_STRING, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION;
		global $SEARCH_SPIDER;
		global $controller; // Pages with a controller can be added to the favorites

		if ($SEARCH_SPIDER) {
			return false; // show no favorites, because they taint every page that is indexed.
		}

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new Menu(i18n::translate('Favorites'), '#', 'down');
		if (!empty($WT_IMAGES['gedcom']['large'])) {
			$menu->addIcon($WT_IMAGE_DIR.'/'.$WT_IMAGES['gedcom']['large']);
		}
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_gedcom");

		$gedfavs=getUserFavorites(WT_GEDCOM);

		if (WT_USER_ID) {
			$userfavs=getUserFavorites(WT_USER_NAME);

			// User favorites
			if ($userfavs || WT_USER_ID) {
				$submenu=new Menu('<strong>'.i18n::translate('My Favorites').'</strong>');
				$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				$menu->addSubMenu($submenu);
				while (WT_USER_ID && isset($controller)) {
					// Get the right $gid from each supported controller type
					switch (get_class($controller)) {
					case 'IndividualController':
						$gid = $controller->pid;
						break;
					case 'FamilyController':
						$gid = $controller->famid;
						break;
					case 'MediaController':
						$gid = $controller->mid;
						break;
					case 'SourceController':
						$gid = $controller->sid;
						break;
					case 'RepositoryController':
						$gid = $controller->rid;
						break;
					default:
						break 2;
					}
					$submenu=new Menu('<em>'.i18n::translate('Add to My Favorites').'</em>', WT_SCRIPT_NAME.normalize_query_string($QUERY_STRING.'&amp;action=addfav&amp;gid='.$gid));
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
					$menu->addSubMenu($submenu);
					break;
				}
				foreach ($userfavs as $fav) {
					$GEDCOM=$fav['file'];
					switch($fav['type']) {
					case 'URL':
						$submenu=new Menu(PrintReady($fav['title']), $fav['url']);
						$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
						$menu->addSubMenu($submenu);
						break;
					case 'INDI':
					case 'FAM':
					case 'SOUR':
					case 'OBJE':
						if (displayDetailsById($fav['gid'], $fav['type'])) {
							$obj=GedcomRecord::getInstance($fav['gid']);
							if ($obj) {
								$submenu=new Menu(PrintReady($obj->getFullName()), encode_url($obj->getLinkUrl()));
								$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
								$menu->addSubMenu($submenu);
							}
						}
						break;
					}
					$GEDCOM=WT_GEDCOM;
				}
				if ($gedfavs) {
					$menu->addSeparator();
				}
			}
		}
		// Gedcom favorites
		if ($gedfavs) {
			$submenu=new Menu('<strong>'.i18n::translate('This GEDCOM\'s Favorites').'</strong>');
			$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
			$menu->addSubMenu($submenu);
			foreach ($gedfavs as $fav) {
				$GEDCOM=$fav['file'];
				switch($fav['type']) {
				case 'URL':
					$submenu=new Menu(PrintReady($fav['title']), $fav['url']);
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
					$menu->addSubMenu($submenu);
					break;
				case 'INDI':
				case 'FAM':
				case 'SOUR':
				case 'OBJE':
					if (displayDetailsById($fav['gid'], $fav['type'])) {
						$obj=GedcomRecord::getInstance($fav['gid']);
						if ($obj) {
							$submenu=new Menu(PrintReady($obj->getFullName()), encode_url($obj->getLinkUrl()));
							$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
							$menu->addSubMenu($submenu);
						}
					}
					break;
				}
				$GEDCOM=WT_GEDCOM;
			}
		}
		return $menu;
	}
}

?>
