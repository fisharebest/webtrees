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

/**
 * A chart showing the distribution of males and females.
 */
class ChartSex
{
    /**
     * Generate a chart showing sex distribution.
     *
     * @param int         $tot_m         The total number of male individuals
     * @param int         $tot_f         The total number of female individuals
     * @param int         $tot_u         The total number of unknown individuals
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        int $tot_m,
        int $tot_f,
        int $tot_u,
        string $color_female = null,
        string $color_male = null,
        string $color_unknown = null
    ): string {
        $color_female  = $color_female  ?? '#ffd1dc';
        $color_male    = $color_male    ?? '#84beff';
        $color_unknown = $color_unknown ?? '#777777';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        if ($tot_m || $tot_f || $tot_u) {
            $data[] = [
                I18N::translate('Males'),
                $tot_m
            ];

            $data[] = [
                I18N::translate('Females'),
                $tot_f
            ];

            $data[] = [
                I18N::translate('Unknown'),
                $tot_u
            ];
        }

        return view('statistics/other/charts/pie', [
            'title'            => null,
            'data'             => $data,
            'colors'           => [$color_male, $color_female, $color_unknown],
            'labeledValueText' => 'percentage',
            'language'         => I18N::languageTag(),
        ]);
    }
}
