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
use Fisharebest\Webtrees\Statistics\Helper\Century;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

/**
 *
 */
class ChartAge extends AbstractGoogle
{
    /**
     * @var Century
     */
    private $centuryHelper;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        parent::__construct($tree);

        $this->centuryHelper = new Century();
    }

    /**
     * Returns the related database records.
     *
     * @return \stdClass[]
     */
    private function queryRecords(): array
    {
        $prefix = DB::connection()->getTablePrefix();

        return DB::table('individuals')
            ->select([
                DB::raw('ROUND(AVG(' . $prefix . 'death.d_julianday2 - ' . $prefix . 'birth.d_julianday1) / 365.25,1) AS age'),
                DB::raw('ROUND((' . $prefix . 'death.d_year - 50) / 100) AS century'),
                'i_sex AS sex'
            ])
            ->join('dates AS birth', function (JoinClause $join): void {
                $join
                    ->on('birth.d_file', '=', 'i_file')
                    ->on('birth.d_gid', '=', 'i_id');
            })
            ->join('dates AS death', function (JoinClause $join): void {
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
            ->all();
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
                $this->centuryHelper->centuryName($century),
                $male_age,
                $female_age,
                $average_age,
            ];
        }

        return view(
            'statistics/other/charts/combo',
            [
                'data'            => $data,
                'colors'          => ['#84beff', '#ffd1dc', '#ff0000'],
                'chart_title'     => I18N::translate('Average age related to death century'),
                'chart_sub_title' => I18N::translate('Average age at death'),
                'hAxis_title'     => I18N::translate('Century'),
                'vAxis_title'     => I18N::translate('Age'),
            ]
        );
    }
}
