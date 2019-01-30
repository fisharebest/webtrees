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
use Fisharebest\Webtrees\Statistics\Helper\Century;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *
 */
class ChartBirth extends AbstractGoogle
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
        $query = DB::table('dates')
            ->selectRaw('FLOOR(d_year / 100 + 1) AS century')
            ->selectRaw('COUNT(*) AS total')
            ->where('d_file', '=', $this->tree->id())
            ->where('d_year', '<>', 0)
            ->where('d_fact', '=', 'BIRT')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century');

        return $query->get()->all();
    }

    /**
     * Create a chart of birth places.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartBirth(string $size = null, string $color_from = null, string $color_to = null): string
    {
        $chart_color1 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-no-values');
        $chart_color2 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-high-values');
        $chart_x      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-x');
        $chart_y      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-y');

        $size       = $size ?? ($chart_x . 'x' . $chart_y);
        $color_from = $color_from ?? $chart_color1;
        $color_to   = $color_to ?? $chart_color2;

        $sizes = explode('x', $size);
        $tot   = 0;
        $rows  = $this->queryRecords();

        foreach ($rows as $values) {
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
            $centuries .= $this->centuryHelper->centuryName($values->century) . ' - ' . I18N::number($values->total) . '|';
        }

        $chd    = $this->arrayToExtendedEncoding($counts);
        $chl    = rawurlencode(substr($centuries, 0, -1));
        $colors = [$color_from, $color_to];

        return view(
            'statistics/other/chart-google',
            [
                'chart_title' => I18N::translate('Births by century'),
                'chart_url'   => $this->getPieChartUrl($chd, $size, $colors, $chl),
                'sizes'       => $sizes,
            ]
        );
    }
}
