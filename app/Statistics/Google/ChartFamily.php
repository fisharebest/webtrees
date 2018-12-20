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
use Fisharebest\Webtrees\Statistics\Google;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartFamily extends Google
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
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
