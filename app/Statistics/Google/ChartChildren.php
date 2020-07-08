<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use stdClass;

/**
 * A chart showing the average number of children by century.
 */
class ChartChildren
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var CenturyService
     */
    private $century_service;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree            = $tree;
        $this->century_service = new CenturyService();
    }

    /**
     * Returns the related database records.
     *
     * @return Collection<stdClass>
     */
    private function queryRecords(): Collection
    {
        return DB::table('families')
            ->selectRaw('AVG(f_numchil) AS total')
            ->selectRaw('ROUND((d_year + 49) / 100) AS century')
            ->join('dates', static function (JoinClause $join): void {
                $join->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('d_julianday1', '<>', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(static function (stdClass $row): stdClass {
                return (object) [
                    'century' => (int) $row->century,
                    'total'   => (float) $row->total,
                ];
            });
    }

    /**
     * Creates a children per family chart.
     *
     * @return string
     */
    public function chartChildren(): string
    {
        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Average number')
            ]
        ];

        foreach ($this->queryRecords() as $record) {
            $data[] = [
                $this->century_service->centuryName($record->century),
                round($record->total, 2),
            ];
        }

        $chart_title   = I18N::translate('Average number of children per family');
        $chart_options = [
            'title' => $chart_title,
            'subtitle' => '',
            'legend' => [
                'position'  => 'none',
            ],
            'vAxis' => [
                'title' => I18N::translate('Number of children'),
            ],
            'hAxis' => [
                'title' => I18N::translate('Century'),
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
