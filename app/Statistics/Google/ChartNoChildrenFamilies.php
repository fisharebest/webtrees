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
class ChartNoChildrenFamilies extends AbstractGoogle
{
    /**
     * @var Tree
     */
    private $tree;

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
        $this->tree          = $tree;
        $this->centuryHelper = new Century();
    }

    /**
     * Returns the related database records.
     *
     * @param int $year1
     * @param int $year2
     *
     * @return \stdClass[]
     */
    private function queryRecords(int $year1, int $year2): array
    {
        $query = DB::table('families')
            ->selectRaw('ROUND((d_year - 50) / 100) AS century')
            ->selectRaw('COUNT(*) AS count')
            ->join('dates', function (JoinClause $join) {
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
     * @param int    $no_child_fam The number of families with no children
     * @param string $size
     * @param int    $year1
     * @param int    $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(
        int $no_child_fam,
        string $size = '220x200',
        int $year1   = -1,
        int $year2   = -1
    ): string {
        $sizes = explode('x', $size);
        $max   = 0;
        $tot   = 0;
        $rows  = $this->queryRecords($year1, $year2);

        if (empty($rows)) {
            return '';
        }

        foreach ($rows as $values) {
            $values->count = (int) $values->count;

            if ($max < $values->count) {
                $max = $values->count;
            }
            $tot += $values->count;
        }

        $unknown = $no_child_fam - $tot;

        if ($unknown > $max) {
            $max = $unknown;
        }

        $chm    = '';
        $chxl   = '0:|';
        $i      = 0;
        $counts = [];

        foreach ($rows as $values) {
            $chxl     .= $this->centuryHelper->centuryName((int) $values->century) . '|';
            $counts[] = intdiv(4095 * $values->count, $max + 1);
            $chm      .= 't' . $values->count . ',000000,0,' . $i . ',11,1|';
            $i++;
        }

        $counts[] = intdiv(4095 * $unknown, $max + 1);
        $chd      = $this->arrayToExtendedEncoding($counts);
        $chm      .= 't' . $unknown . ',000000,0,' . $i . ',11,1';
        $chxl     .= I18N::translateContext('unknown century', 'Unknown') . '|1:||' . I18N::translate('century') . '|2:|0|';
        $step     = $max + 1;

        for ($d = ($max + 1); $d > 0; $d--) {
            if (($max + 1) < ($d * 10 + 1) && fmod($max + 1, $d) === 0) {
                $step = $d;
            }
        }

        if ($step === ($max + 1)) {
            for ($d = $max; $d > 0; $d--) {
                if ($max < ($d * 10 + 1) && fmod($max, $d) === 0) {
                    $step = $d;
                }
            }
        }

        for ($n = $step; $n <= ($max + 1); $n += $step) {
            $chxl .= $n . '|';
        }

        $chxl .= '3:||' . I18N::translate('Total families') . '|';

        $chart_url = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=' . $sizes[0] . 'x' . $sizes[1]
            . '&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:'
            . ($i - 1) . ',3,1|' . $chm . '&amp;chd=e:'
            . $chd . '&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl='
            . rawurlencode($chxl);

        return view(
            'statistics/other/chart-google',
            [
                'chart_title' => I18N::translate('Number of families without children'),
                'chart_url'   => $chart_url,
                'sizes'       => $sizes,
            ]
        );
    }
}
