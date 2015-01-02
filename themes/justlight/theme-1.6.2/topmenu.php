<?php

/*
 * 	Top navigation menu for the JustLight theme
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

class JL_TopMenu {

	public static function getTopMenu($label, $topmenu) {
		return '<div class="btn-group">
					<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">' . $label . '<span class="caret"></span></button>
						<ul class="dropdown-menu">' .
			self::getSubMenu($label, $topmenu) . '
						</ul>
					</div>';
	}

	private static function getSubMenu($label, $topmenu) {
		global $SEARCH_SPIDER;
		$menu = '';
		switch ($topmenu) {
			case 'themes':
				foreach (get_theme_names() as $themename => $themedir) {
					$class = WT_THEME_DIR == 'themes/' . $themedir . '/' ? 'disabled' : '';
					$menu.='<li id="menu-theme-' . $themedir . '" class="' . $class . '">
								<a href="' . get_query_url(array('theme' => $themedir), '&amp;') . '">' . $themename . '</a>
							</li>';
				}
				break;
			case 'languages':
				if ($SEARCH_SPIDER) {
					return null;
				} else {
					foreach (WT_I18N::installed_languages() as $lang => $name) {
						$class = WT_LOCALE == $lang ? 'disabled' : '';
						$menu.='<li id="menu-language-' . $lang . '" class="' . $class . '">
								<a href="' . get_query_url(array('lang' => $lang), '&amp;') . '">' . $name . '</a>
							</li>';
					}
				}
				break;
			case 'login':
				$menu.='<li><a href="edituser.php">' . $label . '</a></li>
						<li>' . logout_link() . '</li>';
				break;
		}
		return $menu;
	}

	public static function getFavoritesMenu() {
		global $REQUIRE_AUTHENTICATION, $controller, $SEARCH_SPIDER;

		$show_user_favs	 = Auth::check() && array_key_exists('user_favorites', WT_Module::getActiveModules());
		$show_gedc_favs	 = !$REQUIRE_AUTHENTICATION && array_key_exists('gedcom_favorites', WT_Module::getActiveModules());

		if ($show_user_favs && !$SEARCH_SPIDER) {
			if ($show_gedc_favs && !$SEARCH_SPIDER) {
				$favorites = array_merge(
					gedcom_favorites_WT_Module::getFavorites(WT_GED_ID), user_favorites_WT_Module::getFavorites(Auth::id())
				);
			} else {
				$favorites = user_favorites_WT_Module::getFavorites(Auth::id());
			}
		} else {
			if ($show_gedc_favs && !$SEARCH_SPIDER) {
				$favorites = gedcom_favorites_WT_Module::getFavorites(WT_GED_ID);
			} else {
				return null;
			}
		}
		// Sort $favorites alphabetically?
		$menu = '<div class="btn-group">
					<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">' . WT_I18N::translate('Favorites') . '
						<span class="caret"></span>
					</button>
					<ul id="menu-favorites" class="dropdown-menu">';

		$list = array();
		foreach ($favorites as $favorite) {
			switch ($favorite['type']) {
				case 'URL':
					$list[$favorite['title']]	 = $favorite['url'];
					break;
				case 'INDI':
				case 'FAM':
				case 'SOUR':
				case 'OBJE':
				case 'NOTE':
					$obj						 = WT_GedcomRecord::getInstance($favorite['gid']);
					if ($obj && $obj->canShowName()) {
						$list[$obj->getFullName()] = $obj->getHtmlUrl();
					}
					break;
			}
		}

		if (count($list) > 0) {
			ksort($list);
			foreach ($list as $key => $value) {
				$menu.='<li><a href="' . $value . '">' . $key . '</a></li>';
			}
		}

		if ($show_user_favs) {
			if (isset($controller->record) && $controller->record instanceof WT_GedcomRecord) {
				$onclick = "jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'" . $controller->record->getXref() . "'},function(){location.reload();})";
				$menu.='<li><a href="#" onclick="' . $onclick . '">' . WT_I18N::translate('Add to favorites') . '</a></li>';
			}
		}
		$menu.='</ul></div>';
		return $menu;
	}

}
