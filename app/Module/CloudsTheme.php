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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * The clouds theme.
 */
class CloudsTheme extends AbstractModule implements ModuleThemeInterface
{
    use ModuleThemeTrait {
        genealogyMenu as baseGenealogyMenu;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('clouds');
    }

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
            'chart-background-u'             => 'eeeeee',
            'chart-box-x'                    => 260,
            'chart-box-y'                    => 85,
            'chart-font-color'               => '000000',
            'chart-spacing-x'                => 4,
            'chart-spacing-y'                => 10,
            'compact-chart-box-x'            => 240,
            'compact-chart-box-y'            => 50,
            'distribution-chart-high-values' => '95b8e0',
            'distribution-chart-low-values'  => 'c8e7ff',
            'distribution-chart-no-values'   => 'ffffff',
        ];

        return $parameters[$parameter_name];
    }

    /**
     * Generate a list of items for the main menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function genealogyMenu(?Tree $tree): array
    {
        $primary_menu = $this->baseGenealogyMenu($tree);

        foreach ($primary_menu as $menu) {
            $submenus = $menu->getSubmenus();

            if ($submenus !== []) {
                // Insert a fake menu / label into the submenu
                array_unshift($submenus, new Menu($menu->getLabel(), '#', '', ['onclick' => 'return false;']));
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
        return [
            asset('css/clouds.min.css'),
        ];
    }
}
