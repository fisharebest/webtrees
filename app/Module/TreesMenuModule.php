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

use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;

use function e;
use function route;

/**
 * Class TreesMenuModule - provide a menu option for the trees options
 */
class TreesMenuModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    /** @var TreeService */
    private $tree_service;

    /**
     * TreesMenuModule constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Family trees');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Family trees” module */
        return I18N::translate('The family trees menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 1;
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
        $trees = $this->tree_service->all();

        if ($trees->count() === 1 || Site::getPreference('ALLOW_CHANGE_GEDCOM') !== '1') {
            return new Menu(I18N::translate('Family tree'), route(TreePage::class, ['tree' => $tree->name()]), 'menu-tree');
        }

        $submenus = [];
        foreach ($trees as $menu_tree) {
            if ($menu_tree->id() === $tree->id()) {
                $active = 'active ';
            } else {
                $active = '';
            }
            $submenus[] = new Menu(e($menu_tree->title()), route(TreePage::class, ['tree' => $menu_tree->name()]), $active . 'menu-tree-' . $menu_tree->id());
        }

        return new Menu(I18N::translate('Family trees'), '#', 'menu-tree', [], $submenus);
    }
}
