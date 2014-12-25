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

use WT_Controller_Page;
use WT_I18N;
use WT_Menu;

/**
 * Class Fab - The F.A.B. theme.
 */
class Fab extends BaseTheme {
	/** {@inheritdoc} */
	public function cssUrl() {
		return 'themes/fab/css-1.6.2/';
	}

	/** {@inheritdoc} */
	protected function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
	}

	/** {@inheritdoc} */
	protected function logoPoweredBy() {
		return
			'<a style="font-size:150%; color:#888;" href="' . WT_WEBTREES_URL . '" title="' . WT_WEBTREES . ' - ' . WT_VERSION . '">' .
			WT_WEBTREES .
			'</a>';
	}

	/** {@inheritdoc} */
	protected function formatMainMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	protected function formatUserMenuItem(WT_Menu $menu) {
		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="20" placeholder="' . WT_I18N::translate('Search') . '">';
	}

	/**
	 * Add some additional markup to the <head> element.
	 *
	 * {@inheritdoc}
	 */
	protected function headContents(WT_Controller_Page $controller) {
		return
			parent::headContents($controller) .
			'<!--[if IE]>' .
			'<link type="text/css" rel="stylesheet" href="' . $this->cssUrl() . 'msie.css">' .
			'<![endif]-->';
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
			'chart-box-x'                    => 260,
			'chart-descendancy-box-x'        => 290,
			'chart-spacing-x'                => 0,
			'distribution-chart-high-values' => '9ca3d4',
			'distribution-chart-low-values'  => 'e5e6ef',
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
			'themes/fab/jquery-ui-1.11.2/jquery-ui.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'fab';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ WT_I18N::translate('F.A.B.');
	}
}
