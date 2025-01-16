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

use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Trait ModuleListTrait - default implementation of ModuleListInterface
 */
trait ModuleListTrait
{
    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    abstract public function title(): string;

    /**
     * A main menu item for this list, or null if the list is empty.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function listMenu(Tree $tree): ?Menu
    {
        if ($this->listIsEmpty($tree)) {
            return null;
        }

        return new Menu(
            $this->listTitle(),
            $this->listUrl($tree),
            $this->listMenuClass(),
            $this->listUrlAttributes()
        );
    }

    /**
     * CSS class for the menu.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return '';
    }

    /**
     * The title for a specific instance of this list.
     *
     * @return string
     */
    public function listTitle(): string
    {
        return $this->title();
    }

    /**
     * The URL for a page showing list options.
     *
     * @param Tree                                      $tree
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'List',
                'tree'    => $tree->name(),
        ] + $parameters);
    }

    /**
     * Attributes for the URL.
     *
     * @return array<string>
     */
    public function listUrlAttributes(): array
    {
        return ['rel' => 'nofollow'];
    }

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    public function listIsEmpty(Tree $tree): bool
    {
        return false;
    }
}
