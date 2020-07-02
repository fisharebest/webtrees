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

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\Service\ColorService;

use function app;
use function count;

/**
 * A chart showing the top used media types.
 */
class ChartMedia
{
    /**
     * @var ModuleThemeInterface
     */
    private $theme;

    /**
     * @var ColorService
     */
    private $color_service;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->theme         = app(ModuleThemeInterface::class);
        $this->color_service = new ColorService();
    }

    /**
     * Create a chart of media types.
     *
     * @param array<string,int> $media      The list of media types to display
     * @param string|null       $color_from
     * @param string|null       $color_to
     *
     * @return string
     */
    public function chartMedia(
        array $media,
        string $color_from = null,
        string $color_to = null
    ): string {
        $chart_color1 = (string) $this->theme->parameter('distribution-chart-no-values');
        $chart_color2 = (string) $this->theme->parameter('distribution-chart-high-values');
        $color_from   = $color_from ?? $chart_color1;
        $color_to     = $color_to   ?? $chart_color2;

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        foreach ($media as $type => $count) {
            $data[] = [
                GedcomTag::getFileFormTypeValue($type),
                $count
            ];
        }

        $colors = $this->color_service->interpolateRgb($color_from, $color_to, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => null,
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }
}
