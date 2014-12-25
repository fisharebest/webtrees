<?php namespace WT\Theme;
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

use WT\Theme;
use WT_I18N;
use WT_Menu;

/**
 * Class Clouds - The clouds theme.
 */
class Clouds extends BaseTheme {
	/** {@inheritdoc} */
	public function cssUrl() {
		return 'themes/clouds/css-1.6.2/';
	}

	/** {@inheritdoc} */
	protected function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
	}

	/** {@inheritdoc} */
	protected function footerContent() {
		return '<div id="footer">' . parent::footerContent() . '</div>';
	}

	/** {@inheritdoc} */
	public function formatBlock($id, $title, $class, $content) {
		return
			'<div id="' . $id . '" class="block" >' .
			'<table class="blockheader"><tr><td class="blockh1"></td><td class="blockh2">' .
			'<div class="blockhc"><b>' . $title . '</b></div>' .
			'</td><td class="blockh3"></td></tr></table>' .
			'<div class="blockcontent normal_inner_block ' . $class . '">' . $content . '</div>' .
			'</div>';
	}

	/** {@inheritdoc} */
	protected function formatMainMenuItem(WT_Menu $menu) {
		// Create an inert menu - to use as a label
		$tmp = new WT_Menu(strip_tags($menu->getLabel()), '');

		// Insert the label into the submenu
		$submenus = $menu->getSubmenus();
		array_unshift($submenus, $tmp);
		$menu->setSubmenus($submenus);

		// Neutralise the top-level menu
		$menu->setLabel('');
		$menu->setLink('');
		$menu->setOnclick('');

		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="15" placeholder="' . WT_I18N::translate('Search') . '">' .
			'<input class="search-icon" type="image" src="' . Theme::theme()->parameter('image-search') . '" alt="' . WT_I18N::translate('Search') . '" title="' . WT_I18N::translate('Search') . '">';
	}

	/** {@inheritdoc} */
	public function hookFooterExtraJavascript() {
		return
			'<script src="' . WT_JQUERY_COLORBOX_URL . '"></script>' .
			'<script src="' . WT_JQUERY_WHEELZOOM_URL . '"></script>' .
			'<script>' .
			'activate_colorbox();' .
			'jQuery.extend(jQuery.colorbox.settings, {' .
			' width: "85%",' .
			' height: "85%",' .
			' transition: "none",' .
			' slideshowStart: "' . WT_I18N::translate('Play') . '",' .
			' slideshowStop: "' . WT_I18N::translate('Stop') . '",' .
			' title: function() { return jQuery(this).data("title"); }' .
			'});' .
			'</script>';
	}

	/** {@inheritdoc} */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'             => 'e9daf1',
			'chart-background-m'             => 'b1cff0',
			'chart-descendancy-box-x'        => 250,
			'chart-spacing-x'                => 4,
			'distribution-chart-high-values' => '95b8e0',
			'distribution-chart-low-values'  => 'c8e7ff',
		);

		if (array_key_exists($parameter_name, $parameters)) {
			return $parameters[$parameter_name];
		} else {
			return parent::parameter($parameter_name);
		}
	}

	/** {@inheritdoc} */
	protected function stylesheets() {
		return array(
			'themes/clouds/jquery-ui-1.11.2/jquery-ui.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'clouds';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('clouds');
	}
}
