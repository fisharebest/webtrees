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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\Service\ColorService;

use function app;
use function count;

/**
 * A chart showing the top given names.
 */
class ChartCommonGiven
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
     * Create a chart of common given names.
     *
     * @param int         $tot_indi   The total number of individuals
     * @param array       $given      The list of common given names
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartCommonGiven(
        int $tot_indi,
        array $given,
        string $color_from = null,
        string $color_to = null
    ): string {
        $chart_color1 = (string) $this->theme->parameter('distribution-chart-no-values');
        $chart_color2 = (string) $this->theme->parameter('distribution-chart-high-values');
        $color_from   = $color_from ?? $chart_color1;
        $color_to     = $color_to   ?? $chart_color2;

        $tot = 0;
        foreach ($given as $count) {
            $tot += $count;
        }

        $data = [
            [
                I18N::translate('Name'),
                I18N::translate('Total')
            ],
        ];

        foreach ($given as $name => $count) {
            $data[] = [ $name, $count ];
        }

        $data[] = [
            I18N::translate('Other'),
            $tot_indi - $tot
        ];

        $colors = $this->color_service->interpolateRgb($color_from, $color_to, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => null,
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }
}
