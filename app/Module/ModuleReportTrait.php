<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Http\RequestHandlers\ReportSetupPage;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Trait ModuleReportTrait - default implementation of ModuleReportInterface
 */
trait ModuleReportTrait
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
     * Name of the XML report file, relative to the resources folder.
     *
     * @return string
     */
    public function xmlFilename(): string
    {
        return 'xml/reports/' . $this->name() . '.xml';
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
            route(ReportSetupPage::class, [
                'xref'   => $individual->xref(),
                'tree'   => $individual->tree()->name(),
                'report' => $this->name(),
            ]),
            'menu-report-' . $this->name(),
            ['rel' => 'nofollow']
        );
    }
}
