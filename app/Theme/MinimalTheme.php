<?php
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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\I18N;

/**
 * The Minimal theme.
 */
class MinimalTheme extends AbstractTheme implements ThemeInterface {
	/**
	 * Where are our CSS, JS and other assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	public function assetUrl() {
		return 'themes/minimal/css-1.7.0/';
	}

	/**
	 * Add markup to a flash message.
	 *
	 * @param \stdClass $message
	 *
	 * @return string
	 */
	protected function flashMessageContainer(\stdClass $message) {
		// This theme uses jQuery markup.
		return '<p class="ui-state-highlight">' . $message->text . '</p>';
	}

	/**
	 * Create a search field and submit button for the quick search form in the header.
	 *
	 * @return string
	 */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="20" placeholder="' . I18N::translate('Search') . '">';
	}

	/**
	 * Add markup to the secondary menu.
	 *
	 * @return string
	 */
	protected function formatSecondaryMenu() {
		return
			'<ul class="secondary-menu">' .
			implode('', $this->secondaryMenu()) .
			'<li>' .
			$this->formQuickSearch() .
			'</li>' .
			'</ul>';
	}

	/**
	 * Create the contents of the <header> tag.
	 *
	 * @return string
	 */
	protected function headerContent() {
		return
			//$this->accessibilityLinks() .
			$this->formatTreeTitle() .
			$this->formatSecondaryMenu();
	}

	/**
	 * A small "powered by webtrees" logo for the footer.
	 *
	 * @return string
	 */
	protected function logoPoweredBy() {
		return '<a href="' . WT_WEBTREES_URL . '" class="powered-by-webtrees" title="' . WT_WEBTREES_URL . '">' . WT_WEBTREES . '</a>';
	}

	/**
	 * Allow themes to add extra scripts to the page footer.
	 *
	 * @return string
	 */
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
			' slideshowStop: "' . I18N::translate('Stop') . '"' .
			' title: function() { return jQuery(this).data("title"); }' .
			'});' .
			'</script>';
	}

	/**
	 * Misecellaneous dimensions, fonts, styles, etc.
	 *
	 * @param string $parameter_name
	 *
	 * @return string|int|float
	 */
	public function parameter($parameter_name) {
		$parameters = array(
			'chart-background-f'             => 'dddddd',
			'chart-background-m'             => 'cccccc',
			'distribution-chart-low-values'  => 'cccccc',
			'distribution-chart-no-values'   => 'ffffff',
		);

		if (array_key_exists($parameter_name, $parameters)) {
			return $parameters[$parameter_name];
		} else {
			return parent::parameter($parameter_name);
		}
	}

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		return array(
			'themes/minimal/jquery-ui-1.11.2/jquery-ui.css',
			$this->assetUrl() . 'style.css',
		);
	}

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	public function themeId() {
		return 'minimal';
	}

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	public function themeName() {
		return /* I18N: Name of a theme. */ I18N::translate('minimal');
	}
}
