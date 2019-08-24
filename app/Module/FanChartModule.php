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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Class FanChartModule
 */
class FanChartModule extends AbstractModule implements ModuleChartInterface
{
    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle()
    {
        return /* I18N: Name of a module/chart */ I18N::translate('Fan chart');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription()
    {
        return /* I18N: Description of the “Fan Chart” module */ I18N::translate('A fan chart of an individual’s ancestors.');
    }

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int
     */
    public function defaultAccessLevel()
    {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * Return a menu item for this chart.
     * We can only do this if the GD2 library is installed with TrueType support.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getChartMenu(Individual $individual)
    {
        if (function_exists('imagettftext')) {
            return new Menu(
                $this->getTitle(),
                'fanchart.php?rootid=' . $individual->getXref() . '&amp;ged=' . $individual->getTree()->getNameUrl(),
                'menu-chart-fanchart',
                array('rel' => 'nofollow')
            );
        } else {
            return null;
        }
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
