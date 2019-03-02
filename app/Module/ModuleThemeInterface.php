<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Interface ModuleThemelInterface - Classes and libraries for module system
 */
interface ModuleThemeInterface extends ModuleInterface
{
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
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenu(Individual $individual): array;

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
     * Generate a list of items for the main menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function genealogyMenu(?Tree $tree): array;

    /**
     * Generate a list of items for the user menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function userMenu(?Tree $tree): array;

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array;
}
