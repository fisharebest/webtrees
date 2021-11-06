<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\I18N;

use function count;
use function view;

/**
 * A chart showing individuals with sources.
 */
class ChartIndividualWithSources
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
        string $color_to = null
    ): string {
        $color_from = $color_from ?? ['--chart-values-low', '#ffffff'];
        $color_to   = $color_to ??  ['--chart-values-high', '#84beff'];

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        if ($tot_indi || $tot_indi_source) {
            $data[] = [
                I18N::translate('Without sources'),
                $tot_indi - $tot_indi_source
            ];

            $data[] = [
                I18N::translate('With sources'),
                $tot_indi_source
            ];
        }

        return view('statistics/other/charts/pie', [
            'title'            => I18N::translate('Individuals with sources'),
            'data'             => $data,
            'colors'           => [$color_from, $color_to],
            'steps'            => count($data) - 1,
            'labeledValueText' => 'percentage',
            'language'         => I18N::languageTag(),
        ]);
    }
}
