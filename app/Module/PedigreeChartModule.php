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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Class PedigreeChartModule
 */
class PedigreeChartModule extends AbstractModule implements ModuleChartInterface
{
    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Pedigree');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “PedigreeChart” module */
        return I18N::translate('A chart of an individual’s ancestors, formatted as a tree.');
    }

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int
     */
    public function defaultAccessLevel(): int
    {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * Return a menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getChartMenu(Individual $individual)
    {
        return new Menu(
            $this->getTitle(),
            route('pedigree', [
                'xref' => $individual->getXref(),
                'ged'  => $individual->getTree()->getName(),
            ]),
            'menu-chart-pedigree',
            ['rel' => 'nofollow']
        );
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getBoxChartMenu(Individual $individual)
    {
        return $this->getChartMenu($individual);
    }
}
