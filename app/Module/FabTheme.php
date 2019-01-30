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

/**
 * The F.A.B. theme.
 */
class FabTheme extends AbstractModule implements ModuleThemeInterface
{
    use ModuleThemeTrait;

    /**
     * Where are our CSS, JS and other assets?
     */
    public const    ASSET_DIR  = 'themes/fab/css-2.0.0/';

    protected const PERSON_BOX_CLASSES = [
        'M' => 'person_box',
        'F' => 'person_boxF',
        'U' => 'person_boxNN',
    ];

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a theme. */
        return I18N::translate('F.A.B.');
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
            'chart-spacing-x'                => 5,
            'chart-spacing-y'                => 10,
            'compact-chart-box-x'            => 240,
            'compact-chart-box-y'            => 50,
            'distribution-chart-high-values' => '9ca3d4',
            'distribution-chart-low-values'  => 'e5e6ef',
            'distribution-chart-no-values'   => 'ffffff',
            'distribution-chart-x'           => 440,
            'distribution-chart-y'           => 220,
            'line-width'                     => 1.5,
            'shadow-blur'                    => 0,
            'shadow-color'                   => '',
            'shadow-offset-x'                => 0,
            'shadow-offset-y'                => 0,
            'stats-small-chart-x'            => 440,
            'stats-small-chart-y'            => 125,
            'stats-large-chart-x'            => 900,
            'image-dline'                    => static::ASSET_DIR . 'images/dline.png',
            'image-dline2'                   => static::ASSET_DIR . 'images/dline2.png',
            'image-hline'                    => static::ASSET_DIR . 'images/hline.png',
            'image-spacer'                   => static::ASSET_DIR . 'images/spacer.png',
            'image-vline'                    => static::ASSET_DIR . 'images/vline.png',
            'image-minus'                    => static::ASSET_DIR . 'images/minus.png',
            'image-plus'                     => static::ASSET_DIR . 'images/plus.png',
        ];

        return $parameters[$parameter_name];
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [
            'themes/_common/css-2.0.0/style.css',
            'themes/fab/css-2.0.0/style.css',
        ];
    }
}
