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
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *
 */
class ChartFamilyLargest extends AbstractGoogle
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
     * Returns the related database records.
     *
     * @param int $total
     *
     * @return \stdClass[]
     */
    private function queryRecords(int $total): array
    {
        $query = DB::table('families')
            ->select(['f_numchil AS tot', 'f_id AS id'])
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('tot', 'desc')
            ->limit($total);

        return $query->get()->all();
    }

    /**
     * Create a chart of the largest families.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param int         $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string $size       = null,
        string $color_from = null,
        string $color_to   = null,
        int    $total      = 10
    ): string {
        $chart_color1 = (string) Theme::theme()->parameter('distribution-chart-no-values');
        $chart_color2 = (string) Theme::theme()->parameter('distribution-chart-high-values');
        $chart_x      = Theme::theme()->parameter('stats-large-chart-x');
        $chart_y      = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? $chart_x . 'x' . $chart_y;
        $color_from = $color_from ?? $chart_color1;
        $color_to   = $color_to ?? $chart_color2;
        $sizes      = explode('x', $size);
        $rows       = $this->queryRecords($total);

        if (!isset($rows[0])) {
            return '';
        }

        $tot = 0;
        foreach ($rows as $row) {
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

        $chl    = rawurlencode(implode('|', $chl));
        $colors = [$color_from, $color_to];

        return view(
            'statistics/other/chart-google',
            [
                'chart_title' => I18N::translate('Largest families'),
                'chart_url'   => $this->getPieChartUrl($chd, $size, $colors, $chl),
                'sizes'       => $sizes,
            ]
        );
    }
}
