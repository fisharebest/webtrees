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
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;
use Fisharebest\Webtrees\Theme;

/**
 *
 */
class ChartCommonGiven extends AbstractGoogle
{
    /**
     * Create a chart of common given names.
     *
     * @param int         $tot_indi   The total number of individuals
     * @param array       $given      The list of common given names
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartCommonGiven(
        int $tot_indi,
        array $given,
        string $size = null,
        string $color_from = null,
        string $color_to = null
    ) : string {
        $chart_color1 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-no-values');
        $chart_color2 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-high-values');
        $chart_x      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-x');
        $chart_y      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-y');

        $size       = $size ?? ($chart_x . 'x' . $chart_y);
        $color_from = $color_from ?? $chart_color1;
        $color_to   = $color_to ?? $chart_color2;
        $sizes      = explode('x', $size);

        if (empty($given)) {
            return '';
        }

        $tot = 0;
        foreach ($given as $count) {
            $tot += $count;
        }

        $chd = '';
        $chl = [];

        foreach ($given as $givn => $count) {
            if ($tot === 0) {
                $per = 0;
            } else {
                $per = intdiv(100 * $count, $tot_indi);
            }
            $chd .= $this->arrayToExtendedEncoding([$per]);
            $chl[] = $givn . ' - ' . I18N::number($count);
        }

        $per   = intdiv(100 * ($tot_indi - $tot), $tot_indi);
        $chd .= $this->arrayToExtendedEncoding([$per]);
        $chl[] = I18N::translate('Other') . ' - ' . I18N::number($tot_indi - $tot);

        $chart_title = implode(I18N::$list_separator, $chl);
        $chl         = rawurlencode(implode('|', $chl));
        $colors      = [$color_from, $color_to];

        return view(
            'statistics/other/chart-google',
            [
                'chart_title' => $chart_title,
                'chart_url'   => $this->getPieChartUrl($chd, $size, $colors, $chl),
                'sizes'       => $sizes,
            ]
        );
    }
}
