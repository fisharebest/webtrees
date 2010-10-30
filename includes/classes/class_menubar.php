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
require_once WT_ROOT.'includes/classes/class_module.php';

class MenuBar {
	/**
	* get the menu with links to the gedcom portals
	* @return Menu the menu item
	*/
	public static function getGedcomMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES;

		if ($TEXT_DIRECTION=='rtl') $ff='_rtl'; else $ff='';
		//-- main menu
		$menu = new Menu(i18n::translate('Home page'), 'index.php?ctype=gedcom', 'down');
		if (!empty($WT_IMAGES['home']))
			$menu->addIcon('home');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", 'icon_large_gedcom');
		//-- gedcom list
		$gedcom_titles=get_gedcom_titles();
		if (count($gedcom_titles)>1 && get_site_setting('ALLOW_CHANGE_GEDCOM')) {
			foreach ($gedcom_titles as $gedcom_title) {
				$submenu = new Menu(PrintReady($gedcom_title->gedcom_title, true), 'index.php?ctype=gedcom&amp;ged='.$gedcom_title->gedcom_name);
				if (!empty($WT_IMAGES['gedcom'])) {
					$submenu->addIcon('gedcom');
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
	* get the mypage menu
	* @return Menu the menu item
	*/
	public static function getMyPageMenu() {
		global $MEDIA_DIRECTORY, $MULTI_MEDIA;
		global $TEXT_DIRECTION, $WT_IMAGES;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;
		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		if (!WT_USER_ID) {
			return null;
		}

		//-- main menu
		$menu = new Menu(i18n::translate('My Page'), "index.php?ctype=user", "down");
		$menu->addIcon('mypage');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_mypage");

		//-- mypage submenu
		$submenu = new Menu(i18n::translate('My Page'), "index.php?ctype=user");
		$submenu->addIcon('mypage');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mypage");
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$submenu = new Menu(i18n::translate('My account'), "edituser.php");
			$submenu->addIcon('mypage');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mypage");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new Menu(i18n::translate('My Pedigree'), "pedigree.php?rootid=".WT_USER_GEDCOM_ID."&amp;show_full={$showFull}&amp;talloffset={$showLayout}");
			if (!empty($WT_IMAGES["pedigree"]))
				$submenu->addIcon('pedigree');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new Menu(i18n::translate('My Individual Record'), "individual.php?pid=".WT_USER_GEDCOM_ID);
			$submenu->addIcon('indis');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_indis");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN){
			$menu->addSeparator();
			//-- admin submenu
			$submenu = new Menu(i18n::translate('Administration'), "admin.php");
			$submenu->addIcon('admin');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- manage_gedcoms submenu
			$submenu = new Menu(i18n::translate('GEDCOM administration'), "editgedcoms.php");
			$submenu->addIcon('admin');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- user_admin submenu
			if (WT_USER_IS_ADMIN) {
				$submenu = new Menu(i18n::translate('User administration'), "useradmin.php");
				$submenu->addIcon('admin');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
				$menu->addSubmenu($submenu);
				//-- manage_media submenu
				if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
					$submenu = new Menu(i18n::translate('Manage multimedia'), "media.php");
					$submenu->addIcon('menu_media');
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
				$submenu->addIcon('menu_media');
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
	public static function getChartsMenu($rootid='') {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;
		global $controller;

		if (isset($controller)) {
			if (!$rootid) {
				if (isset($controller->pid)) $rootid = $controller->pid;
				if (isset($controller->rootid)) $rootid = $controller->rootid;
			}
		}

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			return null;
		}

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		//-- main charts menu item
		$link = "pedigree.php?ged=".WT_GEDCOM."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
		if ($rootid) $link .= "&amp;rootid={$rootid}";
		$menu = new Menu(i18n::translate('Charts'), $link, "down");
		$menu->addIcon('charts');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_pedigree");

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
				$link = "pedigree.php?ged=".WT_GEDCOM."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
				if ($rootid) $link .= "&amp;rootid={$rootid}";
				$submenu = new Menu(i18n::translate('Pedigree Chart'), $link);
				$submenu->addIcon('pedigree');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
				$menu->addSubmenu($submenu);
				break;

			case "descendancy":
				//-- descendancy
				$link = "descendancy.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Descendancy chart'), $link);
				$submenu->addIcon('descendant');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_descendant");
				$menu->addSubmenu($submenu);
				break;

			case "ancestry":
				//-- ancestry
				$link = "ancestry.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;rootid={$rootid}&amp;show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Ancestry chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "compact":
				//-- compact
				$link = "compact.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Compact Chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "fanchart":
				//-- fan chart
				$link = "fanchart.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Circle diagram'), $link);
				$submenu->addIcon('fanchart');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fanchart");
				$menu->addSubmenu($submenu);
				break;

			case "hourglass":
				//-- hourglass
				$link = "hourglass.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Hourglass chart'), $link);
				$submenu->addIcon('hourglass');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_hourglass");
				$menu->addSubmenu($submenu);
				break;

			case "familybook":
				//-- familybook
				$link = "familybook.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new Menu(i18n::translate('Family book chart'), $link);
				$submenu->addIcon('fambook');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fambook");
				$menu->addSubmenu($submenu);
				break;

			case "timeline":
				//-- timeline
				$link = "timeline.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;pids[]=".$rootid;
				$submenu = new Menu(i18n::translate('Timeline chart'), $link);
				$submenu->addIcon('timeline');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_timeline");
				$menu->addSubmenu($submenu);
				if (isset($controller) && !empty($controller->family)) {
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
							$submenu = new Menu(i18n::translate('Show couple on timeline chart'), 'timeline.php?pids[0]='.$controller->getHusband().'&amp;pids[1]='.$controller->getWife());
							if (!empty($WT_IMAGES["timeline"])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;

						case "childTimeLine":
							// charts / children_timeline
							$submenu = new Menu(i18n::translate('Show children on timeline chart'), 'timeline.php?'.$controller->getChildrenUrlTimeline());
							if (!empty($WT_IMAGES["timeline"])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;

						case "familyTimeLine":
							// charts / family_timeline
							$submenu = new Menu(i18n::translate('Show family on timeline chart'), 'timeline.php?pids[0]='.$controller->getHusband().'&amp;pids[1]='.$controller->getWife().'&amp;'.$controller->getChildrenUrlTimeline(2));
							if (!empty($WT_IMAGES["timeline"])) {
								$submenu->addIcon('timeline');
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
				if ($rootid) $link .= "&amp;pids[]={$rootid}&amp;addFamily=1";
				$submenu = new Menu(i18n::translate('Lifespan chart'), $link);
				$submenu->addIcon('timeline');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_timeline");
				$menu->addSubmenu($submenu);
				break;

			case "relationship":
				if ($rootid) {
					// Pages focused on a specific person
					$from=array($rootid);
					$to=array('', WT_USER_GEDCOM_ID, WT_USER_ROOT_ID);
					if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
						foreach (user_favorites_WT_Module::getUserFavorites(WT_USER_NAME) as $favorite) {
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
							if (isset($controller) && !empty($controller->indi)) {
								$person=Person::getInstance($pid2);
								if ($person instanceof Person) {
									$submenu = new Menu(
										i18n::translate('Relationship Chart').': '.PrintReady($person->getFullName()),
										"relationship.php?pid1={$pid2}&amp;pid2={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDCOM
									);
								} else {
									$submenu = new Menu(
										i18n::translate('Relationship Chart'),
										"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDCOM
									);
								}
								$submenu->addIcon('relationship');
								$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_relationship");
								$menu->addSubmenu($submenu);
							} else {
								$submenu = new Menu(
									i18n::translate('Relationship Chart'),
									"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDCOM
								);
								$submenu->addIcon('relationship');
								$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_relationship");
								$menu->addSubmenu($submenu);
								break;
							}
						}
					}
				}
				break;

			case "statistics":
				//-- statistics plot
				$submenu = new Menu(i18n::translate('Statistics'), "statistics.php?ged=".WT_GEDCOM);
				$submenu->addIcon('statistic');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_statistic");
				$menu->addSubmenu($submenu);
				break;

			case "treenav":
				//-- interactive tree
				$link = "treenav.php?ged=".WT_GEDCOM;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Interactive tree'), $link);
				$submenu->addIcon('tree');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_gedcom");
				$menu->addSubmenu($submenu);
				break;

			//added for pedigree_map
			case "pedigree_map":
				//-- pedigree map
				$link = "module.php?ged=".WT_GEDCOM."&amp;mod=googlemap&amp;mod_action=pedigree_map";
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new Menu(i18n::translate('Pedigree Map'), $link);
				global $WT_IMAGES;
				$WT_IMAGES['pedigree_map']='modules/googlemap/images/pedigree_map.gif';
				$submenu->addIcon('pedigree_map');
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
	public static function getListsMenu($surname="") {
		global $TEXT_DIRECTION, $WT_IMAGES, $MULTI_MEDIA, $SEARCH_SPIDER, $controller;

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
				$link .= "&amp;surname={$surname}";
				$menu = new Menu(i18n::translate('Lists'), $link);
				$menu->addIcon('lists');
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_small_indis");
			} else {
				$menu = new Menu(i18n::translate('Lists'), $link, "down");
				$menu->addIcon('lists');
				$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_indis");
			}

			//-- gedcom list
			if (get_site_setting('ALLOW_CHANGE_GEDCOM')) {
				foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
					$submenu = new Menu(i18n::translate('Individuals')." - ".PrintReady(get_gedcom_setting($ged_id, 'title')), 'indilist.php?ged='.$gedcom);
					$submenu->addIcon('gedcom');
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_gedcom");
					$menu->addSubmenu($submenu);
				}
			}
			return $menu;
		}
		//-- main lists menu item
		$link = "indilist.php?ged=".WT_GEDCOM;
		if ($style=="sub") {
			$link .= "&amp;surname=".$surname;
			$menu = new Menu(i18n::translate('Lists'), $link);
			$menu->addIcon('lists');
			$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_indis");
		} else {
			$menu = new Menu(i18n::translate('Lists'), $link, "down");
			$menu->addIcon('lists');
			$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_indis");
		}

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["individual"] = i18n::translate('Individuals');
		if (file_exists(WT_ROOT.'famlist.php')) $menuList["family"] = i18n::translate('Families');
		if (file_exists(WT_ROOT.'branches.php')) $menuList["branches"] = i18n::translate('Branches');
		if ($style=="top" && file_exists(WT_ROOT.'sourcelist.php')) $menuList["source"] = i18n::translate('Sources');
		if ($style=="top" && file_exists(WT_ROOT.'notelist.php')) $menuList["note"] = i18n::translate('Shared Notes');
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
				if ($surname) $link .= "&amp;surname=".$surname;
				$submenu = new Menu(i18n::translate('Individuals'), $link);
				$submenu->addIcon('indis');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_indis");
				$menu->addSubmenu($submenu);
				break;

			case "family":
				//-- famlist sub menu
				$link = "famlist.php?ged=".WT_GEDCOM;
				if ($surname) $link .= "&amp;surname=".$surname;
				$submenu = new Menu(i18n::translate('Families'), $link);
				$submenu->addIcon('cfamily');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_cfamily");
				$menu->addSubmenu($submenu);
				break;

			case "branches":
				//-- branches sub menu
				$link = "branches.php?ged=".WT_GEDCOM;
				if ($surname) $link .= "&amp;surn=".$surname;
				$submenu = new Menu(i18n::translate('Branches'), $link);
				$submenu->addIcon('patriarch');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_patriarch");
				$menu->addSubmenu($submenu);
				break;

			case "source":
				//-- source
				$submenu = new Menu(i18n::translate('Sources'), 'sourcelist.php?ged='.WT_GEDCOM);
				$submenu->addIcon('menu_source');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_source");
				$menu->addSubmenu($submenu);
				break;

			case "note":
				//-- shared note
				$submenu = new Menu(i18n::translate('Shared Notes'), 'notelist.php?ged='.WT_GEDCOM);
				$submenu->addIcon('menu_note');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_notes");
				$menu->addSubmenu($submenu);
				break;

			case "repository":
				//-- repository
				$submenu = new Menu(i18n::translate('Repositories'), "repolist.php");
				$submenu->addIcon('menu_repository');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_repository");
				$menu->addSubmenu($submenu);
				break;

			case "places":
				//-- places
				$submenu = new Menu(i18n::translate('Place hierarchy'), 'placelist.php?ged='.WT_GEDCOM);
				$submenu->addIcon('place');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_place");
				$menu->addSubmenu($submenu);
				break;

			case "media":
				//-- medialist
				$submenu = new Menu(i18n::translate('Multimedia'), 'medialist.php?ged='.WT_GEDCOM);
				$submenu->addIcon('menu_media');
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
	public static function getCalendarMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'calendar.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new Menu("", "", "");
			return $menu;
		}
		//-- main calendar menu item
		$menu = new Menu(i18n::translate('Calendar'), 'calendar.php?ged='.WT_GEDCOM, "down");
		$menu->addIcon('calendar');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_calendar");
		//-- viewday sub menu
		$submenu = new Menu(i18n::translate('View Day'), 'calendar.php?ged='.WT_GEDCOM);
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new Menu(i18n::translate('View Month'), "calendar.php?ged=".WT_GEDCOM."&amp;action=calendar");
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new Menu(i18n::translate('View Year'), "calendar.php?ged=".WT_GEDCOM."&amp;action=year");
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return Menu the menu item
	*/
	public static function getReportsMenu($pid="", $famid="") {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $controller;

		$active_reports=WT_Module::getActiveReports();
		if ($SEARCH_SPIDER || !$active_reports) {
			return null;
		}

		if ($TEXT_DIRECTION=="rtl") {
			$ff="_rtl";
		} else {
			$ff="";
		}

		$menu = new Menu(i18n::translate('Reports'), 'reportengine.php?ged='.WT_GEDCOM, "down");
		$menu->addIcon('reports');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_reports");

		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the search menu
	* @return Menu the menu item
	*/
	public static function getSearchMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SHOW_MULTISITE_SEARCH, $SEARCH_SPIDER;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'search.php')) || (!empty($SEARCH_SPIDER))) {
			return null;
		}
		//-- main search menu item
		$menu = new Menu(i18n::translate('Search'), 'search.php?ged='.WT_GEDCOM, "down");
		$menu->addIcon('search');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_search");
		//-- search_general sub menu
		$submenu = new Menu(i18n::translate('General Search'), "search.php?ged=".WT_GEDCOM."&amp;action=general");
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new Menu(i18n::translate('Soundex Search'), "search.php?ged=".WT_GEDCOM."&amp;action=soundex");
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new Menu(i18n::translate('Advanced search'), "search_advanced.php?ged=".WT_GEDCOM);
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(i18n::translate('Search and replace'), "search.php?ged=".WT_GEDCOM."&amp;action=replace");
			$submenu->addIcon('search');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
			$menu->addSubmenu($submenu);
		}

		//-- search_multisite sub menu
		if ($SHOW_MULTISITE_SEARCH >= WT_USER_ACCESS_LEVEL) {
			$sitelist = get_server_list();
			if (count($sitelist)>0) {
				$submenu = new Menu(i18n::translate('Multi Site Search'), "search.php?ged=".WT_GEDCOM."&amp;action=multisite");
				$submenu->addIcon('search');
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
	public static function getModuleMenus() {
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
	public static function getHelpMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $QUERY_STRING, $helpindex, $action;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			return null;
		}
		//-- main help menu item
		$menu = new Menu(i18n::translate('Help'), "#", "down");
		$menu->addIcon('menu_help');
		if (empty($helpindex))
			$menu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$menu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_help");

		//-- help_for_this_page sub menu
		$submenu = new Menu(i18n::translate('Help with this page'), "#");
		$submenu->addIcon('help');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		if (empty($helpindex))
			$submenu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$submenu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addSubmenu($submenu);
		//-- help_contents sub menu
		$submenu = new Menu(i18n::translate('Help Contents'), "#");
		$submenu->addIcon('help');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$submenu->addOnclick("return helpPopup('help_contents_help');");
		$menu->addSubmenu($submenu);
		//-- faq sub menu
		if (array_key_exists('faq', WT_Module::getActiveModules()) && WT_DB::prepare("SELECT COUNT(*) FROM `##block` WHERE module_name='faq'")->fetchOne()) {

			$submenu = new Menu(i18n::translate('FAQ'), "module.php?mod=faq&mod_action=show");
			$submenu->addIcon('help');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
			$menu->addSubmenu($submenu);
		}
		//-- add wiki links
		$menu->addSeparator();
		$submenu = new Menu(i18n::translate('Wiki Main Page'), WT_WEBTREES_WIKI.'" target="_blank');
		$submenu->addIcon('wiki');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_wiki");
		$menu->addSubmenu($submenu);

		//-- add contact links to help menu
		$menu->addSeparator();
		$menuitems = contact_menus();
		foreach($menuitems as $menuitem) {
			$submenu = new Menu($menuitem["label"], $menuitem["link"]);
			$submenu->addIcon('mypage');
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
		$submenu->addIcon('help');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the menu with links change to each theme
	* @return Menu the menu item
	*/
	public static function getThemeMenu() {
		global $SEARCH_SPIDER, $ALLOW_THEME_DROPDOWN;

		if ($ALLOW_THEME_DROPDOWN && !$SEARCH_SPIDER && get_site_setting('ALLOW_USER_THEMES')) {
			$url=WT_SCRIPT_NAME.'?'.get_query_string();
			$menu=new Menu(i18n::translate('Theme'));
			$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
			foreach (get_theme_names() as $themename=>$themedir) {
				$submenu=new Menu($themename, $url.'&amp;theme='.rawurlencode($themedir));
				if ($themedir==WT_THEME_DIR) {
					$submenu->addClass('favsubmenuitem_selected', 'favsubmenuitem_hover');
				} else {
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				}
				$menu->addSubMenu($submenu);
			}
			return $menu;
		} else {
			return null;
		}
	}
	/**
	* get the menu with links change to each color themes subcolor type
	* @return Menu the menu item
	*/
	public static function getColorMenu($COLOR_THEME_LIST) {
		$url=WT_SCRIPT_NAME.'?'.get_query_string();
		$menu=new Menu(i18n::translate('Color Palette'));
		$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
		foreach ($COLOR_THEME_LIST as $colorChoice=>$colorName) {
			$submenu=new Menu($colorName, $url.'&amp;themecolor='.rawurlencode($colorChoice));
			$menu->addSubMenu($submenu);
		}
		return $menu;
	}
	/**
	* get the menu with links to change language
	* @return Menu the menu item
	*/
	public static function getLanguageMenu() {
		global $QUERY_STRING, $WT_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new Menu(i18n::translate('Language'), '#', 'down');
		$menu->addClass("langmenuitem$ff", "langmenuitem_hover$ff", "submenu$ff", "icon_language");

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
			return null;
		}
	}
	/**
	* get the menu with links to the user/gedcom favorites
	* @return Menu the menu item
	*/
	public static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $GEDCOM, $QUERY_STRING, $WT_IMAGES, $TEXT_DIRECTION;
		global $SEARCH_SPIDER;
		global $controller; // Pages with a controller can be added to the favorites

		if ($SEARCH_SPIDER) {
			return false; // show no favorites, because they taint every page that is indexed.
		}

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new Menu(i18n::translate('Favorites'), '#', 'down');
		$menu->addIcon('gedcom');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_gedcom");

		if (array_key_exists('gedcom_favorites', WT_Module::getActiveModules())) {
			$gedfavs=gedcom_favorites_WT_Module::getUserFavorites(WT_GEDCOM);
		} else {
			$gedfavs=array();
		}

		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$userfavs=user_favorites_WT_Module::getUserFavorites(WT_USER_NAME);

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
						$obj=GedcomRecord::getInstance($fav['gid']);
						if ($obj && $obj->canDisplayName()) {
							$submenu=new Menu(PrintReady($obj->getFullName()), $obj->getHtmlUrl());
							$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
							$menu->addSubMenu($submenu);
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
					$obj=GedcomRecord::getInstance($fav['gid']);
					if ($obj && $obj->canDisplayName()) {
						$submenu=new Menu(PrintReady($obj->getFullName()), $obj->getHtmlUrl());
						$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
						$menu->addSubMenu($submenu);
					}
					break;
				}
				$GEDCOM=WT_GEDCOM;
			}
		}
		return $menu;
	}
}
