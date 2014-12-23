<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class CustomTheme - This is a template showing how to create a custom theme.
 *
 * Since theme folders beginning with an underscore are reserved for special
 * use, you should copy this entire folder ("themes/_custom") to a new name.
 * For example, "themes/custom".  You should also rename the class.
 *
 * In this example, we are extending the webtrees theme, but you could
 * also extend any of the core themes, or even the base theme.  You
 * should choose a unique class name, as users may install many custom themes.
 *
 * Only the first two functions are required: themeId() and themeName().
 * The rest are just examples, and should be removed in actual themes.
 *
 * Many of the core classes are being moved to namespaces.  For example,
 * WT_Foo will be renamed to WT\Foo.  The object API should remain the
 * same.  Avoid unecessary type-hints to allow your
 */
class CustomTheme extends WT\Theme\Webtrees {
	/**
	 * Give your theme a unique identifier.  Themes beginning with an underscore
	 * are reserved for internal use.
	 *
	 * {@inheritdoc}
	 */
	public function themeId() {
		return '_custom';
	}

	/**
	 * Give your theme a name.  This is shown to the users.
	 * Use HTML entities where appropriate.  e.g. “Black &amp; white”.
	 *
	 * You could use switch($this->locale) {} to provide a translated versions
	 * of the theme name.
	 *
	 * {@inheritdoc}
	 */
	public function themeName() {
		return 'Custom theme';
	}

	/**
	 * This is an example function which shows how to add an additional CSS file to the theme.
	 *
	 * {@inheritdoc}
	 */
	public function stylesheets() {
		try {
			$css_files   = parent::stylesheets();
			// Put a version number in the URL, to prevent browsers from caching old versions.
			$css_files[] = WT_SERVER_NAME . WT_SCRIPT_PATH . 'themes/custom/custom.css';
		} catch (Exception $ex) {
			// Something went wrong with our script?  Use the default behaviour instead.
			return parent::stylesheets();
		}

		return $css_files;
	}

	/**
	 * This is an example function which shows one way to remove an entry from a menu.
	 *
	 * {@inheritdoc}
	 */
	public function menuLists() {
		try {
			// Start with the default "Lists" menu.
			$menu = parent::menuLists();
			// Remove the "notes" sub-menu.
			$submenus = array_filter($menu->getSubmenus(), function (WT_Menu $menu) {
				return $menu->getId() !== 'menu-list-note';
			});
			// Replace the sub-menus
			$menu->setSubmenus($submenus);
		} catch (Exception $ex) {
			// Something went wrong with our script?  Maybe the core code was updated?
			// Use the default behaviour instead, so that our theme continues to work.
			return parent::menuLists();
		}

		return $menu;
	}
}

return new CustomTheme; // This script must return a theme object.
