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
use Fisharebest\Webtrees\Statistics\Helper\Century;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartDivorce extends AbstractGoogle
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
     * General query on divorces.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartDivorce(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $sql =
            "SELECT FLOOR(d_year/100+1) AS century, COUNT(*) AS total" .
            " FROM `##dates`" .
            " WHERE d_file={$this->tree->id()} AND d_year<>0 AND d_fact = 'DIV' AND d_type IN ('@#DGREGORIAN@', '@#DJULIAN@')";

        $sql .= " GROUP BY century ORDER BY century";

        $rows = $this->runSql($sql);

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes = explode('x', $size);
        $tot   = 0;
        foreach ($rows as $values) {
            $values->total = (int) $values->total;
            $tot += $values->total;
        }
        // Beware divide by zero
        if ($tot === 0) {
            return '';
        }
        $centuries = '';
        $counts    = [];

        foreach ($rows as $values) {
            $counts[] = intdiv(100 * $values->total, $tot);
            $centuries .= $this->centuryHelper->centuryName((int) $values->century) . ' - ' . I18N::number($values->total) . '|';
        }

        $chd = $this->arrayToExtendedEncoding($counts);
        $chl = substr($centuries, 0, -1);

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_from},{$color_to}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Divorces by century') . '" title="' . I18N::translate('Divorces by century') . '" />';
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
