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
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Theme;

/**
 * The clouds theme.
 */
class CloudsTheme extends AbstractTheme implements ThemeInterface {
	/**
	 * Where are our CSS, JS and other assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	public function assetUrl() {
		return 'themes/clouds/css-1.7.0/';
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
	 * Format the contents of a variable-height home-page block.
	 *
	 * @param string $id
	 * @param string $title
	 * @param string $class
	 * @param string $content
	 *
	 * @return string
	 */
	public function formatBlock($id, $title, $class, $content) {
		return
			'<div id="' . $id . '" class="block" >' .
			'<table class="blockheader"><tr><td class="blockh1"></td><td class="blockh2">' .
			'<div class="blockhc"><b>' . $title . '</b></div>' .
			'</td><td class="blockh3"></td></tr></table>' .
			'<div class="blockcontent normal_inner_block ' . $class . '">' . $content . '</div>' .
			'</div>';
	}

	/**
	 * Create a search field and submit button for the quick search form in the header.
	 *
	 * @return string
	 */
	protected function formQuickSearchFields() {
		return
			'<input type="search" name="query" size="15" placeholder="' . I18N::translate('Search') . '">' .
			'<input class="search-icon" type="image" src="' . $this->assetUrl() . 'images/go.png" alt="' . I18N::translate('Search') . '" title="' . I18N::translate('Search') . '">';
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
			' slideshowStop: "' . I18N::translate('Stop') . '",' .
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
			'chart-background-f'             => 'e9daf1',
			'chart-background-m'             => 'b1cff0',
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

	/**
	 * Create the primary menu.
	 *
	 * @param Menu[] $menus
	 *
	 * @return string
	 */
	protected function primaryMenuContent(array $menus) {
		$html = '';

		foreach ($menus as $menu) {
			// Create an inert menu - to use as a label
			$tmp = new Menu($menu->getLabel(), '');

			// Insert the label into the submenu
			$submenus = $menu->getSubmenus();
			array_unshift($submenus, $tmp);
			$menu->setSubmenus($submenus);

			$html .= $menu->getMenuAsList();
		}

		return $html;
	}

	/**
	 * A list of CSS files to include for this page.
	 *
	 * @return string[]
	 */
	protected function stylesheets() {
		return array(
			'themes/clouds/jquery-ui-1.11.2/jquery-ui.css',
			$this->assetUrl() . 'style.css',
		);
	}

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	public function themeId() {
		return 'clouds';
	}

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	public function themeName() {
		return /* I18N: Name of a theme. */ I18N::translate('clouds');
	}
}
