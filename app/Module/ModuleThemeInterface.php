<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Interface ModuleThemelInterface - Classes and libraries for module system
 */
interface ModuleThemeInterface extends ModuleInterface
{
    /**
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return array<Menu>
     */
    public function individualBoxMenu(Individual $individual): array;

    /**
     * Themes menu.
     *
     * @return Menu|null
     */
    public function menuThemes(): Menu|null;

    /**
     * Generate a list of items for the main menu.
     *
     * @param Tree|null $tree
     *
     * @return array<Menu>
     */
    public function genealogyMenu(Tree|null $tree): array;

    /**
     * Generate a list of items for the user menu.
     *
     * @param Tree|null $tree
     *
     * @return array<Menu>
     */
    public function userMenu(Tree|null $tree): array;

    /**
     * A list of CSS files to include for this page.
     *
     * @return array<string>
     */
    public function stylesheets(): array;

    /**
     * Sets the base Bootstrap theme.
     *
     * @return string
     */
    public function bootstrapTheme(): string;
}
