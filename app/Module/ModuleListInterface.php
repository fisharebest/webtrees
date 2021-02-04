<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Interface ModuleListInterface - Classes and libraries for module system
 */
interface ModuleListInterface extends ModuleInterface
{

    /**
     * A main menu item for this list, or null if the list is empty.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function listMenu(Tree $tree): ?Menu;

    /**
     * CSS class for the menu.
     *
     * @return string
     */
    public function listMenuClass(): string;

    /**
     * The title for a specific instance of this list.
     *
     * @return string
     */
    public function listTitle(): string;

    /**
     * The URL for a page showing list options.
     *
     * @param Tree    $tree
     * @param mixed[] $parameters
     *
     * @return string
     */
    public function listUrl(Tree $tree, array $parameters = []): string;

    /**
     * Attributes for the URL.
     *
     * @return array<string>
     */
    public function listUrlAttributes(): array;

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    public function listIsEmpty(Tree $tree): bool;
}
