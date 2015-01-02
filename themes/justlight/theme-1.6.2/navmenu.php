<?php

/*
 * 	Main navigation menu for the JustLight theme
 *  
 *  webtrees: Web based Family History software
 *  Copyright (C) 2014 webtrees development team.
 *  Copyright (C) 2014 JustCarmen.
 * 
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

use WT\Auth;

class JL_NavMenu {

	private static function getNavMenu($label, $link, $id, $navmenu) {
		return '<li id="' . $id . '-nav" class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $label . '<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li id="' . $id . '"><a href="' . $link . '">' . $label . '</a></li>
						<li class="divider"></li>' .
			self::getNavSubMenu($navmenu) . '
					</ul>
				</li>';
	}

	private static function getNavSubMenu($navmenu) {
		switch ($navmenu) {
			case 'gedcom' : return self::getGedcomSubMenu();
			case 'mypage' : return self::getMyPageSubMenu();
			case 'charts' : return self::getChartsSubMenu();
			case 'lists' : return self::getListsSubMenu();
			case 'calendar' : return self::getCalendarSubMenu();
			case 'reports' : return self::getReportsSubMenu();
			case 'search' : return self::getSearchSubMenu();
		}
	}

	public static function getGedcomMenu() {
		return self::getNavMenu(
				WT_I18N::translate('Home page'), 'index.php?ctype=gedcom&amp;ged=' . WT_GEDURL, 'menu-tree', 'gedcom'
		);
	}

	public static function getGedcomSubMenu() {
		$menu				 = '';
		$ALLOW_CHANGE_GEDCOM = WT_Site::getPreference('ALLOW_CHANGE_GEDCOM') && count(WT_Tree::getAll()) > 1;
		foreach (WT_Tree::getAll() as $tree) {
			if ($tree->tree_id == WT_GED_ID || $ALLOW_CHANGE_GEDCOM) {
				$submenu = new WT_Menu(
					$tree->tree_title_html, 'index.php?ctype=gedcom&amp;ged=' . $tree->tree_name_url, 'menu-tree-' . $tree->tree_id // Cannot use name - it must be a CSS identifier
				);
				$menu.=$submenu->getMenuAsList();
			}
		}
		return $menu;
	}

	public static function getMyPageMenu() {
		if (!Auth::id()) {
			return null;
		}
		return self::getNavMenu(
				WT_I18N::translate('My page'), 'index.php?ctype=user&amp;ged=' . WT_GEDURL, 'menu-mypage', 'mypage'
		);
	}

	public static function getMyPageSubMenu() {
		global $PEDIGREE_FULL_DETAILS, $PEDIGREE_LAYOUT;

		$showFull	 = ($PEDIGREE_FULL_DETAILS) ? 1 : 0;
		$showLayout	 = ($PEDIGREE_LAYOUT) ? 1 : 0;

		$menu = '';
		//-- editaccount submenu
		if (Auth::user()->getPreference('editaccount')) {
			$submenu = new WT_Menu(WT_I18N::translate('My account'), 'edituser.php', 'menu-myaccount');
			$menu.=$submenu->getMenuAsList();
		}
		if (WT_USER_GEDCOM_ID) {
			//-- my_pedigree submenu
			$submenu = new WT_Menu(
				WT_I18N::translate('My pedigree'), 'pedigree.php?ged=' . WT_GEDURL . '&amp;rootid=' . WT_USER_GEDCOM_ID . "&amp;show_full={$showFull}&amp;talloffset={$showLayout}", 'menu-mypedigree'
			);
			$menu.=$submenu->getMenuAsList();
			//-- my_indi submenu
			$submenu = new WT_Menu(WT_I18N::translate('My individual record'), 'individual.php?pid=' . WT_USER_GEDCOM_ID . '&amp;ged=' . WT_GEDURL, 'menu-myrecord');
			$menu.=$submenu->getMenuAsList();
		}
		if (WT_USER_GEDCOM_ADMIN) {
			//-- admin submenu
			$submenu = new WT_Menu(WT_I18N::translate('Administration'), 'admin.php', 'menu-admin');
			$menu.=$submenu->getMenuAsList();
		}
		return $menu;
	}

	public static function getChartsMenu() {
		global $SEARCH_SPIDER, $controller;

		if ($SEARCH_SPIDER || !WT_GED_ID) {
			return null;
		}

		$indi_xref = $controller->getSignificantIndividual()->getXref();

		return self::getNavMenu(
				WT_I18N::translate('Charts'), 'pedigree.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart', 'charts'
		);
	}

	public static function getChartsSubMenu() {
		global $controller;
		$indi_xref = $controller->getSignificantIndividual()->getXref();

		// Build a sortable list of submenu items and then sort it in localized name order
		$menuList = array(
			'pedigree'		 => WT_I18N::translate('Pedigree'),
			'descendancy'	 => WT_I18N::translate('Descendants'),
			'ancestry'		 => WT_I18N::translate('Ancestors'),
			'compact'		 => WT_I18N::translate('Compact tree'),
			'hourglass'		 => WT_I18N::translate('Hourglass chart'),
			'familybook'	 => WT_I18N::translate('Family book'),
			'timeline'		 => WT_I18N::translate('Timeline'),
			'lifespan'		 => WT_I18N::translate('Lifespans'),
			'relationship'	 => WT_I18N::translate('Relationships'),
			'statistics'	 => WT_I18N::translate('Statistics'),
		);
		if (function_exists('imagettftext')) {
			$menuList['fanchart'] = WT_I18N::translate('Fan chart');
		}
		// TODO: Use WT_Module_Chart ??
		if (array_key_exists('tree', WT_Module::getActiveModules())) {
			$menuList['tree'] = WT_I18N::translate('Interactive tree');
		}
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			$menuList['pedigree_map'] = WT_I18N::translate('Pedigree map');
		}
		asort($menuList);

		// Produce the submenus in localized name order
		$menu = '';
		foreach ($menuList as $menuType => $menuName) {
			switch ($menuType) {
				case 'pedigree':
					$submenu = new WT_Menu($menuName, 'pedigree.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-pedigree');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'descendancy':
					$submenu = new WT_Menu($menuName, 'descendancy.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-descendancy');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'ancestry':
					$submenu = new WT_Menu($menuName, 'ancestry.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-ancestry');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'compact':
					$submenu = new WT_Menu($menuName, 'compact.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-compact');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'fanchart':
					$submenu = new WT_Menu($menuName, 'fanchart.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-fanchart');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'hourglass':
					$submenu = new WT_Menu($menuName, 'hourglass.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-hourglass');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'familybook':
					$submenu = new WT_Menu($menuName, 'familybook.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL, 'menu-chart-familybook');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'timeline':
					$class = $controller instanceof WT_Controller_Family && $controller->record ? ' class="dropdown-submenu"' : '';
					$menu.= '<li id="menu-chart-timeline"' . $class . '>
								<a href="timeline.php?pids%5B%5D=' . $indi_xref . '&amp;ged=' . WT_GEDURL . '">' .
						$menuName . '
								</a>';

					if ($controller instanceof WT_Controller_Family && $controller->record) {
						// Build a sortable list of submenu items and then sort it in localized name order
						$menuList					 = array();
						$menuList['parentTimeLine']	 = WT_I18N::translate('Show couple on timeline chart');
						$menuList['childTimeLine']	 = WT_I18N::translate('Show children on timeline chart');
						$menuList['familyTimeLine']	 = WT_I18N::translate('Show family on timeline chart');
						asort($menuList);

						// Produce the submenus in localized name order
						$menu.='<ul class="dropdown-menu">';
						foreach ($menuList as $submenuType => $submenuName) {
							switch ($submenuType) {
								case 'parentTimeLine':
									// charts / parents_timeline
									$menu.= '<li id="menu-chart-timeline-parents">
										<a href="timeline.php?' . $controller->getTimelineIndis(array('HUSB', 'WIFE')) . '&amp;ged=' . WT_GEDURL . '">' . $submenuName . '</a>
									</li>';
									break;

								case 'childTimeLine':
									// charts / children_timeline
									$menu.= '<li id="menu-chart-timeline-children">
										<a href="timeline.php?' . $controller->getTimelineIndis(array('CHIL')) . '&amp;ged=' . WT_GEDURL . '">' . $submenuName . '</a>
									</li>';
									break;

								case 'familyTimeLine':
									// charts / family_timeline
									$menu.= '<li id="menu-chart-timeline-family">
										<a href="timeline.php?' . $controller->getTimelineIndis(array('HUSB', 'WIFE', 'CHIL')) . '&amp;ged=' . WT_GEDURL . '">' . $submenuName . '</a>
									</li>';
									break;
							}
						}
						$menu.='</ul></li>';
					}
					break;

				case 'lifespan':
					$submenu = new WT_Menu($menuName, 'lifespan.php?pids%5B%5D=' . $indi_xref . '&amp;addFamily=1&amp;ged=' . WT_GEDURL, 'menu-chart-lifespan');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'relationship':
					if ($indi_xref) {
						// Pages focused on a specific person - from the person, to me
						$pid1	 = WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID; ;
						$pid2	 = $indi_xref;
						if ($pid1 == $pid2) {
							$pid2 = '';
						}
						$submenu = new WT_Menu(
							WT_I18N::translate('Relationships'), 'relationship.php?pid1=' . $pid1 . '&amp;pid2=' . $pid2 . '&amp;ged=' . WT_GEDURL, 'menu-chart-relationship'
						);
					} else {
						// Regular pages - from me, to somebody
						$pid1	 = WT_USER_GEDCOM_ID ? WT_USER_GEDCOM_ID : WT_USER_ROOT_ID;
						$pid2	 = '';
						$submenu = new WT_Menu(
							WT_I18N::translate('Relationships'), 'relationship.php?pid1=' . $pid1 . '&amp;pid2=' . $pid2 . '&amp;ged=' . WT_GEDURL, 'menu-chart-relationship'
						);
					}
					$menu .= $submenu->getMenuAsList();
					break;

				case 'statistics':
					$submenu = new WT_Menu($menuName, 'statistics.php?ged=' . WT_GEDURL, 'menu-chart-statistics');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'tree':
					$submenu = new WT_Menu($menuName, 'module.php?mod=tree&amp;mod_action=treeview&amp;ged=' . WT_GEDURL . '&amp;rootid=' . $indi_xref, 'menu-chart-tree');
					$menu.=$submenu->getMenuAsList();
					break;

				case 'pedigree_map':
					$submenu = new WT_Menu($menuName, 'module.php?ged=' . WT_GEDURL . '&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=' . $indi_xref, 'menu-chart-pedigree_map');
					$menu.=$submenu->getMenuAsList();
					break;
			}
		}
		return $menu;
	}

	public static function getListsMenu() {
		return self::getNavMenu(
				WT_I18N::translate('Lists'), 'indilist.php?ged=' . WT_GEDURL, 'menu-list', 'lists'
		);
	}

	public static function getListsSubMenu() {
		global $SEARCH_SPIDER, $controller;

		// Do not show empty lists
		$row = WT_DB::prepare(
				"SELECT SQL_CACHE" .
				" EXISTS(SELECT 1 FROM `##sources` WHERE s_file=?                  ) AS sour," .
				" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='REPO') AS repo," .
				" EXISTS(SELECT 1 FROM `##other`   WHERE o_file=? AND o_type='NOTE') AS note," .
				" EXISTS(SELECT 1 FROM `##media`   WHERE m_file=?                  ) AS obje"
			)->execute(array(WT_GED_ID, WT_GED_ID, WT_GED_ID, WT_GED_ID))->fetchOneRow();

		// Build a list of submenu items and then sort it in localized name order
		$surname_url = '&surname=' . rawurlencode($controller->getSignificantSurname()) . '&amp;ged=' . WT_GEDURL;

		$menulist = array(
			new WT_Menu(WT_I18N::translate('Individuals'), 'indilist.php?ged=' . WT_GEDURL . $surname_url, 'menu-list-indi'),
		);

		if (!$SEARCH_SPIDER) {
			$menulist[]	 = new WT_Menu(WT_I18N::translate('Families'), 'famlist.php?ged=' . WT_GEDURL . $surname_url, 'menu-list-fam');
			$menulist[]	 = new WT_Menu(WT_I18N::translate('Branches'), 'branches.php?ged=' . WT_GEDURL . $surname_url, 'menu-branches');
			$menulist[]	 = new WT_Menu(WT_I18N::translate('Place hierarchy'), 'placelist.php?ged=' . WT_GEDURL, 'menu-list-plac');
			if ($row->obje && !getThemeOption('media_menu')) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Media objects'), 'medialist.php?ged=' . WT_GEDURL, 'menu-list-obje');
			}
			if ($row->repo) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Repositories'), 'repolist.php?ged=' . WT_GEDURL, 'menu-list-repo');
			}
			if ($row->sour) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Sources'), 'sourcelist.php?ged=' . WT_GEDURL, 'menu-list-sour');
			}
			if ($row->note) {
				$menulist[] = new WT_Menu(WT_I18N::translate('Shared notes'), 'notelist.php?ged=' . WT_GEDURL, 'menu-list-note');
			}
		}
		uasort($menulist, function(WT_Menu $x, WT_Menu $y) { return WT_I18N::strcasecmp($x->getLabel(), $y->getLabel()); });

		$menu = '';
		foreach ($menulist as $submenu) {
			$menu .= $submenu->getMenuAsList();
		}

		return $menu;
	}

	public static function getCalendarMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}

		return self::getNavMenu(
				WT_I18N::translate('Calendar'), 'calendar.php?ged=' . WT_GEDURL, 'menu-calendar', 'calendar'
		);
	}

	public static function getCalendarSubMenu() {
		$menu	 = '';
		//-- viewday sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Day'), 'calendar.php?ged=' . WT_GEDURL, 'menu-calendar-day');
		$menu.=$submenu->getMenuAsList();
		//-- viewmonth sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Month'), 'calendar.php?ged=' . WT_GEDURL . '&amp;action=calendar', 'menu-calendar-month');
		$menu.=$submenu->getMenuAsList();
		//-- viewyear sub menu
		$submenu = new WT_Menu(WT_I18N::translate('Year'), 'calendar.php?ged=' . WT_GEDURL . '&amp;action=year', 'menu-calendar-year');
		$menu.=$submenu->getMenuAsList();
		return $menu;
	}

	/**
	 * get the reports menu
	 *
	 * @return WT_Menu the menu item
	 */
	public static function getReportsMenu() {
		global $SEARCH_SPIDER;

		$active_reports = WT_Module::getActiveReports();
		if ($SEARCH_SPIDER || !$active_reports) {
			return null;
		}

		return self::getNavMenu(
				WT_I18N::translate('Reports'), 'reportengine.php?ged=' . WT_GEDURL, 'menu-report', 'reports'
		);
	}

	public static function getReportsSubMenu() {

		$active_reports = WT_Module::getActiveReports();

		$menu = '';
		foreach ($active_reports as $report) {
			foreach ($report->getReportMenus() as $submenu) {
				$menu.=$submenu->getMenuAsList();
			}
		}
		return $menu;
	}

	public static function getSearchMenu() {
		global $SEARCH_SPIDER;

		if ($SEARCH_SPIDER) {
			return null;
		}

		return self::getNavMenu(
				WT_I18N::translate('Search'), 'search.php?ged=' . WT_GEDURL, 'menu-search', 'search'
		);
	}

	public static function getSearchSubMenu() {
		$menu	 = '';
		//-- search_general sub menu
		$submenu = new WT_Menu(WT_I18N::translate('General search'), 'search.php?ged=' . WT_GEDURL, 'menu-search-general');
		$menu.=$submenu->getMenuAsList();
		//-- search_soundex sub menu
		$submenu = new WT_Menu(/* I18N: search using “sounds like”, rather than exact spelling */ WT_I18N::translate('Phonetic search'), 'search.php?ged=' . WT_GEDURL . '&amp;action=soundex', 'menu-search-soundex');
		$menu.=$submenu->getMenuAsList();
		//-- advanced search
		$submenu = new WT_Menu(WT_I18N::translate('Advanced search'), 'search_advanced.php?ged=' . WT_GEDURL, 'menu-search-advanced');
		$menu.=$submenu->getMenuAsList();
		//-- search_replace sub menu
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Search and replace'), 'search.php?ged=' . WT_GEDURL . '&amp;action=replace', 'menu-search-replace');
			$menu.=$submenu->getMenuAsList();
		}
		return $menu;
	}

	public static function getModuleMenus() {
		$menus = array();
		foreach (WT_Module::getActiveMenus() as $module) {
			$menu = $module->getMenu();
			if ($menu) {
				if (count($menu->getSubmenus()) > 0) {
					$navmenu = '
						<li id="' . $menu->getId() . '-nav" class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $menu->getLabel() . '<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li id="' . $menu->getId() . '"><a href="' . $menu->getLink() . '">' . $menu->getLabel() . '</a></li>
							<li class="divider"></li>';
					foreach ($menu->getSubmenus() as $submenu) {
						$onclick = $submenu->getOnclick ? 'onclick="' . $submenu->getOnclick() . '"' : "";
						$navmenu.= '<li id="' . $submenu->getId() . '"><a href="' . $submenu->getLink() . '" ' . $onclick . '>' . $submenu->getLabel() . '</a></li>';
					}
					$navmenu.= '
						</ul>
					</li>';
				} else {
					$navmenu = '<li id="' . $menu->getId() . '"><a href="' . $menu->getLink() . '">' . $menu->getLabel() . '</a></li>';
				}
				$menus[] = $navmenu;
			}
		}
		$menus = implode('', $menus);
		return $menus;
	}

	// Theme options
	public static function getCompactMenu() {
		global $controller;

		$indi_xref	 = $controller->getSignificantIndividual()->getXref();
		$menu		 = '
			<li id="menu-view-nav" class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . WT_I18N::translate('View') . '<span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li id="menu-view"><a href="pedigree.php?rootid=' . $indi_xref . '&amp;ged=' . WT_GEDURL . '">' . WT_I18N::translate('View') . '</a></li>
					<li class="divider"></li>';

		$menu .= '
					<li id="menu-chart" class="dropdown-submenu">
						<a class="dropdown-submenu-toggle" href="#">' . WT_I18N::translate('Charts') . '<span class="right-caret"></span></a>
						<ul class="dropdown-menu sub-menu">' . self::getChartsSubMenu() . '</ul>
					</li>
					<li id="menu-list" class="dropdown-submenu">
						<a class="dropdown-submenu-toggle" href="#">' . WT_I18N::translate('Lists') . '<span class="right-caret"></span></a>
						<ul class="dropdown-menu sub-menu">' . self::getListsSubMenu() . '</ul>
					</li>';

		$active_reports = WT_Module::getActiveReports();
		if (getThemeOption('compact_menu_reports') == 1 && $active_reports) {
			$menu .= '
						<li id="menu-report" class="dropdown-submenu">
							<a class="dropdown-submenu-toggle" href="#">' . WT_I18N::translate('Reports') . '<span class="right-caret"></span></a>
							<ul class="dropdown-menu sub-menu">' . self::getReportsSubMenu() . '</ul>
						</li>';
		};

		$menu .= '
					<li id="menu-calendar" class="dropdown-submenu">
						<a class="dropdown-submenu-toggle" href="#">' . WT_I18N::translate('Calendar') . '<span class="right-caret"></span></a>
						<ul class="dropdown-menu sub-menu">' . self::getCalendarSubMenu() . '</ul>
					</li>';
		$menu .= '
				</ul>
			</li>';
		return $menu;
	}

	public static function getMediaMenu($folders) {
		global $MEDIA_DIRECTORY;

		$mainfolder	 = getThemeOption('media_link') == $MEDIA_DIRECTORY ? '' : '&amp;folder=' . rawurlencode(getThemeOption('media_link'));
		$subfolders	 = getThemeOption('subfolders') ? '&amp;subdirs=on' : '';

		$menu = '
			<li id="menu-media-nav" class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . WT_I18N::translate('Media') . '<span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li id="menu-media"><a href="medialist.php?action=filter&amp;search=no' . $mainfolder . '&amp;sortby=title&amp;' . $subfolders . '&amp;max=20&amp;columns=2">' . WT_I18N::translate('Media') . '</a></li>
					<li class="divider"></li>';
		foreach ($folders as $key => $folder) {
			if ($key !== $MEDIA_DIRECTORY) {
				$submenu = new WT_Menu(ucfirst($folder), 'medialist.php?action=filter&amp;search=no&amp;folder=' . rawurlencode($key) . '&amp;sortby=title&amp;' . $subfolders . '&amp;max=20&amp;columns=2', 'menu-media-' . $key);
				$menu.=$submenu->getMenuAsList();
			}
		}
		$menu .= '
				</ul>
			</li>';
		return $menu;
	}

	public static function getSingleModuleMenu($module) {
		$modulemenu	 = new $module;
		$menu		 = $modulemenu->getMenu();
		if ($menu) {
			if (count($menu->getSubmenus()) > 0) {
				$navmenu = '
					<li id="' . $menu->getId() . '-nav" class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $menu->getLabel() . '<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li id="' . $menu->getId() . '"><a href="' . $menu->getLink() . '">' . $menu->getLabel() . '</a></li>
						<li class="divider"></li>';
				foreach ($menu->getSubmenus() as $submenu) {
					$onclick = $submenu->getOnclick() ? 'onclick="' . $submenu->getOnclick() . '"' : "";
					$navmenu.= '<li id="' . $submenu->getId() . '"><a href="' . $submenu->getLink() . '" ' . $onclick . '>' . $submenu->getLabel() . '</a></li>';
				}
				$navmenu.= '
					</ul>
				</li>';
			} else {
				$navmenu = '<li id="' . $menu->getId() . '"><a href="' . $menu->getLink() . '">' . $menu->getLabel() . '</a></li>';
			}
			return $navmenu;
		}
	}

}
