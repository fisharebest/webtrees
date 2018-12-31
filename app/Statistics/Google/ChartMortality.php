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
use Fisharebest\Webtrees\Theme;

/**
 *
 */
class ChartMortality extends AbstractGoogle
{
    /**
     * Create a chart showing mortality.
     *
     * @param int         $tot_l
     * @param int         $tot_d
     * @param string      $per_l
     * @param string      $per_d
     * @param string|null $size
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(
        int $tot_l,
        int $tot_d,
        string $per_l,
        string $per_d,
        string $size = null,
        string $color_living = null,
        string $color_dead = null
    ): string {
        // Raw data - for calculation
        $tot = $tot_l + $tot_d;

        if ($tot === 0) {
            return '';
        }

        $WT_STATS_S_CHART_X = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y = Theme::theme()->parameter('stats-small-chart-y');

        $size         = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_living = $color_living ?? 'ffffff';
        $color_dead   = $color_dead ?? 'cccccc';

        $sizes = explode('x', $size);

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
