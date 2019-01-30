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
class ChartMortality extends AbstractGoogle
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
     * Create a chart showing mortality.
     *
     * @param int         $tot_l
     * @param int         $tot_d
     * @param string|null $size
     * @param string|null $color_living
     * @param string|null $color_dead
     *
     * @return string
     */
    public function chartMortality(
        int $tot_l,
        int $tot_d,
        string $size = null,
        string $color_living = null,
        string $color_dead = null
    ): string {
        // Raw data - for calculation
        $tot = $tot_l + $tot_d;

        if ($tot === 0) {
            return '';
        }

        $chart_x = Theme::theme()->parameter('stats-small-chart-x');
        $chart_y = Theme::theme()->parameter('stats-small-chart-y');

        $size         = $size ?? ($chart_x . 'x' . $chart_y);
        $color_living = $color_living ?? 'ffffff';
        $color_dead   = $color_dead ?? 'cccccc';

        $sizes = explode('x', $size);

        $chd = $this->arrayToExtendedEncoding([
            intdiv(4095 * $tot_l, $tot),
            intdiv(4095 * $tot_d, $tot),
        ]);

        $per_l = $this->individualRepository->totalLivingPercentage();
        $per_d = $this->individualRepository->totalDeceasedPercentage();

        $chl =
            I18N::translate('Living') . ' - ' . $per_l . '|' .
            I18N::translate('Dead') . ' - ' . $per_d . '|';

        $chart_title =
            I18N::translate('Living') . ' - ' . $per_l . I18N::$list_separator .
            I18N::translate('Dead') . ' - ' . $per_d;

        $colors = [$color_living, $color_dead];

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
