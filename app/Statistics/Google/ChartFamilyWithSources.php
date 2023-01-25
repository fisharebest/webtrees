<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

    /**
     * @param ColorService $color_service
     */
    public function __construct(ColorService $color_service)
    {
        $this->color_service = $color_service;
    }

    /**
     * Create a chart of individuals with/without sources.
     *
     * @param int         $tot_fam        The total number of families
     * @param int         $tot_fam_source The total number of families with sources
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(
        int $tot_fam,
        int $tot_fam_source,
        string $color_from = null,
        string $color_to = null
    ): string {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        if ($tot_fam > 0 || $tot_fam_source > 0) {
            $data[] = [
                I18N::translate('Without sources'),
                $tot_fam - $tot_fam_source
            ];

            $data[] = [
                I18N::translate('With sources'),
                $tot_fam_source
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
