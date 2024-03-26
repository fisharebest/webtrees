<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Statistics\Service\ColorService;

use function count;
use function view;

/**
 * A chart showing the mortality.
 */
class ChartMortality
{
    private ColorService $color_service;

    /**
     * @param ColorService $color_service
     */
    public function __construct(ColorService $color_service)
    {
        $this->color_service = $color_service;
    }

    /**
     * Create a chart showing mortality.
     *
     * @param int         $tot_l
     * @param int         $tot_d
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(
        int $tot_l,
        int $tot_d,
        string|null $color_living = null,
        string|null $color_dead = null
    ): string {
        $color_living ??= '#ffffff';
        $color_dead ??= '#cccccc';

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Total')
            ]
        ];

        if ($tot_l > 0 || $tot_d > 0) {
            $data[] = [
                I18N::translate('Living'),
                $tot_l
            ];

            $data[] = [
                I18N::translate('Dead'),
                $tot_d
            ];
        }

        $colors = $this->color_service->interpolateRgb($color_living, $color_dead, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'            => null,
            'data'             => $data,
            'colors'           => $colors,
            'labeledValueText' => 'percentage',
            'language'         => I18N::languageTag(),
        ]);
    }
}
