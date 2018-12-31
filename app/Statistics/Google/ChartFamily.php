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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;
use Fisharebest\Webtrees\Statistics\Helper\Century;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartFamily extends AbstractGoogle
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
     * Create a chart of the largest families.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null,
        string $total      = '10'
    ): string {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_L_CHART_X    = Theme::theme()->parameter('stats-large-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? $WT_STATS_L_CHART_X . 'x' . $WT_STATS_S_CHART_Y;
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;
        $total      = $total ?? '10';

        $sizes = explode('x', $size);
        $total = (int) $total;
        $rows  = $this->runSql(
            " SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE f_file={$this->tree->id()}" .
            " ORDER BY tot DESC" .
            " LIMIT " . $total
        );
        if (!isset($rows[0])) {
            return '';
        }
        $tot = 0;
        foreach ($rows as $row) {
            $row->tot = (int) $row->tot;
            $tot += $row->tot;
        }
        $chd = '';
        $chl = [];
        foreach ($rows as $row) {
            $family = Family::getInstance($row->id, $this->tree);

            if ($family && $family->canShow()) {
                if ($tot === 0) {
                    $per = 0;
                } else {
                    $per = intdiv(100 * $row->tot, $tot);
                }

                $chd .= $this->arrayToExtendedEncoding([$per]);
                $chl[] = htmlspecialchars_decode(strip_tags($family->getFullName())) . ' - ' . I18N::number($row->tot);
            }
        }

        $chl = rawurlencode(implode('|', $chl));

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Largest families') . '" title="' . I18N::translate('Largest families') . '" />';
    }

    /**
     * Create a chart of individuals with/without sources.
     *
     * @param int         $tot_fam        The total number of families
     * @param int         $tot_fam_source The total number of families with sources
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartFamsWithSources(int $tot_fam, int $tot_fam_source, string $size = null, string $color_from = null, string $color_to = null): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes = explode('x', $size);

        if ($tot_fam === 0) {
            return '';
        }

        $tot_sfam_per  = $tot_fam_source / $tot_fam;
        $with          = (int) (100 * $tot_sfam_per);
        $chd           = $this->arrayToExtendedEncoding([100 - $with, $with]);
        $chl           = I18N::translate('Without sources') . ' - ' . I18N::percentage(1 - $tot_sfam_per, 1) . '|' . I18N::translate('With sources') . ' - ' . I18N::percentage($tot_sfam_per, 1);
        $chart_title   = I18N::translate('Families with sources');

        $chart_url = 'https://chart.googleapis.com/chart?cht=p3&chd=e:' . $chd
            . '&chs=' . $size . '&chco=' . $color_from . ',' . $color_to . '&chf=bg,s,ffffff00&chl=' . $chl;

        return view(
            'statistics/other/chart-families-with-sources',
            [
                'chart_title' => $chart_title,
                'chart_url'   => $chart_url,
                'sizes'       => $sizes,
            ]
        );
    }

    /**
     * Create a chart of children with no families.
     *
     * @param int    $no_child_fam The number of families with no children
     * @param string $size
     * @param string $year1
     * @param string $year2
     *
     * @return string
     */
    public function chartNoChildrenFamilies(int $no_child_fam, string $size = '220x200', string $year1 = '-1', string $year2 = '-1'): string
    {
        $year1 = (int) $year1;
        $year2 = (int) $year2;

        $sizes = explode('x', $size);

        if ($year1 >= 0 && $year2 >= 0) {
            $years = " married.d_year BETWEEN '{$year1}' AND '{$year2}' AND";
        } else {
            $years = '';
        }

        $max  = 0;
        $tot  = 0;
        $rows = $this->runSql(
            "SELECT" .
            " COUNT(*) AS count," .
            " FLOOR(married.d_year/100+1) AS century" .
            " FROM" .
            " `##families` AS fam" .
            " JOIN" .
            " `##dates` AS married ON (married.d_file = fam.f_file AND married.d_gid = fam.f_id)" .
            " WHERE" .
            " f_numchil = 0 AND" .
            " fam.f_file = {$this->tree->id()} AND" .
            $years .
            " married.d_fact = 'MARR' AND" .
            " married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')" .
            " GROUP BY century ORDER BY century"
        );

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
            $chxl     .= $this->centuryHelper->centuryName($values->century) . '|';
            $counts[] = intdiv(4095 * $values->count, $max + 1);
            $chm      .= 't' . $values->count . ',000000,0,' . $i . ',11,1|';
            $i++;
        }

        $counts[] = intdiv(4095 * $unknown, $max + 1);
        $chd      = $this->arrayToExtendedEncoding($counts);
        $chm      .= 't' . $unknown . ',000000,0,' . $i . ',11,1';
        $chxl     .= I18N::translateContext('unknown century', 'Unknown') . '|1:||' . I18N::translate('century') . '|2:|0|';
        $step     = $max + 1;

        for ($d = (int) ($max + 1); $d > 0; $d--) {
            if (($max + 1) < ($d * 10 + 1) && fmod($max + 1, $d) === 0) {
                $step = $d;
            }
        }

        if ($step === (int) ($max + 1)) {
            for ($d = (int) $max; $d > 0; $d--) {
                if ($max < ($d * 10 + 1) && fmod($max, $d) === 0) {
                    $step = $d;
                }
            }
        }

        for ($n = $step; $n <= ($max + 1); $n += $step) {
            $chxl .= $n . '|';
        }

        $chxl .= '3:||' . I18N::translate('Total families') . '|';

        return "<img src=\"https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chm=D,FF0000,0,0:" . ($i - 1) . ",3,1|{$chm}&amp;chd=e:{$chd}&amp;chco=0000FF,ffffff00&amp;chbh=30,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Number of families without children') . '" title="' . I18N::translate('Number of families without children') . '" />';
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }
}
