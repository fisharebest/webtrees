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

/**
 * Class Webtrees - The webtrees (default) theme.
 */
class Webtrees extends BaseTheme {
	/** {@inheritdoc} */
	public function cssUrl() {
		return 'themes/webtrees/css-1.6.2/';
	}

	/** {@inheritdoc} */
	protected function favicon() {
		return '<link rel="icon" href="' . $this->cssUrl() . 'favicon.png" type="image/png">';
	}

	/** {@inheritdoc} */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="25" placeholder="' . WT_I18N::translate('Search') . '">' .
			'<input type="image" class="image" src="' . Theme::theme()->parameter('image-search') . '" alt="' . WT_I18N::translate('Search') . '" title="' . WT_I18N::translate('Search') . '">';
	}

	/** {@inheritdoc} */
	public function hookFooterExtraJavascript() {
		return
			'<script src="' . WT_JQUERY_COLORBOX_URL . '"></script>' .
			'<script src="' . WT_JQUERY_WHEELZOOM_URL . '"></script>' .
			'<script>' .
			'activate_colorbox();' .
			'jQuery.extend(jQuery.colorbox.settings, {' .
			' width:"85%",' .
			' height:"85%",' .
			' transition:"none",' .
			' slideshowStart:"' . WT_I18N::translate('Play') . '",' .
			' slideshowStop:"' . WT_I18N::translate('Stop') . '",' .
			' title: function() { return jQuery(this).data("title"); }' .
			'});' .
			'</script>';
	}

	/** {@inheritdoc} */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'             => 'e9daf1',
			'chart-background-m'             => 'b1cff0',
			'distribution-chart-high-values' => '84beff',
			'distribution-chart-low-values'  => 'c3dfff',
			'image-search'                   => $this->cssUrl() . 'images/search.png',
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
			'themes/webtrees/jquery-ui-1.11.2/jquery-ui.css',
			$this->cssUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'webtrees';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return WT_I18N::translate('webtrees');
	}
}
