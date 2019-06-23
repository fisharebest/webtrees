<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use stdClass;

/**
 * A chart showing the marriage ages by century.
 */
class ChartMarriageAge
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
     * @return stdClass[]
     */
    private function queryRecords(): array
    {
        $prefix = DB::connection()->getTablePrefix();

        $male = DB::table('dates as married')
            ->select([
                DB::raw('ROUND(AVG(' . $prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 - 182.5) / 365.25, 1) AS age'),
                DB::raw('ROUND((' . $prefix . 'married.d_year + 49) / 100) AS century'),
                DB::raw("'M' as sex")
            ])
            ->join('families as fam', static function (JoinClause $join): void {
                $join->on('fam.f_id', '=', 'married.d_gid')
                    ->on('fam.f_file', '=', 'married.d_file');
            })
            ->join('dates as birth', static function (JoinClause $join): void {
                $join->on('birth.d_gid', '=', 'fam.f_husb')
                    ->on('birth.d_file', '=', 'fam.f_file');
            })
            ->whereIn('married.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('married.d_file', '=', $this->tree->id())
            ->where('married.d_fact', '=', 'MARR')
            ->where('married.d_julianday1', '>', 'birth.d_julianday1')
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('birth.d_julianday1', '<>', 0)
            ->groupBy(['century', 'sex']);

        $female = DB::table('dates as married')
            ->select([
                DB::raw('ROUND(AVG(' . $prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 - 182.5) / 365.25, 1) AS age'),
                DB::raw('ROUND((' . $prefix . 'married.d_year + 49) / 100) AS century'),
                DB::raw("'F' as sex")
            ])
            ->join('families as fam', static function (JoinClause $join): void {
                $join->on('fam.f_id', '=', 'married.d_gid')
                    ->on('fam.f_file', '=', 'married.d_file');
            })
            ->join('dates as birth', static function (JoinClause $join): void {
                $join->on('birth.d_gid', '=', 'fam.f_wife')
                    ->on('birth.d_file', '=', 'fam.f_file');
            })
            ->whereIn('married.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('married.d_file', '=', $this->tree->id())
            ->where('married.d_fact', '=', 'MARR')
            ->where('married.d_julianday1', '>', 'birth.d_julianday1')
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('birth.d_julianday1', '<>', 0)
            ->groupBy(['century', 'sex']);

        return $male->unionAll($female)
           ->orderBy('century')
           ->get()
           ->all();
    }

    /**
     * General query on ages at marriage.
     *
     * @return string
     */
    public function chartMarriageAge(): string
    {
        $out = [];

        foreach ($this->queryRecords() as $record) {
            $out[(int) $record->century][$record->sex] = (float) $record->age;
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
                $male_age,
                $female_age,
                $average_age,
            ];
        }

        $chart_title   = I18N::translate('Average age in century of marriage');
        $chart_options = [
            'title' => $chart_title,
            'subtitle' => I18N::translate('Average age at marriage'),
            'vAxis' => [
                'title' => I18N::translate('Age'),
            ],
            'hAxis' => [
                'title' => I18N::translate('Century'),
            ],
            'colors' => [
                '#84beff',
                '#ffd1dc',
                '#ff0000',
            ],
        ];

        return view(
            'statistics/other/charts/combo',
            [
                'data'          => $data,
                'chart_options' => $chart_options,
                'chart_title'   => $chart_title,
            ]
        );
    }
}
