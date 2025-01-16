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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;

/**
 * Class ListsMenuModule - provide a menu option for the lists
 */
class ListsMenuModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Lists');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Lists” module */
        return I18N::translate('The lists menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 3;
    }

    /**
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree): ?Menu
    {
        $submenus = $this->module_service->findByComponent(ModuleListInterface::class, $tree, Auth::user())
            ->map(static function (ModuleListInterface $module) use ($tree): ?Menu {
                return $module->listMenu($tree);
            })
            ->filter()
            ->sort(static function (Menu $x, Menu $y): int {
                return I18N::comparator()($x->getLabel(), $y->getLabel());
            });

        if ($submenus->isEmpty()) {
            return null;
        }

        return new Menu(I18N::translate('Lists'), '#', 'menu-list', [], $submenus->all());
    }
}
