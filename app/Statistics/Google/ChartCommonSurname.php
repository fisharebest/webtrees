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
use Fisharebest\Webtrees\Statistics\Google;
use Fisharebest\Webtrees\Statistics\Individual;
use Fisharebest\Webtrees\Statistics\Surname;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartCommonSurname extends Google
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var Individual
     */
    private $individual;

    /**
     * @var Surname
     */
    private $surname;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree       = $tree;
        $this->individual = new Individual($tree);
        $this->surname    = new Surname($tree);
    }

    /**
     * Create a chart of common surnames.
     *
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     * @param string      $number_of_surnames
     *
     * @return string
     */
    public function chartCommonSurnames(
        string $size = null,
        string $color_from = null,
        string $color_to = null,
        string $number_of_surnames = '10'
    ): string {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size               = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from         = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to           = $color_to ?? $WT_STATS_CHART_COLOR2;
        $number_of_surnames = (int) $number_of_surnames;

        $sizes    = explode('x', $size);
        $tot_indi = $this->individual->totalIndividualsQuery();

        $all_surnames = $this->surname->topSurnames($number_of_surnames, 0);

        if (empty($all_surnames)) {
            return '';
        }

        $SURNAME_TRADITION = $this->tree->getPreference('SURNAME_TRADITION');

        $tot = 0;

        foreach ($all_surnames as $surn => $surnames) {
            $tot += array_sum($surnames);
        }

        $chd = '';
        $chl = [];
        foreach ($all_surnames as $surns) {
            $count_per = 0;
            $max_name  = 0;
            $top_name  = '';
            foreach ($surns as $spfxsurn => $count) {
                $per = $count;
                $count_per += $per;
                // select most common surname from all variants
                if ($per > $max_name) {
                    $max_name = $per;
                    $top_name = $spfxsurn;
                }
            }
            switch ($SURNAME_TRADITION) {
                case 'polish':
                    // most common surname should be in male variant (Kowalski, not Kowalska)
                    $top_name = preg_replace([
                        '/ska$/',
                        '/cka$/',
                        '/dzka$/',
                        '/żka$/',
                    ], [
                        'ski',
                        'cki',
                        'dzki',
                        'żki',
                    ], $top_name);
            }
            $per   = intdiv(100 * $count_per, $tot_indi);
            $chd .= $this->arrayToExtendedEncoding([$per]);
            $chl[] = $top_name . ' - ' . I18N::number($count_per);
        }

        $per   = intdiv(100 * ($tot_indi - $tot), $tot_indi);
        $chd .= $this->arrayToExtendedEncoding([$per]);
        $chl[] = I18N::translate('Other') . ' - ' . I18N::number($tot_indi - $tot);

        $chart_title = implode(I18N::$list_separator, $chl);
        $chl         = implode('|', $chl);

        return '<img src="https://chart.googleapis.com/chart?cht=p3&amp;chd=e:' . $chd . '&amp;chs='
            . $size . '&amp;chco=' . $color_from . ',' . $color_to . '&amp;chf=bg,s,ffffff00&amp;chl='
            . rawurlencode($chl) . '" width="' . $sizes[0] . '" height="' . $sizes[1] . '" alt="'
            . $chart_title . '" title="' . $chart_title . '" />';
    }
}
