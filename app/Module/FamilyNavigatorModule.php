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
use Fisharebest\Webtrees\Individual;

/**
 * Class FamilyNavigatorModule
 */
class FamilyNavigatorModule extends AbstractModule implements ModuleSidebarInterface
{
    use ModuleSidebarTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/sidebar */
        return I18N::translate('Family navigator');
    }

    public function description(): string
    {
        /* I18N: Description of the “Family navigator” module */
        return I18N::translate('A sidebar showing an individual’s close families and relatives.');
    }

    /**
     * The default position for this sidebar.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultSidebarOrder(): int
    {
        return 2;
    }

    /**
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasSidebarContent(Individual $individual): bool
    {
        return true;
    }

    /**
     * Load this sidebar synchronously.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getSidebarContent(Individual $individual): string
    {
        return view('modules/family_nav/sidebar', ['individual' => $individual]);
    }
}
