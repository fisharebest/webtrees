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
     * Create a list of all themes available on the system, including
     * any custom themes.
     *
     * @return ModuleThemeInterface[]
     */
    public static function installedThemes(): array
    {
        return  Module::findByInterface(ModuleThemeInterface::class)->all();
    }

    /**
     * An associative array of theme names, for <select> fields, etc.
     *
     * @return string[]
     */
    public static function themeNames(): array
    {
        $theme_names = [];
        foreach (self::installedThemes() as $theme) {
            $theme_names[$theme->name()] = $theme->title();
        }

        return $theme_names;
    }

    /**
     * The currently active theme
     *
     * @param ModuleThemeInterface|null $theme
     *
     * @return ModuleThemeInterface
     */
    public static function theme(ModuleThemeInterface $theme = null): ModuleThemeInterface
    {
        self::$theme = $theme ?? self::$theme ?? app()->make(WebtreesTheme::class);

        return self::$theme;
    }
}
