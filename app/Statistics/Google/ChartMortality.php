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
use Fisharebest\Webtrees\Statistics\Deceased;
use Fisharebest\Webtrees\Statistics\Google;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Statistics\Living;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartMortality extends Google
{
    /**
     * @var Living
     */
    private $living;

    /**
     * @var Deceased
     */
    private $deceased;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->living   = new Living($tree);
        $this->deceased = new Deceased($tree);
    }

    /**
     * Create a chart showing mortality.
     *
     * @param string|null $size
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(string $size = null, string $color_living = null, string $color_dead = null): string
    {
        $WT_STATS_S_CHART_X = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y = Theme::theme()->parameter('stats-small-chart-y');

        $size         = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_living = $color_living ?? 'ffffff';
        $color_dead   = $color_dead ?? 'cccccc';

        $sizes = explode('x', $size);

        // Raw data - for calculation
        $tot_l = $this->living->totalLivingQuery();
        $tot_d = $this->deceased->totalDeceasedQuery();
        $tot   = $tot_l + $tot_d;

        // I18N data - for display
        $per_l = $this->living->totalLivingPercentage();
        $per_d = $this->deceased->totalDeceasedPercentage();

        if ($tot === 0) {
            return '';
        }

        $chd = $this->arrayToExtendedEncoding([
            intdiv(4095 * $tot_l, $tot),
            intdiv(4095 * $tot_d, $tot),
        ]);

        $chl =
            I18N::translate('Living') . ' - ' . $per_l . '|' .
            I18N::translate('Dead') . ' - ' . $per_d . '|';

        $chart_title =
            I18N::translate('Living') . ' - ' . $per_l . I18N::$list_separator .
            I18N::translate('Dead') . ' - ' . $per_d;

        return "<img src=\"https://chart.googleapis.com/chart?cht=p3&amp;chd=e:{$chd}&amp;chs={$size}&amp;chco={$color_living},{$color_dead}&amp;chf=bg,s,ffffff00&amp;chl={$chl}\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . $chart_title . '" title="' . $chart_title . '" />';
    }
}
