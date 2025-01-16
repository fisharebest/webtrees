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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

use function round;
use function view;

/**
 * A chart showing the average age of individuals related to the death century.
 */
class ChartAge
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
     * @return Collection<array-key,object>
     */
    private function queryRecords(): Collection
    {
        return DB::table('individuals')
            ->select([
                new Expression('AVG(' . DB::prefix('death.d_julianday2') . ' - ' . DB::prefix('birth.d_julianday1') . ') / 365.25 AS age'),
                new Expression('ROUND((' . DB::prefix('death.d_year') . ' + 49) / 100, 0) AS century'),
                'i_sex AS sex'
            ])
            ->join('dates AS birth', static function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id');
            })
            ->join('dates AS death', static function (JoinClause $join): void {
                $join
                    ->on('death.d_file', '=', 'i_file')
                    ->on('death.d_gid', '=', 'i_id');
            })
            ->where('i_file', '=', $this->tree->id())
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('death.d_fact', '=', 'DEAT')
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->whereIn('death.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->whereColumn('death.d_julianday1', '>=', 'birth.d_julianday2')
            ->where('birth.d_julianday2', '<>', 0)
            ->groupBy(['century', 'sex'])
            ->orderBy('century')
            ->orderBy('sex')
            ->get()
            ->map(static fn (object $row): object => (object) [
                'age'     => (float) $row->age,
                'century' => (int) $row->century,
                'sex'     => $row->sex,
            ]);
    }

    /**
     * General query on ages.
     *
     * @return string
     */
    public function chartAge(): string
    {
        $out = [];
        foreach ($this->queryRecords() as $record) {
            $out[$record->century][$record->sex] = $record->age;
        }

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Males'),
                I18N::translate('Females'),
                I18N::translate('Average age'),
            ]
        ];

        foreach ($out as $century => $values) {
            $female_age  = $values['F'] ?? 0;
            $male_age    = $values['M'] ?? 0;
            $average_age = ($female_age + $male_age) / 2.0;

            $data[] = [
                $this->century_service->centuryName($century),
                round($male_age, 1),
                round($female_age, 1),
                round($average_age, 1),
            ];
        }

        $chart_title   = I18N::translate('Average age related to death century');
        $chart_options = [
            'title' => $chart_title,
            'subtitle' => I18N::translate('Average age at death'),
            'vAxis' => [
                'title' => I18N::translate('Age'),
            ],
            'hAxis' => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors' => [
                '#84beff',
                '#ffd1dc',
                '#ff0000',
            ],
        ];

        return view('statistics/other/charts/combo', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }
}
