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
use Fisharebest\Webtrees\Statistics\Repository\IndividualRepository;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartSex extends AbstractGoogle
{
    /**
     * @var IndividualRepository
     */
    private $individualRepository;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->individualRepository = new IndividualRepository($tree);
    }

    /**
     * Generate a chart showing sex distribution.
     *
     * @param int         $tot_m         The total number of male individuals
     * @param int         $tot_f         The total number of female individuals
     * @param int         $tot_u         The total number of unknown individuals
     * @param string|null $size
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        int $tot_m,
        int $tot_f,
        int $tot_u,
        string $size          = null,
        string $color_female  = null,
        string $color_male    = null,
        string $color_unknown = null
    ): string {
        $chart_x = Theme::theme()->parameter('stats-small-chart-x');
        $chart_y = Theme::theme()->parameter('stats-small-chart-y');

        $size          = $size ?? ($chart_x . 'x' . $chart_y);
        $color_female  = $color_female ?? 'ffd1dc';
        $color_male    = $color_male ?? '84beff';
        $color_unknown = $color_unknown ?? '777777';

        $sizes = explode('x', $size);

        // Raw data - for calculation
        $tot = $tot_f + $tot_m + $tot_u;

        // I18N data - for display
        $per_f = $this->individualRepository->totalSexFemalesPercentage();
        $per_m = $this->individualRepository->totalSexMalesPercentage();
        $per_u = $this->individualRepository->totalSexUnknownPercentage();

        if ($tot === 0) {
            return '';
        }

        if ($tot_u > 0) {
            $chd = $this->arrayToExtendedEncoding([
                intdiv(4095 * $tot_u, $tot),
                intdiv(4095 * $tot_f, $tot),
                intdiv(4095 * $tot_m, $tot),
            ]);

            $chl =
                I18N::translateContext('unknown people', 'Unknown') . ' - ' . $per_u . '|' .
                I18N::translate('Females') . ' - ' . $per_f . '|' .
                I18N::translate('Males') . ' - ' . $per_m;

            $chart_title =
                I18N::translate('Males') . ' - ' . $per_m . I18N::$list_separator .
                I18N::translate('Females') . ' - ' . $per_f . I18N::$list_separator .
                I18N::translateContext('unknown people', 'Unknown') . ' - ' . $per_u;

            $colors = [$color_unknown, $color_female, $color_male];
        } else {
            $chd = $this->arrayToExtendedEncoding([
                intdiv(4095 * $tot_f, $tot),
                intdiv(4095 * $tot_m, $tot),
            ]);

            $chl =
                I18N::translate('Females') . ' - ' . $per_f . '|' .
                I18N::translate('Males') . ' - ' . $per_m;

            $chart_title =
                I18N::translate('Males') . ' - ' . $per_m . I18N::$list_separator .
                I18N::translate('Females') . ' - ' . $per_f;

            $colors = [$color_female, $color_male];
        }

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
