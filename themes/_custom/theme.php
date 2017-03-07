<?php
namespace MyNamespace\MyProject;

/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Theme\WebtreesTheme;

/**
 * Class MyTheme - This is a template showing how to create a custom theme.
 *
 * Since theme folders beginning with an underscore are reserved for special
 * use, you should copy this entire folder ("themes/_custom") to a new name.
 * For example, "themes/mytheme". You should also rename the class.
 *
 * In this example, we are extending the webtrees theme, but you could
 * also extend any of the core themes, or even the base theme.
 */
class MyTheme extends WebtreesTheme {
	// The ID (and folder) for your theme.
	const THEME_DIR = '_custom';

	/**
	 * Give your theme a name. This is shown to the users.
	 * Use HTML entities where appropriate. e.g. “Black &amp; white”.
	 */
	public function themeName() {
		return 'Custom theme';
	}

	/**
	 * This is an example function which shows how to add an additional CSS file to the theme.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		return array_merge(parent::stylesheets(), [
			// Put a version number in the URL, for efficient browser caching.
			WT_BASE_URL . 'themes/' . self::THEME_DIR . '/css-1.0.0/style.css',
		]);
	}

	/**
	 * This is an example function which shows one way to remove an entry from a menu.
	 *
	 * @param string $surname The significant surname for the page.
	 *
	 * {@inheritdoc}
	 */
	public function menuLists($surname) {
		// Start with the default "Lists" menu.
		$menu = parent::menuLists($surname);
		// Remove the "notes" sub-menu.
		$submenus = array_filter($menu->getSubmenus(), function (Menu $menu) {
			return $menu->getClass() !== 'menu-list-note';
		});
		// Replace the sub-menus
		$menu->setSubmenus($submenus);

		return $menu;
	}
}

return new MyTheme; // This script must return a theme object.

