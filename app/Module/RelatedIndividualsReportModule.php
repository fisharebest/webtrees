<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\I18N;

/**
 * Class RelatedIndividualsReportModule
 */
class RelatedIndividualsReportModule extends AbstractModule implements ModuleReportInterface
{
    use ModuleReportTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Name of a report */
        return I18N::translate('Related individuals');
    }

    public function description(): string
    {
        // This text also appears in the .XML file - update both together
        /* I18N: Description of the “Related individuals” module */
        return I18N::translate('A report of the individuals that are closely related to an individual.');
    }
}
