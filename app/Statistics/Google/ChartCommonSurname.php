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
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartCommonSurname extends AbstractGoogle
{
    /**
     * @var string
     */
    private $surname_tradition;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        parent::__construct($tree);

        $this->surname_tradition = $this->tree->getPreference('SURNAME_TRADITION');
    }

    /**
     * Count up the different versions of a name and returns the one with the most matches. Takes
     * different surname traditions into account.
     *
     * @param array $surns
     *
     * @return array [ name, count ]
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

        if ($this->surname_tradition === 'polish') {
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
            $top_name,
            $count_per
        ];
    }

    /**
     * Create a chart of common surnames.
     *
     * @param int         $tot_indi     The total number of individuals
     * @param array       $all_surnames The list of common surnames
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartCommonSurnames(
        int $tot_indi,
        array $all_surnames,
        string $color_from = null,
        string $color_to = null
    ): string {
        $chart_color1 = (string) $this->theme->parameter('distribution-chart-no-values');
        $chart_color2 = (string) $this->theme->parameter('distribution-chart-high-values');
        $color_from   = $color_from ?? $chart_color1;
        $color_to     = $color_to   ?? $chart_color2;

        $tot = 0;
        foreach ($all_surnames as $surn => $surnames) {
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

        $colors = $this->interpolateRgb($color_from, $color_to, \count($data) - 1);

        return view(
            'statistics/other/charts/pie',
            [
                'title'  => null,
                'data'   => $data,
                'colors' => $colors,
            ]
        );
    }
}
