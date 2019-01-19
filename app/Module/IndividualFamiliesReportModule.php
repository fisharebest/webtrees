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

/**
 * Class IndividualFamiliesReportModule
 */
class IndividualFamiliesReportModule extends AbstractModule implements ModuleInterface, ModuleReportInterface
{
    use ModuleReportTrait;

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected $access_level = Auth::PRIV_USER;

    /** {@inheritdoc} */
    public function title(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Name of a module/report */
        return I18N::translate('Related families');
    }

    /** {@inheritdoc} */
    public function description(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Description of the “Related families” */
        return I18N::translate('A report of the families that are closely related to an individual.');
    }

    /**
     * Return a menu item for this report.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function getReportMenu(Individual $individual): Menu
    {
        return new Menu(
            $this->title(),
            route('report-setup', [
                'ged'    => $individual->tree()->name(),
                'report' => $this->getName(),
            ]),
            'menu-report-' . $this->getName(),
            ['rel' => 'nofollow']
        );
    }
}
