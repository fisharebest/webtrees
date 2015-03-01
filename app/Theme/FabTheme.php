<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class FabTheme - The F.A.B. theme.
 */
class FabTheme extends BaseTheme {
	/** {@inheritdoc} */
	public function assetUrl() {
		return 'themes/fab/css-1.7.0/';
	}

	/** {@inheritdoc} */
	protected function favicon() {
		return '<link rel="icon" href="' . $this->assetUrl() . 'favicon.png" type="image/png">';
	}

	/** {@inheritdoc} */
	protected function flashMessageContainer(\stdClass $message) {
		// This theme uses jQuery markup.
		return '<p class="ui-state-highlight">' . $message->text . '</p>';
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

	/** {@inheritdoc} */
	protected function formatSecondaryMenuItem(Menu $menu) {
		return $menu->getMenuAsList();
	}

	/** {@inheritdoc} */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="20" placeholder="' . I18N::translate('Search') . '">';
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
			' slideshowStart: "' . I18N::translate('Play') . '",' .
			' slideshowStop: "' . I18N::translate('Stop') . '",' .
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
			'chart-box-y'                    => 85,
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
			$this->assetUrl() . 'style.css',
		);
	}

	/** {@inheritdoc} */
	public function themeId() {
		return 'fab';
	}

	/** {@inheritdoc} */
	public function themeName() {
		return /* I18N: Name of a theme. */ I18N::translate('F.A.B.');
	}
}
