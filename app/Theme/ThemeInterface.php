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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Specification for a theme.
 */
interface ThemeInterface
{
    /**
     * Create a contact link for a user.
     *
     * @param User $user
     *
     * @return string
     */
    public function contactLink(User $user): string;

    /**
     * Display an icon for this fact.
     *
     * @param Fact $fact
     *
     * @return string
     */
    public function icon(Fact $fact): string;

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBox(Individual $individual): string;

    /**
     * Display an empty box - for a missing individual in a chart.
     *
     * @return string
     */
    public function individualBoxEmpty(): string;

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxLarge(Individual $individual): string;

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxSmall(Individual $individual): string;

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @return string
     */
    public function individualBoxSmallEmpty(): string;

    /**
     * Initialise the theme. We cannot pass these in a constructor, as the construction
     * happens in a theme file, and we need to be able to change it.
     *
     * @param Request   $request
     * @param Tree|null $tree The current tree (if there is one).
     *
     * @return void
     */
    public function init(Request $request, Tree $tree = null);

    /**
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenu(Individual $individual): array;

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
    public function themeId(): string;

    /**
     * What is this theme called?
     *
     * @return string
     */
    public function themeName(): string;
}
