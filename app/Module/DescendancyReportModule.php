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
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Class DescendancyReportModule
 */
class DescendancyReportModule extends AbstractModule implements ModuleReportInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Name of a module/report */
        return I18N::translate('Descendants');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Description of the “Descendants” module */
        return I18N::translate('A report of an individual’s descendants, in a narrative style.');
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
     * Return a menu item for this report.
     *
     * @param Tree $tree
     *
     * @return Menu
     */
    public function getReportMenu(Tree $tree): Menu
    {
        return new Menu(
            $this->getTitle(),
            route('report-setup', ['ged'    => $tree->getName(),
                                   'report' => $this->getName(),
            ]),
            'menu-report-' . $this->getName(),
            ['rel' => 'nofollow']
        );
    }
}
