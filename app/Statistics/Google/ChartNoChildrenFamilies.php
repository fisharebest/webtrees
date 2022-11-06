<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

use function view;

/**
 * A chart showing the number of families with no children by century.
 */
class ChartNoChildrenFamilies
{
    private Tree $tree;

    private CenturyService $century_service;

    /**
     * @param CenturyService $century_service
     * @param Tree           $tree
     */
    public function __construct(CenturyService $century_service, Tree $tree)
    {
        $this->century_service = $century_service;
        $this->tree            = $tree;
    }

    /**
     * Returns the related database records.
     *
     * @param int $year1
     * @param int $year2
     *
     * @return array<object>
     */
    private function queryRecords(int $year1, int $year2): array
    {
        $query = DB::table('families')
            ->selectRaw('ROUND((d_year + 49) / 100) AS century')
            ->selectRaw('COUNT(*) AS total')
            ->join('dates', static function (JoinClause $join): void {
                $join->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century');

        if ($year1 >= 0 && $year2 >= 0) {
            $query->whereBetween('d_year', [$year1, $year2]);
        }

        return $query->get()->all();
    }

    /**
     * Create a chart of children with no families.
     *
     * @param int $no_child_fam The number of families with no children
     * @param int $year1
     * @param int $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(
        int $no_child_fam,
        int $year1 = -1,
        int $year2 = -1
    ): string {
        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Total')
            ]
        ];

        $total = 0;

        foreach ($this->queryRecords($year1, $year2) as $record) {
            $total += (int) $record->total;

            $data[] = [
                $this->century_service->centuryName((int) $record->century),
                (int) $record->total
            ];
        }

        if ($total > 0) {
            $data[] = [
                I18N::translateContext('unknown century', 'Unknown'),
                $no_child_fam - $total,
            ];
        }

        $chart_title   = I18N::translate('Number of families without children');
        $chart_options = [
            'title' => $chart_title,
            'subtitle' => '',
            'legend' => [
                'position'  => 'none',
            ],
            'vAxis' => [
                'title' => I18N::translate('Total families'),
            ],
            'hAxis' => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors' => [
                '#84beff'
            ],
        ];

        return view('statistics/other/charts/column', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }
}
