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

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;

/**
 *
 */
class ChartIndividualWithSources extends AbstractGoogle
{
    /**
     * Create a chart showing individuals with/without sources.
     *
     * @param int         $tot_indi        The total number of individuals
     * @param int         $tot_indi_source The total number of individuals with sources
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartIndisWithSources(
        int $tot_indi,
        int $tot_indi_source,
        string $color_from = null,
        string $color_to   = null
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
            [
                I18N::translate('Without sources'),
                $tot_indi - $tot_indi_source
            ],
            [
                I18N::translate('With sources'),
                $tot_indi_source
            ],
        ];

        $colors = $this->interpolateRgb($color_from, $color_to, \count($data) - 1);

        return view(
            'statistics/other/charts/pie',
            [
                'title'            => I18N::translate('Individuals with sources'),
                'data'             => $data,
                'colors'           => $colors,
                'labeledValueText' => 'percentage',
            ]
        );
    }
}
