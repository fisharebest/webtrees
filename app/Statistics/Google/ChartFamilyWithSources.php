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
use Fisharebest\Webtrees\Statistics\Service\ColorService;

use function count;
use function view;

/**
 * A chart showing families with sources.
 */
class ChartFamilyWithSources
{
    private ColorService $color_service;

    public function __construct(ColorService $color_service)
    {
        $this->color_service = $color_service;
    }

    public function chartFamsWithSources(
        int $total_families,
        int $total_families_with_sources,
        string|null $color_from = null,
        string|null $color_to = null
    ): string {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        if ($total_families > 0 || $total_families_with_sources > 0) {
            $data[] = [
                I18N::translate('Without sources'),
                $total_families - $total_families_with_sources
            ];

            $data[] = [
                I18N::translate('With sources'),
                $total_families_with_sources
            ];
        }

        $colors = $this->color_service->interpolateRgb($color_from, $color_to, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'            => I18N::translate('Families with sources'),
            'data'             => $data,
            'colors'           => $colors,
            'labeledValueText' => 'percentage',
            'language'         => I18N::languageTag(),
        ]);
    }
}
