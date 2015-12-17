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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;

/**
 * Specification for a theme.
 */
interface ThemeInterface {
	/**
	 * Where are our CSS, JS and other assets?
	 *
	 * @return string A relative path, such as "themes/foo/"
	 */
	public function assetUrl();

	/**
	 * Create the top of the <body>.
	 *
	 * @return string
	 */
	public function bodyHeader();

	/**
	 * Create the top of the <body> (for popup windows).
	 *
	 * @return string
	 */
	public function bodyHeaderPopupWindow();

	/**
	 * Create a contact link for a user.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLink(User $user);

	/**
	 * Create the <DOCTYPE> tag.
	 *
	 * @return string
	 */
	public function doctype();

	/**
	 * Close the main content and create the <footer> tag.
	 *
	 * @return string
	 */
	public function footerContainer();

	/**
	 * Close the main content.
	 * Note that popup windows are deprecated
	 *
	 * @return string
	 */
	public function footerContainerPopupWindow();

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
	public function formatBlock($id, $title, $class, $content);

	/**
	 * Create the <head> tag.
	 *
	 * @param PageController $controller The current controller
	 *
	 * @return string
	 */
	public function head(PageController $controller);

	/**
	 * Allow themes to do things after initialization (since they cannot use
	 * the constructor).
	 */
	public function hookAfterInit();

	/**
	 * Allow themes to add extra scripts to the page footer.
	 *
	 * @return string
	 */
	public function hookFooterExtraJavascript();

	/**
	 * Allow themes to add extra content to the page header.
	 * Typically this will be additional CSS.
	 *
	 * @return string
	 */
	public function hookHeaderExtraContent();

	/**
	 * Create the <html> tag.
	 *
	 * @return string
	 */
	public function html();

	/**
	 * Add HTML markup to create an alert
	 *
	 * @param string $html        The content of the alert
	 * @param string $level       One of 'success', 'info', 'warning', 'danger'
	 * @param bool   $dismissible If true, add a close button.
	 *
	 * @return string
	 */
	public function htmlAlert($html, $level, $dismissible);

	/**
	 * Display an icon for this fact.
	 *
	 * @param Fact $fact
	 *
	 * @return string
	 */
	public function icon(Fact $fact);

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBox(Individual $individual);

	/**
	 * Display an empty box - for a missing individual in a chart.
	 *
	 * @return string
	 */
	public function individualBoxEmpty();

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxLarge(Individual $individual);

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function individualBoxSmall(Individual $individual);

	/**
	 * Display an individual in a box - for charts, etc.
	 *
	 * @return string
	 */
	public function individualBoxSmallEmpty();

	/**
	 * Initialise the theme.  We cannot pass these in a constructor, as the construction
	 * happens in a theme file, and we need to be able to change it.
	 *
	 * @param Tree|null $tree The current tree (if there is one).
	 */
	public function init(Tree $tree = null);

	/**
	 * Themes menu.
	 *
	 * @return Menu|null
	 */
	public function menuThemes();

	/**
	 * Misecellaneous dimensions, fonts, styles, etc.
	 *
	 * @param string $parameter_name
	 *
	 * @return string|int|float
	 */
	public function parameter($parameter_name);

	/**
	 * Send any HTTP headers.
	 */
	public function sendHeaders();

	/**
	 * A fixed string to identify this theme, in settings, etc.
	 *
	 * @return string
	 */
	public function themeId();

	/**
	 * What is this theme called?
	 *
	 * @return string
	 */
	public function themeName();
}
