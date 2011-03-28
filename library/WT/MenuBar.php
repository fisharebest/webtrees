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

class WT_MenuBar {
	public static function getGedcomMenu() {
		global $WT_IMAGES;

		//-- main menu
		$menu = new WT_Menu(WT_I18N::translate('Home page'), 'index.php?ctype=gedcom', 'down');
		if (!empty($WT_IMAGES['home']))
			$menu->addIcon('home');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_gedcom');
		//-- gedcom list
		$gedcom_titles=get_gedcom_titles();
		if (count($gedcom_titles)>1 && get_site_setting('ALLOW_CHANGE_GEDCOM')) {
			foreach ($gedcom_titles as $gedcom_title) {
				$submenu = new WT_Menu(PrintReady($gedcom_title->gedcom_title, true), 'index.php?ctype=gedcom&amp;ged='.rawurlencode($gedcom_title->gedcom_name));
				if (!empty($WT_IMAGES['gedcom'])) {
					$submenu->addIcon('gedcom');
				}
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_gedcom');
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	public static function getMyPageMenu() {
		global $MEDIA_DIRECTORY, $MULTI_MEDIA;
		global $WT_IMAGES;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		if (!WT_USER_ID) {
			return null;
		}

		//-- main menu
		$menu = new WT_Menu(WT_I18N::translate('My Page'), 'index.php?ctype=user&amp;ged='.WT_GEDURL, 'down');
		$menu->addIcon('mypage');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_mypage');

		//-- mypage submenu
		$submenu = new WT_Menu(WT_I18N::translate('My Page'), 'index.php?ctype=user&amp;ged='.WT_GEDURL);
		$submenu->addIcon('mypage');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_mypage');
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$submenu = new WT_Menu(WT_I18N::translate('My account'), 'edituser.php');
			$submenu->addIcon('mypage');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_mypage');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new WT_Menu(WT_I18N::translate('My Pedigree'), 'pedigree.php?ged='.WT_GEDURL.'&amp;rootid='.WT_USER_GEDCOM_ID."&amp;show_full={$showFull}&amp;talloffset={$showLayout}");
			if (!empty($WT_IMAGES['pedigree']))
				$submenu->addIcon('pedigree');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_pedigree');
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new WT_Menu(WT_I18N::translate('My individual record'), 'individual.php?ged='.WT_GEDURL.'&amp;pid='.WT_USER_GEDCOM_ID);
			$submenu->addIcon('indis');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_indis');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN) {
			//-- admin submenu
			$submenu = new WT_Menu(WT_I18N::translate('Administration'), 'admin.php');
			$submenu->addIcon('admin');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_admin');
			$menu->addSubmenu($submenu);
		}		
		return $menu;
	}

	public static function getChartsMenu($rootid='') {
		global $WT_IMAGES, $SEARCH_SPIDER;
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;
		global $controller;

		if (isset($controller)) {
			if (!$rootid) {
				if (isset($controller->pid)) $rootid = $controller->pid;
				if (isset($controller->rootid)) $rootid = $controller->rootid;
			}
		}

		if (!empty($SEARCH_SPIDER)) {
			return null;
		}

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		//-- main charts menu item
		$link = 'pedigree.php?ged='.WT_GEDURL."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
		if ($rootid) $link .= "&amp;rootid={$rootid}";
		$menu = new WT_Menu(WT_I18N::translate('Charts'), $link, 'down');
		$menu->addIcon('charts');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_pedigree');

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array(
			'pedigree'    =>WT_I18N::translate('Pedigree Chart'),
			'descendancy' =>WT_I18N::translate('Descendancy chart'),
			'ancestry'    =>WT_I18N::translate('Ancestry chart'),
			'compact'     =>WT_I18N::translate('Compact chart'),
			'hourglass'   =>WT_I18N::translate('Hourglass chart'),
			'familybook'  =>WT_I18N::translate('Family book chart'),
			'timeline'    =>WT_I18N::translate('Timeline chart'),
			'lifespan'    =>WT_I18N::translate('Lifespan chart'),
			'relationship'=>WT_I18N::translate('Relationship chart'),
			'statistics'  =>WT_I18N::translate('Statistics'),
		);
		if (function_exists('imagettftext')) {
			$menuList['fanchart']=WT_I18N::translate('Circle diagram');
		}
		// TODO: Use WT_Module_Chart ??
		if (array_key_exists('tree', WT_Module::getActiveModules())) {
			$menuList['tree']=WT_I18N::translate('Interactive tree');
		}
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			$menuList['pedigree_map']=WT_I18N::translate('Pedigree Map');
		}
		asort($menuList);

		// Produce the submenus in localized name order
		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case 'pedigree':
				//-- pedigree
				$link = 'pedigree.php?ged='.WT_GEDURL."&amp;show_full={$showFull}&amp;talloffset={$showLayout}";
				if ($rootid) $link .= "&amp;rootid={$rootid}";
				$submenu = new WT_Menu(WT_I18N::translate('Pedigree Chart'), $link);
				$submenu->addIcon('pedigree');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_pedigree');
				$menu->addSubmenu($submenu);
				break;

			case 'descendancy':
				//-- descendancy
				$link = 'descendancy.php?ged='.WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(WT_I18N::translate('Descendancy chart'), $link);
				$submenu->addIcon('descendant');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_descendant');
				$menu->addSubmenu($submenu);
				break;

			case 'ancestry':
				//-- ancestry
				$link = 'ancestry.php?ged='.WT_GEDURL;
				if ($rootid) $link .= "&amp;rootid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(WT_I18N::translate('Ancestry chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_ancestry');
				$menu->addSubmenu($submenu);
				break;

			case 'compact':
				//-- compact
				$link = 'compact.php?ged='.WT_GEDURL;
				if ($rootid) $link .= '&amp;rootid='.$rootid;
				$submenu = new WT_Menu(WT_I18N::translate('Compact chart'), $link);
				$submenu->addIcon('ancestry');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_ancestry');
				$menu->addSubmenu($submenu);
				break;

			case 'fanchart':
				//-- fan chart
				$link = 'fanchart.php?ged='.WT_GEDURL;
				if ($rootid) $link .= '&amp;rootid='.$rootid;
				$submenu = new WT_Menu(WT_I18N::translate('Circle diagram'), $link);
				$submenu->addIcon('fanchart');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_fanchart');
				$menu->addSubmenu($submenu);
				break;

			case 'hourglass':
				//-- hourglass
				$link = 'hourglass.php?ged='.WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(WT_I18N::translate('Hourglass chart'), $link);
				$submenu->addIcon('hourglass');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_hourglass');
				$menu->addSubmenu($submenu);
				break;

			case 'familybook':
				//-- familybook
				$link = 'familybook.php?ged='.WT_GEDURL;
				if ($rootid) $link .= "&amp;pid={$rootid}&amp;show_full={$showFull}";
				$submenu = new WT_Menu(WT_I18N::translate('Family book chart'), $link);
				$submenu->addIcon('fambook');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_fambook');
				$menu->addSubmenu($submenu);
				break;

			case 'timeline':
				//-- timeline
				$link = 'timeline.php?ged='.WT_GEDURL;
				if ($rootid) $link .= '&amp;pids[]='.$rootid;
				$submenu = new WT_Menu(WT_I18N::translate('Timeline chart'), $link);
				$submenu->addIcon('timeline');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_timeline');
				$menu->addSubmenu($submenu);
				if (isset($controller) && !empty($controller->family)) {
					// Build a sortable list of submenu items and then sort it in localized name order
					$menuList = array();
					$menuList['parentTimeLine'] = WT_I18N::translate('Show couple on timeline chart');
					$menuList['childTimeLine'] = WT_I18N::translate('Show children on timeline chart');
					$menuList['familyTimeLine'] = WT_I18N::translate('Show family on timeline chart');
					asort($menuList);

					// Produce the submenus in localized name order
					foreach ($menuList as $menuType => $menuName) {
						switch ($menuType) {
						case 'parentTimeLine':
							// charts / parents_timeline
							$submenu = new WT_Menu(WT_I18N::translate('Show couple on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE')).'&amp;ged='.WT_GEDURL);
							if (!empty($WT_IMAGES['timeline'])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass('submenuitem', 'submenuitem_hover');
							$menu->addSubmenu($submenu);
							break;

						case 'childTimeLine':
							// charts / children_timeline
							$submenu = new WT_Menu(WT_I18N::translate('Show children on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('CHIL')).'&amp;ged='.WT_GEDURL);
							if (!empty($WT_IMAGES['timeline'])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass('submenuitem', 'submenuitem_hover');
							$menu->addSubmenu($submenu);
							break;

						case 'familyTimeLine':
							// charts / family_timeline
							$submenu = new WT_Menu(WT_I18N::translate('Show family on timeline chart'), 'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE','CHIL')).'&amp;ged='.WT_GEDURL);
							if (!empty($WT_IMAGES['timeline'])) {
								$submenu->addIcon('timeline');
							}
							$submenu->addClass('submenuitem', 'submenuitem_hover');
							$menu->addSubmenu($submenu);
							break;

						}
					}
				}

				break;

			case 'lifespan':
				//-- lifespan
				$link = 'lifespan.php?ged='.WT_GEDURL;
				if ($rootid) $link .= "&amp;pids[]={$rootid}&amp;addFamily=1";
				$submenu = new WT_Menu(WT_I18N::translate('Lifespan chart'), $link);
				$submenu->addIcon('timeline');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_timeline');
				$menu->addSubmenu($submenu);
				break;

			case 'relationship':
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
										WT_I18N::translate('Relationship chart').': '.PrintReady($person->getFullName()),
										"relationship.php?pid1={$pid2}&amp;pid2={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
									);
								} else {
									$submenu = new WT_Menu(
										WT_I18N::translate('Relationship chart'),
										"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
									);
								}
								$submenu->addIcon('relationship');
								$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_relationship');
								$menu->addSubmenu($submenu);
							} else {
								$submenu = new WT_Menu(
									WT_I18N::translate('Relationship chart'),
									"relationship.php?pid1={$pid1}&amp;pretty=2&amp;followspouse=1&amp;ged=".WT_GEDURL
								);
								$submenu->addIcon('relationship');
								$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_relationship');
								$menu->addSubmenu($submenu);
								break;
							}
						}
					}
				}
				break;

			case 'statistics':
				//-- statistics plot
				$submenu = new WT_Menu(WT_I18N::translate('Statistics'), 'statistics.php?ged='.WT_GEDURL);
				$submenu->addIcon('statistic');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_statistic');
				$menu->addSubmenu($submenu);
				break;

			case 'tree':
				//-- interactive tree
				$link = 'module.php?mod=tree&amp;mod_action=treeview&amp;ged='.WT_GEDURL.'&amp;rootid='.$rootid;
				$submenu = new WT_Menu(WT_I18N::translate('Interactive tree'), $link);
				$submenu->addIcon('tree');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_gedcom');
				$menu->addSubmenu($submenu);
				break;

			//added for pedigree_map
			case 'pedigree_map':
				//-- pedigree map
				$link = 'module.php?ged='.WT_GEDURL.'&amp;mod=googlemap&amp;mod_action=pedigree_map';
				if ($rootid) $link .= '&amp;rootid='.$rootid;
				$submenu = new WT_Menu(WT_I18N::translate('Pedigree Map'), $link);
				global $WT_IMAGES;
				$WT_IMAGES['pedigree_map']=WT_MODULES_DIR.'googlemap/images/pedigree_map.gif';
				$submenu->addIcon('pedigree_map');
				$submenu->addClass('submenuitem', 'submenuitem_hover');
				$menu->addSubmenu($submenu);
				break;
			//end of added for pedigree_map
			}
		}
		return $menu;
	}

	public static function getListsMenu() {
		global $WT_IMAGES, $MULTI_MEDIA, $SEARCH_SPIDER, $controller;

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

		// The top level menu shows the individual list
		$menu=new WT_Menu(WT_I18N::translate('Lists'), 'indilist.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('lists');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_indis');
 
		// Search engines only get the individual list
		if ($SEARCH_SPIDER) {
			return $menu;
		}

		// Build a list of submenu items and then sort it in localized name order
		$menuList=array(
			'branches.php'  =>WT_I18N::translate('Branches'),
			'famlist.php'   =>WT_I18N::translate('Families'),
			'indilist.php'  =>WT_I18N::translate('Individuals'),
			'medialist.php' =>WT_I18N::translate('Multimedia'),
			'placelist.php' =>WT_I18N::translate('Place hierarchy'),
			'repolist.php'  =>WT_I18N::translate('Repositories'),
			'notelist.php'  =>WT_I18N::translate('Shared notes'),
			'sourcelist.php'=>WT_I18N::translate('Sources')
		);
		asort($menuList);

		foreach ($menuList as $page=>$name) {
			$link=$page.'?ged='.WT_GEDURL;
			switch ($page) {
			case 'indilist.php':
				if ($surname) $link .= '&amp;surname='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('indis');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_indis');
				$menu->addSubmenu($submenu);
				break;

			case 'famlist.php':
				if ($surname) $link .= '&amp;surname='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('cfamily');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_cfamily');
				$menu->addSubmenu($submenu);
				break;

			case 'branches.php':
				if ($surname) $link .= '&amp;surn='.$surname;
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('patriarch');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_patriarch');
				$menu->addSubmenu($submenu);
				break;

			case 'sourcelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_source');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_source');
				$menu->addSubmenu($submenu);
				break;

			case 'notelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_note');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_notes');
				$menu->addSubmenu($submenu);
				break;

			case 'repolist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('menu_repository');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_repository');
				$menu->addSubmenu($submenu);
				break;

			case 'placelist.php':
				$submenu = new WT_Menu($name, $link);
				$submenu->addIcon('place');
				$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_place');
				$menu->addSubmenu($submenu);
				break;

			case 'medialist.php':
				if ($MULTI_MEDIA) {
					$submenu = new WT_Menu($name, $link);
					$submenu->addIcon('menu_media');
					$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_media');
					$menu->addSubmenu($submenu);
				}
				break;
			}
		}

		return $menu;
	}

	public static function getCalendarMenu() {
		global $WT_IMAGES, $SEARCH_SPIDER;

		if ((!file_exists(WT_ROOT.'calendar.php')) || (!empty($SEARCH_SPIDER))) {
			$menu = new WT_Menu('', '', '');
			return $menu;
		}
		//-- main calendar menu item
		$menu = new WT_Menu(WT_I18N::translate('Calendar'), 'calendar.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('calendar');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_calendar');
		//-- viewday sub menu
		$submenu = new WT_Menu(WT_I18N::translate('View Day'), 'calendar.php?ged='.WT_GEDURL);
		$submenu->addIcon('calendar');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_calendar');
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new WT_Menu(WT_I18N::translate('View Month'), 'calendar.php?ged='.WT_GEDURL.'&amp;action=calendar');
		$submenu->addIcon('calendar');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_calendar');
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new WT_Menu(WT_I18N::translate('View Year'), 'calendar.php?ged='.WT_GEDURL.'&amp;action=year');
		$submenu->addIcon('calendar');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_calendar');
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return WT_Menu the menu item
	*/
	public static function getReportsMenu($pid='', $famid='') {
		global $WT_IMAGES, $SEARCH_SPIDER, $controller;

		$active_reports=WT_Module::getActiveReports();
		if ($SEARCH_SPIDER || !$active_reports) {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('Reports'), 'reportengine.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('reports');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_reports');

		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	public static function getSearchMenu() {
		global $WT_IMAGES, $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}
		//-- main search menu item
		$menu = new WT_Menu(WT_I18N::translate('Search'), 'search.php?ged='.WT_GEDURL, 'down');
		$menu->addIcon('search');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_search');
		//-- search_general sub menu
		$submenu = new WT_Menu(WT_I18N::translate('General Search'), 'search.php?ged='.WT_GEDURL);
		$submenu->addIcon('search');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_search');
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Soundex Search'), 'search.php?ged='.WT_GEDURL.'&amp;action=soundex');
		$submenu->addIcon('search');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_search');
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new WT_Menu(WT_I18N::translate('Advanced search'), 'search_advanced.php?ged='.WT_GEDURL);
		$submenu->addIcon('search');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_search');
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Search and replace'), 'search.php?ged='.WT_GEDURL.'&amp;action=replace');
			$submenu->addIcon('search');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_search');
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}

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

	public static function getHelpMenu() {
		global $WT_IMAGES, $SEARCH_SPIDER, $helpindex, $action;

		if (!empty($SEARCH_SPIDER)) {
			return null;
		}
		//-- main help menu item
		$menu = new WT_Menu(WT_I18N::translate('Help'), '#', 'down');
		$menu->addIcon('menu_help');
		if (empty($helpindex))
			$menu->addOnclick("return helpPopup('".WT_SCRIPT_NAME."');");
		else
			$menu->addOnclick("return helpPopup('".$helpindex."');");
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_help');

		//-- help_contents sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Help contents'), '#');
		$submenu->addIcon('help');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_help');
		$submenu->addOnclick("return helpPopup('help_contents_help');");
		$menu->addSubmenu($submenu);
		//-- faq sub menu
		if (array_key_exists('faq', WT_Module::getActiveModules()) && WT_DB::prepare("SELECT COUNT(*) FROM `##block` WHERE module_name='faq'")->fetchOne()) {

			$submenu = new WT_Menu(WT_I18N::translate('FAQ'), 'module.php?mod=faq&mod_action=show');
			$submenu->addIcon('help');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_help');
			$menu->addSubmenu($submenu);
		}
		//-- add wiki links
		$menu->addSeparator();
		$submenu = new WT_Menu(WT_I18N::translate('Wiki Main Page'), WT_WEBTREES_WIKI.'" target="_blank');
		$submenu->addIcon('wiki');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_wiki');
		$menu->addSubmenu($submenu);

		//-- add contact links to help menu
		$menu->addSeparator();
		$menuitems = contact_menus();
		foreach ($menuitems as $menuitem) {
			$submenu = new WT_Menu($menuitem['label'], $menuitem['link']);
			$submenu->addIcon('mypage');
			$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_contact');
			if (!empty($menuitem['onclick'])) $submenu->addOnclick($menuitem['onclick']);
			$menu->addSubmenu($submenu);
		}
		//-- add show/hide context_help
		$menu->addSeparator();
		if ($_SESSION['show_context_help'])
			$submenu = new WT_Menu(WT_I18N::translate('Hide contextual help'), get_query_url(array('show_context_help'=>'no')));
		else
			$submenu = new WT_Menu(WT_I18N::translate('Show contextual help'), get_query_url(array('show_context_help'=>'yes')));
		$submenu->addIcon('help');
		$submenu->addClass('submenuitem', 'submenuitem_hover', '', 'icon_small_menu_help');
		$menu->addSubmenu($submenu);
		return $menu;
	}

	public static function getThemeMenu() {
		global $SEARCH_SPIDER, $ALLOW_THEME_DROPDOWN;

		if ($ALLOW_THEME_DROPDOWN && !$SEARCH_SPIDER && get_site_setting('ALLOW_USER_THEMES')) {
			$menu=new WT_Menu(WT_I18N::translate('Theme'));
			$menu->addClass('thememenuitem', 'thememenuitem_hover', 'themesubmenu', 'icon_small_theme');
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

	public static function getLanguageMenu() {
		global $WT_IMAGES;

		$menu=new WT_Menu(WT_I18N::translate('Language'), '#', 'down');
		$menu->addClass('langmenuitem', 'langmenuitem_hover', 'submenu', 'icon_language');

		foreach (WT_I18N::installed_languages() as $lang=>$name) {
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

	public static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $GEDCOM, $WT_IMAGES;
		global $SEARCH_SPIDER;
		global $controller; // Pages with a controller can be added to the favorites

		$menu=new WT_Menu(WT_I18N::translate('Favorites'), '#', 'down');
		$menu->addIcon('gedcom');
		$menu->addClass('menuitem', 'menuitem_hover', 'submenu', 'icon_large_gedcom');

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
				$submenu=new WT_Menu('<strong>'.WT_I18N::translate('My Favorites').'</strong>');
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
					$submenu=new WT_Menu('<em>'.WT_I18N::translate('Add to My Favorites').'</em>', get_query_url(array('action'=>'addfav', 'gid'=>$gid)));
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
			$submenu=new WT_Menu('<strong>'.WT_I18N::translate('This GEDCOM\'s Favorites').'</strong>');
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
