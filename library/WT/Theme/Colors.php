<?php
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
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

namespace WT\Theme;

use WT\Auth;
use WT\Theme;
use WT_I18N;
use WT_Menu;
use WT_Site;

/**
 * Class Colors - The colors theme.
 */
class Colors extends Clouds {
	/** @var string[] A list of color palettes */
	protected $palettes;

	/** @var string Which of the color palettes to use on this page */
	protected $palette;

	/** {@inheritdoc} */
	public function assetUrl() {
		return 'themes/colors/css-1.7.0/';
	}

	/** {@inheritdoc} */
	protected function formatSecondaryMenu() {
		return
			'<ul class="secondary-menu">' .
			implode('', $this->secondaryMenu()) .
			'<li>' .
			$this->formQuickSearch() .
			'</li>' .
			'</ul>';
	}

	/** {@inheritdoc} */
	protected function headerContent() {
		return
			//$this->accessibilityLinks() .
			$this->formatTreeTitle() .
			$this->formatSecondaryMenu();
	}

	/**
	 * Create resources for the colors theme.
	 *
	 * {@inheritdoc}
	 */
	public function hookAfterInit() {
		$this->palettes = array(
			'aquamarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Aqua Marine'),
			'ash'             => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Ash'),
			'belgianchocolate'=> /* I18N: The name of a colour-scheme */ WT_I18N::translate('Belgian Chocolate'),
			'bluelagoon'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Lagoon'),
			'bluemarine'      => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Blue Marine'),
			'coffeeandcream'  => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Coffee and Cream'),
			'coldday'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Cold Day'),
			'greenbeam'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Green Beam'),
			'mediterranio'    => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mediterranio'),
			'mercury'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Mercury'),
			'nocturnal'       => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Nocturnal'),
			'olivia'          => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Olivia'),
			'pinkplastic'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Pink Plastic'),
			'sage'            => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Sage'),
			'shinytomato'     => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Shiny Tomato'),
			'tealtop'         => /* I18N: The name of a colour-scheme */ WT_I18N::translate('Teal Top'),
		);
		uasort($this->palettes, array('WT_I18N', 'strcasecmp'));

		// If we've selected a new palette, and we are logged in, set this value as a default.
		if (isset($_GET['themecolor']) && array_key_exists($_GET['themecolor'], $this->palettes)) {
			// Request to change color
			$this->palette = $_GET['themecolor'];
			Auth::user()->setPreference('themecolor', $this->palette);
			if (Auth::isAdmin()) {
				WT_Site::setPreference('DEFAULT_COLOR_PALETTE', $this->palette);
			}
			unset($_GET['themecolor']);
			// Rember that we have selected a value
			$this->session->subColor = $this->palette;
		}
		// If we are logged in, use our preference
		$this->palette = Auth::user()->getPreference('themecolor');
		// If not logged in or no preference, use one we selected earlier in the session?
		if (!$this->palette) {
			$this->palette = $this->session->subColor;
		}
		// We haven't selected one this session?  Use the site default
		if (!$this->palette) {
			$this->palette = WT_Site::getPreference('DEFAULT_COLOR_PALETTE');
		}
		// Make sure our selected palette actually exists
		if (!array_key_exists($this->palette, $this->palettes)) {
			$this->palette = 'ash';
		}
	}

	/**
	 * Generate a list of items for the user menu.
	 *
	 * @return WT_Menu[]
	 */
	protected function secondaryMenu() {
		$menubar = parent::secondaryMenu();
		$menubar[] = $this->menuPalette();

		return $menubar;
	}

	/**
	 * Create a menu of palette options
	 *
	 * @return WT_Menu
	 */
	protected function menuPalette() {
		$menu = new WT_Menu(/* I18N: A colour scheme */ WT_I18N::translate('Palette'), '#', 'menu-color');

		foreach ($this->palettes as $palette_id => $palette_name) {
			$submenu = new WT_Menu($palette_name, get_query_url(array('themecolor' => $palette_id), '&amp;'), 'menu-color-' . $palette_id);
			if ($this->palette === $palette_id) {
				$submenu->addClass('active');
			}
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/** {@inheritdoc} */
	protected function stylesheets() {
		return array(
			'themes/colors/jquery-ui-1.11.2/jquery-ui.css',
			$this->assetUrl() . 'style.css',
			$this->assetUrl() . 'palette/' . $this->palette . '.css',
		);
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'colors';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('colors');
	}
}
