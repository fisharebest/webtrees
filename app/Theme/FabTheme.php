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

namespace Fisharebest\Webtrees\Theme;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;

/**
 * The F.A.B. theme.
 */
class FabTheme extends AbstractTheme implements ThemeInterface
{
    /**
     * Where are our CSS, JS and other assets?
     */
    const THEME_DIR  = 'fab';
    const ASSET_DIR  = 'themes/' . self::THEME_DIR . '/css-2.0.0/';
    const STYLESHEET = self::ASSET_DIR . 'style.css';

    /**
     * Add markup to the secondary menu.
     *
     * @return string
     */
    public function formatSecondaryMenu(): string
    {
        return
            '<ul class="nav wt-secondary-menu justify-content-end">' .
            implode('', array_map(function (Menu $menu): string {
                return $menu->bootstrap4();
            }, $this->secondaryMenu())) .
            '<li>' .
            $this->formQuickSearch() .
            '</li>' .
            '</ul>';
    }

    /**
     * Create a search field and submit button for the quick search form in the header.
     *
     * @return string
     */
    public function formQuickSearchFields(): string
    {
        return
            '<input type="search" name="query" size="20" placeholder="' . I18N::translate('Search') . '">';
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
            'chart-box-x'                    => 260,
            'chart-box-y'                    => 85,
            'distribution-chart-high-values' => '9ca3d4',
            'distribution-chart-low-values'  => 'e5e6ef',
        ];

        return $parameters[$parameter_name] ?? parent::parameter($parameter_name);
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
        return I18N::translate('F.A.B.');
    }
}
