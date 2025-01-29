<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

/**
 * A chart showing the distribution of males and females.
 */
class ChartSex
{
    public function __construct()
    {
        // Empty constructor to prevent the "deprecated constructor" warning in PHP 7.4
    }

    public function chartSex(
        int $total_male,
        int $total_female,
        int $total_unknown,
        ?string $color_female = null,
        ?string $color_male = null,
        ?string $color_unknown = null
    ): string {
        $color_female ??= '#ffd1dc';
        $color_male ??= '#84beff';
        $color_unknown ??= '#777777';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        if ($total_male > 0 || $total_female > 0 || $total_unknown > 0) {
            $data[] = [
                I18N::translate('Males'),
                $total_male
            ];

            $data[] = [
                I18N::translate('Females'),
                $total_female
            ];

            $data[] = [
                I18N::translate('Unknown'),
                $total_unknown
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
