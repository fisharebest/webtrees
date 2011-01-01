<?php
// System for generating menus.
//
// webtrees: Web based Family History software
// Copyright (C) 2010 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
//
// Modifications Copyright (c) 2010 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class WT_MenuBar {
	/**
	* get the menu with links to the gedcom portals
	* @return WT_Menu the menu item
	*/
	public static function getGedcomMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES;

		if ($TEXT_DIRECTION=='rtl') $ff='_rtl'; else $ff='';
		//-- main menu
		$menu = new WT_Menu(i18n::translate('Home page'), 'index.php?ctype=gedcom', 'down');
		if (!empty($WT_IMAGES['home']))
			$menu->addIcon('home');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", 'icon_large_gedcom');
		//-- gedcom list
		$gedcom_titles=get_gedcom_titles();
		if (count($gedcom_titles)>1 && get_site_setting('ALLOW_CHANGE_GEDCOM')) {
			foreach ($gedcom_titles as $gedcom_title) {
				$submenu = new WT_Menu(PrintReady($gedcom_title->gedcom_title, true), 'index.php?ctype=gedcom&amp;ged='.rawurlencode($gedcom_title->gedcom_name));
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
	* @return WT_Menu the menu item
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
		$menu = new WT_Menu(i18n::translate('My Page'), "index.php?ctype=user&amp;ged=".WT_GEDURL, "down");
		$menu->addIcon('mypage');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_mypage");

		//-- mypage submenu
		$submenu = new WT_Menu(i18n::translate('My Page'), "index.php?ctype=user&amp;ged=".WT_GEDURL);
		$submenu->addIcon('mypage');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mypage");
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$submenu = new WT_Menu(i18n::translate('My account'), "edituser.php");
			$submenu->addIcon('mypage');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_mypage");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new WT_Menu(i18n::translate('My Pedigree'), "pedigree.php?ged=".WT_GEDURL."&amp;rootid=".WT_USER_GEDCOM_ID."&amp;show_full={$showFull}&amp;talloffset={$showLayout}");
			if (!empty($WT_IMAGES["pedigree"]))
				$submenu->addIcon('pedigree');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new WT_Menu(i18n::translate('My individual record'), "individual.php?ged=".WT_GEDURL."&amp;pid=".WT_USER_GEDCOM_ID);
			$submenu->addIcon('indis');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_indis");
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN) {
			$menu->addSeparator();
			//-- admin submenu
			$submenu = new WT_Menu(i18n::translate('Administration'), "admin.php");
			$submenu->addIcon('admin');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- manage_gedcoms submenu
			$submenu = new WT_Menu(i18n::translate('GEDCOM administration'), "editgedcoms.php");
			$submenu->addIcon('admin');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
			$menu->addSubmenu($submenu);
			//-- user_admin submenu
			if (WT_USER_IS_ADMIN) {
				$submenu = new WT_Menu(i18n::translate('User administration'), "useradmin.php");
				$submenu->addIcon('admin');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_admin");
				$menu->addSubmenu($submenu);
				//-- manage_media submenu
				if (is_writable($MEDIA_DIRECTORY) && $MULTI_MEDIA) {
					$submenu = new WT_Menu(i18n::translate('Manage multimedia'), "media.php?ged=".WT_GEDURL);
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
				$submenu = new WT_Menu(i18n::translate('Upload media files'), "uploadmedia.php?ged=".WT_GEDURL);
				$submenu->addIcon('menu_media');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_media");
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	/**
	* get the menu for the charts
	* @return WT_Menu the menu item
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
		$link = "pedigree.php?ged=".WT_GEDURL."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
		if ($rootid) $link .= "&amp;rootid={$rootid}";
		$menu = new WT_Menu(i18n::translate('Charts'), $link, "down");
		$menu->addIcon('charts');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_pedigree");

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array();
		$menuList["pedigree"] = i18n::translate('Pedigree Chart');
		if (file_exists(WT_ROOT.'descendancy.php')) $menuList["descendancy"] = i18n::translate('Descendancy chart');
		if (file_exists(WT_ROOT.'ancestry.php')) $menuList["ancestry"] = i18n::translate('Ancestry chart');
		if (file_exists(WT_ROOT.'compact.php')) $menuList["compact"] = i18n::translate('Compact chart');
		if (file_exists(WT_ROOT.'fanchart.php') && function_exists("imagettftext")) $menuList["fanchart"] = i18n::translate('Circle diagram');
		if (file_exists(WT_ROOT.'hourglass.php')) $menuList["hourglass"] = i18n::translate('Hourglass chart');
		if (file_exists(WT_ROOT.'familybook.php')) $menuList["familybook"] = i18n::translate('Family book chart');
		if (file_exists(WT_ROOT.'timeline.php')) $menuList["timeline"] = i18n::translate('Timeline chart');
		if (file_exists(WT_ROOT.'lifespan.php')) $menuList["lifespan"] = i18n::translate('Lifespan chart');
		if (file_exists(WT_ROOT.'relationship.php')) $menuList["relationship"] = i18n::translate('Relationship chart');
		if (file_exists(WT_ROOT.'statistics.php')) $menuList["statistics"] = i18n::translate('Statistics');
		if (file_exists(WT_ROOT.'treenav.php')) $menuList["treenav"] = i18n::translate('Interactive tree');
		if (file_exists(WT_ROOT.'modules/googlemap/pedigree_map.php')) {
			$menuList["pedigree_map"] = i18n::translate('Pedigree Map');//added for pedigree_map
		}
		asort($menuList);

		// Produce the submenus in localized name order
		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case "pedigree":
				//-- pedigree
				$link = "pedigree.php?ged=".WT_GEDURL."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
				if ($rootid) $link .= "&amp;rootid={$rootid}";
				$submenu = new WT_Menu(i18n::translate('Pedigree Chart'), $link);
				$submenu->addIcon('pedigree');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_pedigree");
				$menu->addSubmenu($submenu);
				break;

			case "descendancy":
				//-- descendancy
				$link = "descendancy.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(i18n::translate('Descendancy chart'), $link);
				$submenu->addIcon('descendant');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_descendant");
				$menu->addSubmenu($submenu);
				break;

			case "ancestry":
				//-- ancestry
				$link = "ancestry.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;rootid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(i18n::translate('Ancestry chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "compact":
				//-- compact
				$link = "compact.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new WT_Menu(i18n::translate('Compact chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_ancestry");
				$menu->addSubmenu($submenu);
				break;

			case "fanchart":
				//-- fan chart
				$link = "fanchart.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new WT_Menu(i18n::translate('Circle diagram'), $link);
				$submenu->addIcon('fanchart');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fanchart");
				$menu->addSubmenu($submenu);
				break;

			case "hourglass":
				//-- hourglass
				$link = "hourglass.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(i18n::translate('Hourglass chart'), $link);
				$submenu->addIcon('hourglass');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_hourglass");
				$menu->addSubmenu($submenu);
				break;

			case "familybook":
				//-- familybook
				$link = "familybook.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(i18n::translate('Family book chart'), $link);
				$submenu->addIcon('fambook');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_fambook");
				$menu->addSubmenu($submenu);
				break;

			case "timeline":
				//-- timeline
				$link = "timeline.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;pids[]=".$rootid;
				$submenu = new WT_Menu(i18n::translate('Timeline chart'), $link);
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
					foreach ($menuList as $menuType => $menuName) {
						switch ($menuType) {
						case "parentTimeLine":
							// charts / parents_timeline
							$submenu = new WT_Menu(i18n::translate('Show couple on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE')).'&amp;ged='.WT_GEDURL);
							if (!empty($WT_IMAGES["timeline"])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;

						case "childTimeLine":
							// charts / children_timeline
							$submenu = new WT_Menu(i18n::translate('Show children on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('CHIL')).'&amp;ged='.WT_GEDURL);
							if (!empty($WT_IMAGES["timeline"])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass("submenuitem{$ff}", "submenuitem_hover{$ff}");
							$menu->addSubmenu($submenu);
							break;

						case "familyTimeLine":
							// charts / family_timeline
							$submenu = new WT_Menu(i18n::translate('Show family on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE','CHIL')).'&amp;ged='.WT_GEDURL);
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
				$link = "lifespan.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;pids[]={$rootid}&amp;addFamily=1";
				$submenu = new WT_Menu(i18n::translate('Lifespan chart'), $link);
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
								$person=WT_Person::getInstance($pid2);
								if ($person instanceof WT_Person) {
									$submenu = new WT_Menu(
										i18n::translate('Relationship chart').': '.PrintReady($person->getFullName()),
										"relationship.php?pid1={$pid2}&amp;pid2={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
									);
								} else {
									$submenu = new WT_Menu(
										i18n::translate('Relationship chart'),
										"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
									);
								}
								$submenu->addIcon('relationship');
								$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_relationship");
								$menu->addSubmenu($submenu);
							} else {
								$submenu = new WT_Menu(
									i18n::translate('Relationship chart'),
									"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
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
				$submenu = new WT_Menu(i18n::translate('Statistics'), "statistics.php?ged=".WT_GEDURL);
				$submenu->addIcon('statistic');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_statistic");
				$menu->addSubmenu($submenu);
				break;

			case "treenav":
				//-- interactive tree
				$link = "treenav.php?ged=".WT_GEDURL;
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new WT_Menu(i18n::translate('Interactive tree'), $link);
				$submenu->addIcon('tree');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_gedcom");
				$menu->addSubmenu($submenu);
				break;

			//added for pedigree_map
			case "pedigree_map":
				//-- pedigree map
				$link = "module.php?ged=".WT_GEDURL."&amp;mod=googlemap&amp;mod_action=pedigree_map";
				if ($rootid) $link .= "&amp;rootid=".$rootid;
				$submenu = new WT_Menu(i18n::translate('Pedigree Map'), $link);
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
	* @return WT_Menu the menu item
	*/
	public static function getListsMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $MULTI_MEDIA, $SEARCH_SPIDER, $controller;

		$surname='';
		if (isset($controller)) {
			if (isset($controller->indi)) {
				list($surname)=explode(',', $controller->indi->getSortName());
			}
			if (isset($controller->rootid)) {
				$person = WT_Person::getInstance($controller->rootid);
				list($surname)=explode(',', $person->getSortName());
			}
		}

		if ($TEXT_DIRECTION=='rtl') $ff='_rtl'; else $ff='';

		// The top level menu shows the individual list
		$menu=new WT_Menu(i18n::translate('Lists'), 'indilist.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('lists');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", 'icon_large_indis');
 
		// Search engines only get the individual list
		if ($SEARCH_SPIDER) {
			return $menu;
		}

		// Build a list of submenu items and then sort it in localized name order
		$menuList=array(
			'branches.php'  =>i18n::translate('Branches'),
			'famlist.php'   =>i18n::translate('Families'),
			'indilist.php'  =>i18n::translate('Individuals'),
			'medialist.php' =>i18n::translate('Multimedia'),
			'placelist.php' =>i18n::translate('Place hierarchy'),
			'repolist.php'  =>i18n::translate('Repositories'),
			'notelist.php'  =>i18n::translate('Shared notes'),
			'sourcelist.php'=>i18n::translate('Sources')
		);
		asort($menuList);

		foreach ($menuList as $page=>$name) {
			$link=$page.'?ged='.WT_GEDURL;
			switch ($page) {
			case 'indilist.php':
				if ($surname) $link .= '&amp;surname='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('indis');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_indis');
				break;

			case 'famlist.php':
				if ($surname) $link .= '&amp;surname='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('cfamily');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_cfamily');
				break;

			case 'branches.php':
				if ($surname) $link .= '&amp;surn='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('patriarch');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_patriarch');
				break;

			case 'sourcelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_source');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_menu_source');
				break;

			case 'notelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_note');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_notes');
				break;

			case 'repolist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_repository');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_menu_repository');
				break;

			case 'placelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('place');
				$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_place');
				break;

			case 'medialist.php':
				if ($MULTI_MEDIA) {
					$submenu = new WT_Menu($name, $link);
					$submenu->addIcon('menu_media');
					$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_menu_media');
				}
				break;
			}
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/**
	* get the menu for the calendar
	* @return WT_Menu the menu item
	*/
	public static function getCalendarMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;

		if ($TEXT_DIRECTION=='rtl') $ff='_rtl'; else $ff='';
		if ((!file_exists(WT_ROOT.'calendar.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new WT_Menu('', '', '');
			return $menu;
		}
		//-- main calendar menu item
		$menu = new WT_Menu(i18n::translate('Calendar'), 'calendar.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('calendar');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", 'icon_large_calendar');
		//-- viewday sub menu
		$submenu = new WT_Menu(i18n::translate('View Day'), 'calendar.php?ged='.WT_GEDURL);
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", '', 'icon_small_calendar');
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new WT_Menu(i18n::translate('View Month'), "calendar.php?ged=".WT_GEDURL."&amp;action=calendar");
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new WT_Menu(i18n::translate('View Year'), "calendar.php?ged=".WT_GEDURL."&amp;action=year");
		$submenu->addIcon('calendar');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_calendar");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return WT_Menu the menu item
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

		$menu = new WT_Menu(i18n::translate('Reports'), 'reportengine.php?ged='.WT_GEDURL, "down");
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
	* @return WT_Menu the menu item
	*/
	public static function getSearchMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if ((!file_exists(WT_ROOT.'search.php')) || (!empty($SEARCH_SPIDER))) {
			return null;
		}
		//-- main search menu item
		$menu = new WT_Menu(i18n::translate('Search'), 'search.php?ged='.WT_GEDURL, "down");
		$menu->addIcon('search');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_search");
		//-- search_general sub menu
		$submenu = new WT_Menu(i18n::translate('General Search'), "search.php?ged=".WT_GEDURL."&amp;action=general");
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new WT_Menu(i18n::translate('Soundex Search'), "search.php?ged=".WT_GEDURL."&amp;action=soundex");
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new WT_Menu(i18n::translate('Advanced search'), "search_advanced.php?ged=".WT_GEDURL);
		$submenu->addIcon('search');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(i18n::translate('Search and replace'), "search.php?ged=".WT_GEDURL."&amp;action=replace");
			$submenu->addIcon('search');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_search");
			$menu->addSubmenu($submenu);
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
	* @return WT_Menu the menu item
	*/
	public static function getHelpMenu() {
		global $TEXT_DIRECTION, $WT_IMAGES, $SEARCH_SPIDER, $helpindex, $action;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";
		if (!empty($SEARCH_SPIDER)) {
			return null;
		}
		//-- main help menu item
		$menu = new WT_Menu(i18n::translate('Help'), "#", "down");
		$menu->addIcon('menu_help');
		if (empty($helpindex))
			$menu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$menu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_help");

		//-- help_contents sub menu
		$submenu = new WT_Menu(i18n::translate('Help contents'), "#");
		$submenu->addIcon('help');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$submenu->addOnclick("return helpPopup('help_contents_help');");
		$menu->addSubmenu($submenu);
		//-- faq sub menu
		if (array_key_exists('faq', WT_Module::getActiveModules()) && WT_DB::prepare("SELECT COUNT(*) FROM `##block` WHERE module_name='faq'")->fetchOne()) {

			$submenu = new WT_Menu(i18n::translate('FAQ'), "module.php?mod=faq&mod_action=show");
			$submenu->addIcon('help');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
			$menu->addSubmenu($submenu);
		}
		//-- add wiki links
		$menu->addSeparator();
		$submenu = new WT_Menu(i18n::translate('Wiki Main Page'), WT_WEBTREES_WIKI.'" target="_blank');
		$submenu->addIcon('wiki');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_wiki");
		$menu->addSubmenu($submenu);

		//-- add contact links to help menu
		$menu->addSeparator();
		$menuitems = contact_menus();
		foreach ($menuitems as $menuitem) {
			$submenu = new WT_Menu($menuitem["label"], $menuitem["link"]);
			$submenu->addIcon('mypage');
			$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_contact");
			if (!empty($menuitem["onclick"])) $submenu->addOnclick($menuitem["onclick"]);
			$menu->addSubmenu($submenu);
		}
		//-- add show/hide context_help
		$menu->addSeparator();
		if ($_SESSION["show_context_help"])
			$submenu = new WT_Menu(i18n::translate('Hide contextual help'), get_query_url(array('show_context_help'=>'no')));
		else
			$submenu = new WT_Menu(i18n::translate('Show contextual help'), get_query_url(array('show_context_help'=>'yes')));
		$submenu->addIcon('help');
		$submenu->addClass("submenuitem$ff", "submenuitem_hover$ff", "", "icon_small_menu_help");
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the menu with links change to each theme
	* @return WT_Menu the menu item
	*/
	public static function getThemeMenu() {
		global $SEARCH_SPIDER, $ALLOW_THEME_DROPDOWN;

		if ($ALLOW_THEME_DROPDOWN && !$SEARCH_SPIDER && get_site_setting('ALLOW_USER_THEMES')) {
			$menu=new WT_Menu(i18n::translate('Theme'));
			$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
			foreach (get_theme_names() as $themename=>$themedir) {
				$submenu=new WT_Menu($themename, get_query_url(array('theme'=>$themedir)));
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
	* @return WT_Menu the menu item
	*/
	public static function getColorMenu($COLOR_THEME_LIST) {
		$menu=new WT_Menu(i18n::translate('Color Palette'));
		$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', "icon_small_theme");
		foreach ($COLOR_THEME_LIST as $colorChoice=>$colorName) {
			$submenu=new WT_Menu($colorName, get_query_url(array('themecolor'=>$colorChoice)));
			$menu->addSubMenu($submenu);
		}
		return $menu;
	}
	/**
	* get the menu with links to change language
	* @return WT_Menu the menu item
	*/
	public static function getLanguageMenu() {
		global $WT_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new WT_Menu(i18n::translate('Language'), '#', 'down');
		$menu->addClass("langmenuitem$ff", "langmenuitem_hover$ff", "submenu$ff", "icon_language");

		foreach (i18n::installed_languages() as $lang=>$name) {
			$submenu=new WT_Menu($name, get_query_url(array('lang'=>$lang)));
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
	* @return WT_Menu the menu item
	*/
	public static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $GEDCOM, $WT_IMAGES, $TEXT_DIRECTION;
		global $SEARCH_SPIDER;
		global $controller; // Pages with a controller can be added to the favorites

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		$menu=new WT_Menu(i18n::translate('Favorites'), '#', 'down');
		$menu->addIcon('gedcom');
		$menu->addClass("menuitem$ff", "menuitem_hover$ff", "submenu$ff", "icon_large_gedcom");

		// Don't list favorites on private sites and for search engines
		if (!WT_USER_ID && $REQUIRE_AUTHENTICATION || $SEARCH_SPIDER) {
			return $menu;
		}

		if (array_key_exists('gedcom_favorites', WT_Module::getActiveModules())) {
			$gedfavs=gedcom_favorites_WT_Module::getUserFavorites(WT_GEDCOM);
		} else {
			$gedfavs=array();
		}

		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$userfavs=user_favorites_WT_Module::getUserFavorites(WT_USER_NAME);

			// User favorites
			if ($userfavs || WT_USER_ID) {
				$submenu=new WT_Menu('<strong>'.i18n::translate('My Favorites').'</strong>');
				$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
				$menu->addSubMenu($submenu);
				while (WT_USER_ID && isset($controller)) {
					// Get the right $gid from each supported controller type
					switch (get_class($controller)) {
					case 'WT_Controller_Individual':
						$gid = $controller->pid;
						break;
					case 'WT_Controller_Family':
						$gid = $controller->famid;
						break;
					case 'WT_Controller_Media':
						$gid = $controller->mid;
						break;
					case 'WT_Controller_Source':
						$gid = $controller->sid;
						break;
					case 'WT_Controller_Repository':
						$gid = $controller->rid;
						break;
					default:
						break 2;
					}
					$submenu=new WT_Menu('<em>'.i18n::translate('Add to My Favorites').'</em>', get_query_url(array('action'=>'addfav', 'gid'=>$gid)));
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
					$menu->addSubMenu($submenu);
					break;
				}
				foreach ($userfavs as $fav) {
					$GEDCOM=$fav['file'];
					switch($fav['type']) {
					case 'URL':
						$submenu=new WT_Menu(PrintReady($fav['title']), $fav['url']);
						$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
						$menu->addSubMenu($submenu);
						break;
					case 'INDI':
					case 'FAM':
					case 'SOUR':
					case 'OBJE':
						$obj=WT_GedcomRecord::getInstance($fav['gid']);
						if ($obj && $obj->canDisplayName()) {
							$submenu=new WT_Menu(PrintReady($obj->getFullName()), $obj->getHtmlUrl());
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
			$submenu=new WT_Menu('<strong>'.i18n::translate('This GEDCOM\'s Favorites').'</strong>');
			$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
			$menu->addSubMenu($submenu);
			foreach ($gedfavs as $fav) {
				$GEDCOM=$fav['file'];
				switch($fav['type']) {
				case 'URL':
					$submenu=new WT_Menu(PrintReady($fav['title']), $fav['url']);
					$submenu->addClass('favsubmenuitem', 'favsubmenuitem_hover');
					$menu->addSubMenu($submenu);
					break;
				case 'INDI':
				case 'FAM':
				case 'SOUR':
				case 'OBJE':
					$obj=WT_GedcomRecord::getInstance($fav['gid']);
					if ($obj && $obj->canDisplayName()) {
						$submenu=new WT_Menu(PrintReady($obj->getFullName()), $obj->getHtmlUrl());
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
