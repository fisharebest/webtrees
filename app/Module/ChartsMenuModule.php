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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ChartsMenuModule - provide a menu option for the charts
 */
class ChartsMenuModule extends AbstractModule implements ModuleInterface, ModuleMenuInterface
{
    use ModuleMenuTrait;

    /** {@inheritdoc} */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Charts');
    }

    /** {@inheritdoc} */
    public function description(): string
    {
        /* I18N: Description of the “Reports” module */
        return I18N::translate('The charts menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 20;
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
        $request    = Request::createFromGlobals();
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree) ?? $tree->significantIndividual(Auth::user());
        $submenus   = Module::activeCharts($tree)
            ->map(function (ModuleChartInterface $module) use ($individual): Menu {
                return $module->chartMenu($individual);
            });

        if ($submenus->isEmpty()) {
            return null;
        }

        return new Menu(I18N::translate('Charts'), '#', 'menu-chart', ['rel' => 'nofollow'], $submenus->all());
    }
}
