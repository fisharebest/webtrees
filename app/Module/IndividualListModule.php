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

use Fisharebest\Webtrees\I18N;

/**
 * Class IndividualListModule
 */
class IndividualListModule extends AbstractIndividualListModule
{
    protected function routeUrl(): string
    {
        return '/tree/{tree}/individual-list';
    }

    protected function showFamilies(): bool
    {
        return false;
    }

    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Individuals');
    }

    public function description(): string
    {
        /* I18N: Description of the “Individuals” module */
        return I18N::translate('A list of individuals.');
    }

    public function listMenuClass(): string
    {
        return 'menu-list-indi';
    }
}
