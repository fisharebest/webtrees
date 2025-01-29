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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Statistics\Service\ColorService;

use function count;
use function view;

/**
 * A chart showing the top used media types.
 */
class ChartMedia
{
    private ColorService $color_service;

    public function __construct(ColorService $color_service)
    {
        $this->color_service = $color_service;
    }

    /**
     * @param array<string,int> $media
     */
    public function chartMedia(
        array $media,
        ?string $color_from = null,
        ?string $color_to = null
    ): string {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        $element = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE');
        $values  = $element->values();

        foreach ($media as $type => $count) {
            $data[] = [
                $values[$element->canonical($type)] ?? $type,
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
