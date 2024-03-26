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
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

use function array_sum;
use function count;
use function preg_replace;
use function view;

/**
 * A chart showing the top surnames.
 */
class ChartCommonSurname
{
    private ColorService $color_service;

    private SurnameTraditionInterface $surname_tradition;

    /**
     * @param ColorService              $color_service
     * @param SurnameTraditionInterface $surname_tradition
     */
    public function __construct(ColorService $color_service, SurnameTraditionInterface $surname_tradition)
    {
        $this->surname_tradition = $surname_tradition;
        $this->color_service     = $color_service;
    }

    /**
     * Count up the different versions of a name and returns the one with the most matches. Takes
     * different surname traditions into account.
     *
     * @param array<string,int> $surns
     *
     * @return array{0:string,1:int}
     */
    private function getTopNameAndCount(array $surns): array
    {
        $max_name  = 0;
        $count_per = 0;
        $top_name  = '';

        foreach ($surns as $spfxsurn => $count) {
            $per = $count;
            $count_per += $per;

            // select most common surname from all variants
            if ($per > $max_name) {
                $max_name = $per;
                $top_name = $spfxsurn;
            }
        }

        if ($this->surname_tradition instanceof PolishSurnameTradition) {
            // Most common surname should be in male variant (Kowalski, not Kowalska)
            $top_name = preg_replace(
                [
                    '/ska$/',
                    '/cka$/',
                    '/dzka$/',
                    '/żka$/',
                ],
                [
                    'ski',
                    'cki',
                    'dzki',
                    'żki',
                ],
                $top_name
            );
        }

        return [
            (string) $top_name,
            $count_per
        ];
    }

    /**
     * Create a chart of common surnames.
     *
     * @param int               $tot_indi     The total number of individuals
     * @param array<array<int>> $all_surnames The list of common surnames
     * @param string|null       $color_from
     * @param string|null       $color_to
     *
     * @return string
     */
    public function chartCommonSurnames(
        int $tot_indi,
        array $all_surnames,
        ?string $color_from = null,
        ?string $color_to = null
    ): string {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $tot = 0;
        foreach ($all_surnames as $surnames) {
            $tot += array_sum($surnames);
        }

        $data = [
            [
                I18N::translate('Name'),
                I18N::translate('Total')
            ],
        ];

        foreach ($all_surnames as $surns) {
            $data[] = $this->getTopNameAndCount($surns);
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
