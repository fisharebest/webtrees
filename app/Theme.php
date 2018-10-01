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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Theme\ThemeInterface;
use Fisharebest\Webtrees\Theme\WebtreesTheme;

/**
 * Provide access to the current theme.
 */
class Theme
{
    /** @var ThemeInterface The current theme */
    private static $theme;

    /** @var ThemeInterface[] All currently installed themes */
    private static $installed_themes;

    /**
     * Create a list of all themes available on the system, including
     * any custom themes.
     *
     * @return ThemeInterface[]
     */
    public static function installedThemes(): array
    {
        if (self::$installed_themes === null) {
            self::$installed_themes = [];
            foreach (glob(WT_ROOT . '/themes/*/theme.php') as $theme_path) {
                try {
                    $theme = include $theme_path;
                    // Themes beginning with an underscore are reserved for special use.
                    if (substr_compare($theme->themeId(), '_', 0, 1) !== 0) {
                        self::$installed_themes[] = $theme;
                    }
                } catch (\Exception $ex) {
                    DebugBar::addThrowable($ex);

                    // Broken theme? Ignore it.
                }
            }
        }

        return self::$installed_themes;
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
            $theme_names[$theme->themeId()] = $theme->themeName();
        }

        return $theme_names;
    }

    /**
     * The currently active theme
     *
     * @param ThemeInterface|null $theme
     *
     * @return ThemeInterface
     */
    public static function theme(ThemeInterface $theme = null): ThemeInterface
    {
        if ($theme) {
            self::$theme = $theme;
        } elseif (!self::$theme) {
            self::$theme = new WebtreesTheme();
        }

        self::$theme = self::$theme ?? $theme ?? new WebtreesTheme();

        return self::$theme;
    }
}
