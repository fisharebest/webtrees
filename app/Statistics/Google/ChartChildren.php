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
class ChartChildren extends AbstractGoogle
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
     * @return \stdClass[]
     */
    private function queryRecords(): array
    {
        $query = DB::table('families')
            ->selectRaw('ROUND(AVG(f_numchil),2) AS num')
            ->selectRaw('ROUND((d_year - 50) / 100) AS century')
            ->join('dates', function (JoinClause $join) {
                $join->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('d_julianday1', '<>', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century');

        return $query->get()->all();
    }

    /**
     * General query on familes/children.
     *
     * @param string $size
     *
     * @return string
     */
    public function chartChildren(string $size = '220x200'): string
    {
        $sizes = explode('x', $size);
        $max   = 0;
        $rows  = $this->queryRecords();

        if (empty($rows)) {
            return '';
        }

        foreach ($rows as $values) {
            $values->num = (int) $values->num;
            if ($max < $values->num) {
                $max = $values->num;
            }
        }

        $chm    = '';
        $chxl   = '0:|';
        $i      = 0;
        $counts = [];

        foreach ($rows as $values) {
            $chxl .= $this->centuryHelper->centuryName((int) $values->century) . '|';
            if ($max <= 5) {
                $counts[] = (int) ($values->num * 819.2 - 1);
            } elseif ($max <= 10) {
                $counts[] = (int) ($values->num * 409.6);
            } else {
                $counts[] = (int) ($values->num * 204.8);
            }
            $chm .= 't' . $values->num . ',000000,0,' . $i . ',11,1|';
            $i++;
        }

        $chd = $this->arrayToExtendedEncoding($counts);
        $chm = substr($chm, 0, -1);

        if ($max <= 5) {
            $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|3:||' . I18N::translate('Number of children') . '|';
        } elseif ($max <= 10) {
            $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|6|7|8|9|10|3:||' . I18N::translate('Number of children') . '|';
        } else {
            $chxl .= '1:||' . I18N::translate('century') . '|2:|0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|3:||' . I18N::translate('Number of children') . '|';
        }

        $chart_url = 'https://chart.googleapis.com/chart?cht=bvg&amp;chs=' . $sizes[0] . 'x' . $sizes[1]
            . '&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0,3,1|' . $chm
            . '&amp;chd=e:' . $chd . '&amp;chco=0000FF&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl='
            . rawurlencode($chxl);

        return view(
            'statistics/other/chart-google',
            [
                'chart_title' => I18N::translate('Average number of children per family'),
                'chart_url'   => $chart_url,
                'sizes'       => $sizes,
            ]
        );
    }
}
