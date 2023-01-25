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

use Fisharebest\Webtrees\I18N;

/**
 * The xenea theme.
 */
class XeneaTheme extends AbstractModule implements ModuleThemeInterface
{
    use ModuleThemeTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('xenea');
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return array<string>
     */
    public function stylesheets(): array
    {
        return [
            asset('css/xenea.min.css'),
        ];
    }
}
