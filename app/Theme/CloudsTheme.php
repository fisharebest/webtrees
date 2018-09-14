<?php
declare(strict_types = 1);
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
namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * The clouds theme.
 */
class CloudsTheme extends AbstractTheme implements ThemeInterface
{
    /**
     * Where are our CSS, JS and other assets?
     */
    const THEME_DIR  = 'clouds';
    const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
    const STYLESHEET = self::ASSET_DIR . 'style.css';

    /**
     * Misecellaneous dimensions, fonts, styles, etc.
     *
     * @param string $parameter_name
     *
     * @return string|int|float
     */
    public function parameter($parameter_name)
    {
        $parameters = [
            'chart-background-f'             => 'e9daf1',
            'chart-background-m'             => 'b1cff0',
            'chart-spacing-x'                => 4,
            'chart-box-x'                    => 260,
            'chart-box-y'                    => 85,
            'distribution-chart-high-values' => '95b8e0',
            'distribution-chart-low-values'  => 'c8e7ff',
        ];

        return $parameters[$parameter_name] ?? parent::parameter($parameter_name);
    }

    /**
     * Generate a list of items for the main menu.
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function primaryMenu(Individual $individual): array
    {
        $primary_menu = parent::primaryMenu($individual);

        foreach ($primary_menu as $menu) {
            $submenus = $menu->getSubmenus();

            if (!empty($submenus)) {
                // Insert a dummy menu / label into the submenu
                array_unshift($submenus, new Menu($menu->getLabel(), '#', null, ['onclick' => 'return false;']));
                $menu->setSubmenus($submenus);
            }
        }

        return $primary_menu;
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return array_merge(parent::stylesheets(), [
            self::STYLESHEET,
        ]);
    }

    /**
     * What is this theme called?
     *
     * @return string
     */
    public function themeName(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('clouds');
    }
}
