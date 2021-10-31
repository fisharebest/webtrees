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
use Fisharebest\Webtrees\Registry;

use function count;
use function view;

/**
 * A chart showing the top used media types.
 */
class ChartMedia
{
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
        $color_from = $color_from ?? ['--chart-values-low', '#ffffff'];
        $color_to   = $color_to ??  ['--chart-values-high', '#84beff'];

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        $values = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();

        foreach ($media as $type => $count) {
            $data[] = [
                $values[$type] ?? $type,
                $count
            ];
        }

        return view('statistics/other/charts/pie', [
            'title'    => null,
            'data'     => $data,
            'colors'   => [$color_from, $color_to],
            'steps'    => count($data) - 1,
            'language' => I18N::languageTag(),
        ]);
    }
}
