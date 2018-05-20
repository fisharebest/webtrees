<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * The theme for the control panel.
 */
class AdministrationTheme extends AbstractTheme implements ThemeInterface {
	/**
	 * Where are our CSS, JS and other assets?
	 */
	const THEME_DIR  = '_administration';
	const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
	const STYLESHEET = self::ASSET_DIR . 'style.css';

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	public function stylesheets() {
		return array_merge(parent::stylesheets(), [
			self::STYLESHEET,
		]);
	}

	/**
	 * Create the contents of the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContent() {
		return '';
	}

	/**
	 * The admin pages do not have a menu.  All functions are listed
	 * in the control panel.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu[]
	 */
	public function primaryMenu(Individual $individual) {
		return [];
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return Menu[]
	 */
	public function secondaryMenu() {
		return array_filter([
			$this->menuPendingChanges(),
			$this->menuMyPage(),
			$this->menuLanguages(),
			$this->menuLogout(),
		]);
	}

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	public function themeName() {
		return 'administration';
	}
}
