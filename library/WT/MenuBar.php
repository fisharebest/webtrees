<?php
// System for generating menus.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
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
// // Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_MenuBar {
	public static function getGedcomMenu() {
		$menu = new WT_Menu(WT_I18N::translate('Home page'), 'index.php?ctype=gedcom&amp;ged='.WT_GEDURL, 'menu-tree');
		$ALLOW_CHANGE_GEDCOM=WT_Site::preference('ALLOW_CHANGE_GEDCOM') && count(WT_Tree::getAll())>1;
		foreach (WT_Tree::getAll() as $tree) {
			if ($tree->tree_id==WT_GED_ID || $ALLOW_CHANGE_GEDCOM) {
				$submenu = new WT_Menu(
					$tree->tree_title_html,
					'index.php?ctype=gedcom&amp;ged='.$tree->tree_name_url,
					'menu-tree-'.$tree->tree_id // Cannot use name - it must be a CSS identifier
				);
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	public static function getMyPageMenu() {
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;

		$showFull = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout = ($PEDIGREE_LAYOUT) ? 1 : 0;

		if (!WT_USER_ID) {
			return null;
		}

		//-- main menu
		$menu = new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;ged='.WT_GEDURL, 'menu-mymenu');

		//-- mypage submenu
		$submenu = new WT_Menu(WT_I18N::translate('My page'), 'index.php?ctype=user&amp;ged='.WT_GEDURL, 'menu-mypage');
		$menu->addSubmenu($submenu);
		//-- editaccount submenu
		if (get_user_setting(WT_USER_ID, 'editaccount')) {
			$submenu = new WT_Menu(WT_I18N::translate('My account'), 'edituser.php', 'menu-myaccount');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new WT_Menu(
				WT_I18N::translate('My pedigree'),
				'pedigree.php?ged='.WT_GEDURL.'&amp;rootid='.WT_USER_GEDCOM_ID."&amp;show_full={$showFull}&amp;talloffset={$showLayout}",
				'menu-mypedigree'
			);
			$menu->addSubmenu($submenu);
			//-- my_indi submenu
			$submenu = new WT_Menu(WT_I18N::translate('My individual record'), 'individual.php?pid='.WT_USER_GEDCOM_ID.'&amp;ged='.WT_GEDURL, 'menu-myrecord');
			$menu->addSubmenu($submenu);
		}
		if (WT_USER_GEDCOM_ADMIN) {
			//-- admin submenu
			$submenu = new WT_Menu(WT_I18N::translate('Administration'), 'admin.php', 'menu-admin');
			$menu->addSubmenu($submenu);
		}
		return $menu;
	}

	public static function getChartsMenu() {
		global $SEARCH_SPIDER, $controller;

		if ($SEARCH_SPIDER || !WT_GED_ID) {
			return null;
		}

		$indi_xref=$controller->getSignificantIndividual()->getXref();

		$menu = new WT_Menu(WT_I18N::translate('Charts'), 'pedigree.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart');

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array(
			'pedigree'    =>WT_I18N::translate('Pedigree'),
			'descendancy' =>WT_I18N::translate('Descendants'),
			'ancestry'    =>WT_I18N::translate('Ancestors'),
			'compact'     =>WT_I18N::translate('Compact tree'),
			'hourglass'   =>WT_I18N::translate('Hourglass chart'),
			'familybook'  =>WT_I18N::translate('Family book'),
			'timeline'    =>WT_I18N::translate('Timeline'),
			'lifespan'    =>WT_I18N::translate('Lifespans'),
			'relationship'=>WT_I18N::translate('Relationships'),
			'statistics'  =>WT_I18N::translate('Statistics'),
		);
		if (function_exists('imagettftext')) {
			$menuList['fanchart']=WT_I18N::translate('Fan chart');
		}
		// TODO: Use WT_Module_Chart ??
		if (array_key_exists('tree', WT_Module::getActiveModules())) {
			$menuList['tree']=WT_I18N::translate('Interactive tree');
		}
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			$menuList['pedigree_map']=WT_I18N::translate('Pedigree map');
		}
		asort($menuList);

		// Produce the submenus in localized name order
		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
			case 'pedigree':
				$submenu = new WT_Menu($menuName, 'pedigree.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-pedigree');
				$menu->addSubmenu($submenu);
				break;

			case 'descendancy':
				$submenu = new WT_Menu($menuName, 'descendancy.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-descendancy');
				$menu->addSubmenu($submenu);
				break;

			case 'ancestry':
				$submenu = new WT_Menu($menuName, 'ancestry.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-ancestry');
				$menu->addSubmenu($submenu);
				break;

			case 'compact':
				$submenu = new WT_Menu($menuName, 'compact.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-compact');
				$menu->addSubmenu($submenu);
				break;

			case 'fanchart':
				$submenu = new WT_Menu($menuName, 'fanchart.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-fanchart');
				$menu->addSubmenu($submenu);
				break;

			case 'hourglass':
				$submenu = new WT_Menu($menuName, 'hourglass.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-hourglass');
				$menu->addSubmenu($submenu);
				break;

			case 'familybook':
				$submenu = new WT_Menu($menuName, 'familybook.php?rootid='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-familybook');
				$menu->addSubmenu($submenu);
				break;

			case 'timeline':
				//-- timeline
				$link = 'timeline.php?ged='.WT_GEDURL;
				$submenu = new WT_Menu($menuName, 'timeline.php?pids%5B%5D='.$indi_xref.'&amp;ged='.WT_GEDURL, 'menu-chart-timeline');
				if ($controller instanceof WT_Controller_Family && $controller->record) {
					// Build a sortable list of submenu items and then sort it in localized name order
					$menuList = array();
					$menuList['parentTimeLine'] = WT_I18N::translate('Show couple on timeline chart');
					$menuList['childTimeLine'] = WT_I18N::translate('Show children on timeline chart');
					$menuList['familyTimeLine'] = WT_I18N::translate('Show family on timeline chart');
					asort($menuList);

					// Produce the submenus in localized name order
					foreach ($menuList as $submenuType => $submenuName) {
						switch ($submenuType) {
						case 'parentTimeLine':
							// charts / parents_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE')).'&amp;ged='.WT_GEDURL,
								'menu-chart-timeline-parents'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						case 'childTimeLine':
							// charts / children_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?'.$controller->getTimelineIndis(array('CHIL')).'&amp;ged='.WT_GEDURL,
								'menu-chart-timeline-children'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						case 'familyTimeLine':
							// charts / family_timeline
							$subsubmenu = new WT_Menu(
								$submenuName,
								'timeline.php?'.$controller->getTimelineIndis(array('HUSB','WIFE','CHIL')).'&amp;ged='.WT_GEDURL,
								'menu-chart-timeline-family'
							);
							$submenu->addSubmenu($subsubmenu);
							break;

						}
					}
				}
				$menu->addSubmenu($submenu);
				break;

			case 'lifespan':
				$submenu = new WT_Menu($menuName, 'lifespan.php?pids%5B%5D='.$indi_xref.'&amp;addFamily=1&amp;ged='.WT_GEDURL, 'menu-chart-lifespan');
				$menu->addSubmenu($submenu);
				break;

			case 'relationship':
				if ($indi_xref) {
					// Pages focused on a specific person - from the person, to me
					$pid1=WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID;;
					$pid2=$indi_xref;
					if ($pid1==$pid2) {
						$pid2='';
					}
					$submenu = new WT_Menu(
						WT_I18N::translate('Relationships'),
						'relationship.php?pid1='.$pid1.'&amp;pid2='.$pid2.'&amp;ged='.WT_GEDURL,
						'menu-chart-relationship'
					);
					if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
						// Add a submenu showing relationship from this person to each of our favorites
						foreach (user_favorites_WT_Module::getFavorites(WT_USER_ID) as $favorite) {
							if ($favorite['type']=='INDI' && $favorite['gedcom_id']==WT_GED_ID) {
								$person=WT_Individual::getInstance($favorite['gid']);
								if ($person instanceof WT_Individual) {
									$subsubmenu = new WT_Menu(
										$person->getFullName(),
										'relationship.php?pid1='.$person->getXref().'&amp;pid2='.$pid2.'&amp;ged='.WT_GEDURL,
										'menu-chart-relationship-'.$person->getXref().'-'.$pid2 // We don't use these, but a custom theme might
									);
									$submenu->addSubmenu($subsubmenu);
								}
							}
						}
					}
				} else {
					// Regular pages - from me, to somebody
					$pid1=WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID;
					$pid2='';
					$submenu = new WT_Menu(
						WT_I18N::translate('Relationships'),
						'relationship.php?pid1='.$pid1.'&amp;pid2='.$pid2.'&amp;ged='.WT_GEDURL,
						'menu-chart-relationship'
					);
				}
				$menu->addSubmenu($submenu);
				break;

			case 'statistics':
				$submenu = new WT_Menu($menuName, 'statistics.php?ged='.WT_GEDURL, 'menu-chart-statistics');
				$menu->addSubmenu($submenu);
				break;

			case 'tree':
				$submenu = new WT_Menu($menuName, 'module.php?mod=tree&amp;mod_action=treeview&amp;ged='.WT_GEDURL.'&amp;rootid='.$indi_xref, 'menu-chart-tree');
				$menu->addSubmenu($submenu);
				break;

			case 'pedigree_map':
				$submenu = new WT_Menu($menuName, 'module.php?ged='.WT_GEDURL.'&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid='.$indi_xref, 'menu-chart-pedigree_map');
				$menu->addSubmenu($submenu);
				break;
			}
		}
		return $menu;
	}

	public static function getListsMenu() {
		global $SEARCH_SPIDER, $controller;

		// The top level menu shows the individual list
		$menu=new WT_Menu(WT_I18N::translate('Lists'), 'indilist.php?ged='.WT_GEDURL, 'menu-list');

		// Do not show empty lists
		$row=WT_DB::prepare(
			"SELECT SQL_CACHE".
			" EXISTS(SELECT 1 FROM `##sources` WHERE s_file=?                  ) AS sour,".
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='REPO') AS repo,".
			" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='NOTE') AS note,".
			" EXISTS(SELECT 1 FROM `##media`   WHERE m_file=?                  ) AS obje"
		)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchOneRow();

		// Build a list of submenu items and then sort it in localized name order
		$menulist=array('indilist.php'  =>WT_I18N::translate('Individuals'));
		if (!$SEARCH_SPIDER) {
			$menulist['famlist.php'  ]=WT_I18N::translate('Families');
			$menulist['branches.php' ]=WT_I18N::translate('Branches');
			$menulist['placelist.php']=WT_I18N::translate('Place hierarchy');
			// Build a list of submenu items and then sort it in localized name order
			if ($row->obje) {
				$menulist['medialist.php']=WT_I18N::translate('Media objects');
			}
			if ($row->repo) {
				$menulist['repolist.php']=WT_I18N::translate('Repositories');
			}
			if ($row->sour) {
				$menulist['sourcelist.php']=WT_I18N::translate('Sources');
			}
			if ($row->note) {
				$menulist['notelist.php']=WT_I18N::translate('Shared notes');
			}
		}
		asort($menulist);

		$surname_url='?surname='.rawurlencode($controller->getSignificantSurname()).'&amp;ged='.WT_GEDURL;
		foreach ($menulist as $page=>$name) {
			switch ($page) {
			case 'indilist.php':
				$submenu = new WT_Menu($name, $page.$surname_url, 'menu-list-indi');
				$menu->addSubmenu($submenu);
				break;

			case 'famlist.php':
				$submenu = new WT_Menu($name, $page.$surname_url, 'menu-list-fam');
				$menu->addSubmenu($submenu);
				break;

			case 'branches.php':
				$submenu = new WT_Menu($name, $page.$surname_url, 'menu-branches');
				$menu->addSubmenu($submenu);
				break;

			case 'sourcelist.php':
				$submenu = new WT_Menu($name, $page.'?ged='.WT_GEDURL, 'menu-list-sour');
				$menu->addSubmenu($submenu);
				break;

			case 'notelist.php':
				$submenu = new WT_Menu($name, $page.'?ged='.WT_GEDURL, 'menu-list-note');
				$menu->addSubmenu($submenu);
				break;

			case 'repolist.php':
				$submenu = new WT_Menu($name, $page.'?ged='.WT_GEDURL, 'menu-list-repo');
				$menu->addSubmenu($submenu);
				break;

			case 'placelist.php':
				$submenu = new WT_Menu($name, $page.'?ged='.WT_GEDURL, 'menu-list-plac');
				$menu->addSubmenu($submenu);
				break;

			case 'medialist.php':
				$submenu = new WT_Menu($name, $page.'?ged='.WT_GEDURL, 'menu-list-obje');
				$menu->addSubmenu($submenu);
				break;
			}
		}

		return $menu;
	}

	public static function getCalendarMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}
		//-- main calendar menu item
		$menu = new WT_Menu(WT_I18N::translate('Calendar'), 'calendar.php?ged='.WT_GEDURL, 'menu-calendar');
		//-- viewday sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Day'), 'calendar.php?ged='.WT_GEDURL, 'menu-calendar-day');
		$menu->addSubmenu($submenu);
		//-- viewmonth sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Month'), 'calendar.php?ged='.WT_GEDURL.'&amp;action=calendar', 'menu-calendar-month');
		$menu->addSubmenu($submenu);
		//-- viewyear sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Year'), 'calendar.php?ged='.WT_GEDURL.'&amp;action=year', 'menu-calendar-year');
		$menu->addSubmenu($submenu);
		return $menu;
	}

	/**
	* get the reports menu
	* @return WT_Menu the menu item
	*/
	public static function getReportsMenu($pid='', $famid='') {
		global $SEARCH_SPIDER;

		$active_reports=WT_Module::getActiveReports();
		if ($SEARCH_SPIDER || !$active_reports) {
			return null;
		}

		$menu = new WT_Menu(WT_I18N::translate('Reports'), 'reportengine.php?ged='.WT_GEDURL, 'menu-report');

		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu->addSubmenu($submenu);
			}
		}
		return $menu;
	}

	public static function getSearchMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}
		//-- main search menu item
		$menu = new WT_Menu(WT_I18N::translate('Search'), 'search.php?ged='.WT_GEDURL, 'menu-search');
		//-- search_general sub menu
		$submenu = new WT_Menu(WT_I18N::translate('General search'), 'search.php?ged='.WT_GEDURL, 'menu-search-general');
		$menu->addSubmenu($submenu);
		//-- search_soundex sub menu
		$submenu = new WT_Menu(/* I18N: search using “sounds like”, rather than exact spelling */ WT_I18N::translate('Phonetic search'), 'search.php?ged='.WT_GEDURL.'&amp;action=soundex', 'menu-search-soundex');
		$menu->addSubmenu($submenu);
		//-- advanced search
		$submenu = new WT_Menu(WT_I18N::translate('Advanced search'), 'search_advanced.php?ged='.WT_GEDURL, 'menu-search-advanced');
		$menu->addSubmenu($submenu);
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Search and replace'), 'search.php?ged='.WT_GEDURL.'&amp;action=replace', 'menu-search-replace');
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

	public static function getThemeMenu() {
		global $SEARCH_SPIDER;

		if (WT_GED_ID && !$SEARCH_SPIDER && WT_Site::preference('ALLOW_USER_THEMES') && get_gedcom_setting(WT_GED_ID, 'ALLOW_THEME_DROPDOWN')) {
			$menu=new WT_Menu(WT_I18N::translate('Theme'), '#', 'menu-theme');
			foreach (get_theme_names() as $themename=>$themedir) {
				$submenu=new WT_Menu($themename, get_query_url(array('theme'=>$themedir), '&amp;'), 'menu-theme-'.$themedir);
				if (WT_THEME_DIR == 'themes/'.$themedir.'/') {$submenu->addClass('','','theme-active');}
				$menu->addSubMenu($submenu);
			}
			return $menu;
		} else {
			return null;
		}
	}

	public static function getLanguageMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		} else {
			$menu=new WT_Menu(WT_I18N::translate('Language'), '#', 'menu-language');

			foreach (WT_I18N::installed_languages() as $lang=>$name) {
				$submenu=new WT_Menu($name, get_query_url(array('lang'=>$lang), '&amp;'), 'menu-language-'.$lang);
				if (WT_LOCALE == $lang) {$submenu->addClass('','','lang-active');}
				$menu->addSubMenu($submenu);
			}
			if (count($menu->submenus)>1) {
				return $menu;
			} else {
				return null;
			}
		}
	}

	public static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $controller, $SEARCH_SPIDER;

		$show_user_favs=WT_USER_ID               && array_key_exists('user_favorites',   WT_Module::getActiveModules());
		$show_gedc_favs=!$REQUIRE_AUTHENTICATION && array_key_exists('gedcom_favorites', WT_Module::getActiveModules());

		if ($show_user_favs && !$SEARCH_SPIDER) {
			if ($show_gedc_favs && !$SEARCH_SPIDER) {
				$favorites=array_merge(
					gedcom_favorites_WT_Module::getFavorites(WT_GED_ID),
					user_favorites_WT_Module::getFavorites(WT_USER_ID)
				);
			} else {
				$favorites=user_favorites_WT_Module::getFavorites(WT_USER_ID);
			}
		} else {
			if ($show_gedc_favs && !$SEARCH_SPIDER) {
				$favorites=gedcom_favorites_WT_Module::getFavorites(WT_GED_ID);
			} else {
				return null;
			}
		}
		// Sort $favorites alphabetically?

		$menu=new WT_Menu(WT_I18N::translate('Favorites'), '#', 'menu-favorites');

		foreach ($favorites as $favorite) {
			switch($favorite['type']) {
			case 'URL':
				$submenu=new WT_Menu($favorite['title'], $favorite['url']);
				$menu->addSubMenu($submenu);
				break;
			case 'INDI':
			case 'FAM':
			case 'SOUR':
			case 'OBJE':
			case 'NOTE':
				$obj=WT_GedcomRecord::getInstance($favorite['gid']);
				if ($obj && $obj->canShowName()) {
					$submenu=new WT_Menu($obj->getFullName(), $obj->getHtmlUrl());
					$menu->addSubMenu($submenu);
				}
				break;
			}
		}

		if ($show_user_favs) {
			if (isset($controller->record) && $controller->record instanceof WT_GedcomRecord) {
				$submenu=new WT_Menu(WT_I18N::translate('Add to favorites'), '#');
				$submenu->addOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'".$controller->record->getXref()."'},function(){location.reload();})");
				$menu->addSubMenu($submenu);
			}
		}
		return $menu;
	}
}
