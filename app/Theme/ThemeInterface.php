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
	 * Create a contact link for a user.
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function contactLink(User $user);

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
	 * Initialise the theme. We cannot pass these in a constructor, as the construction
	 * happens in a theme file, and we need to be able to change it.
	 *
	 * @param Tree|null $tree The current tree (if there is one).
	 */
	public function init(Tree $tree = null);

	/**
	 * Links, to show in chart boxes;
	 *
	 * @param Individual $individual
	 *
	 * @return Menu[]
	 */
	public function individualBoxMenu(Individual $individual);

	/**
	 * Misecellaneous dimensions, fonts, styles, etc.
	 *
	 * @param string $parameter_name
	 *
	 * @return string|int|float
	 */
	public function parameter($parameter_name);

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
