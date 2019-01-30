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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;

/**
 * Provide access to the current theme.
 */
class Theme
{
    /** @var ModuleThemeInterface The current theme */
    private static $theme;

    /**
     * An associative array of theme names, for <select> fields, etc.
     *
     * @return string[]
     */
    public static function themeNames(): array
    {
        $themes = Module::findByInterface(ModuleThemeInterface::class);

        $theme_names = [];
        foreach ($themes as $theme) {
            $theme_names[$theme->name()] = $theme->title();
        }

        return $theme_names;
    }
}
