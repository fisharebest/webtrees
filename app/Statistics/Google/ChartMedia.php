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

use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Google;
use Fisharebest\Webtrees\Statistics\Repository\MediaRepository;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartMedia extends Google
{
    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->mediaRepository = new MediaRepository($tree);
    }

    /**
     * Create a chart of media types.
     *
     * @param int         $tot        The total number of media files
     * @param array       $med_types  The list of available media types
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(int $tot, array $med_types, string $size = null, string $color_from = null, string $color_to = null): string
    {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_S_CHART_X    = Theme::theme()->parameter('stats-small-chart-x');
        $WT_STATS_S_CHART_Y    = Theme::theme()->parameter('stats-small-chart-y');

        $size       = $size ?? ($WT_STATS_S_CHART_X . 'x' . $WT_STATS_S_CHART_Y);
        $color_from = $color_from ?? $WT_STATS_CHART_COLOR1;
        $color_to   = $color_to ?? $WT_STATS_CHART_COLOR2;

        $sizes = explode('x', $size);

        // Beware divide by zero
        if ($tot === 0) {
            return I18N::translate('None');
        }

        // Build a table listing only the media types actually present in the GEDCOM
        $mediaCounts = [];
        $mediaTypes  = '';
        $chart_title = '';
        $c           = 0;
        $max         = 0;
        $media       = [];

        foreach ($med_types as $type) {
            $count = $this->mediaRepository->totalMediaType($type);
            if ($count > 0) {
                $media[$type] = $count;
                if ($count > $max) {
                    $max = $count;
                }
                $c += $count;
            }
        }
        $count = $this->mediaRepository->totalMediaType('unknown');
        if ($count > 0) {
            $media['unknown'] = $tot - $c;
            if ($tot - $c > $max) {
                $max = $count;
            }
        }
        if (($max / $tot) > 0.6 && count($media) > 10) {
            arsort($media);
            $media = array_slice($media, 0, 10);
            $c     = $tot;
            foreach ($media as $cm) {
                $c -= $cm;
            }
            if (isset($media['other'])) {
                $media['other'] += $c;
            } else {
                $media['other'] = $c;
            }
        }
        asort($media);
        foreach ($media as $type => $count) {
            $mediaCounts[] = intdiv(100 * $count, $tot);
            $mediaTypes    .= GedcomTag::getFileFormTypeValue($type) . ' - ' . I18N::number($count) . '|';
            $chart_title   .= GedcomTag::getFileFormTypeValue($type) . ' (' . $count . '), ';
        }
        $chart_title = substr($chart_title, 0, -2);
        $chd         = $this->arrayToExtendedEncoding($mediaCounts);
        $chl         = substr($mediaTypes, 0, -1);

        $chart_url = 'https://chart.googleapis.com/chart?cht=p3&chd=e:' . $chd
            . '&chs=' . $size . '&chco=' . $color_from . ',' . $color_to . '&chf=bg,s,ffffff00&chl=' . $chl;

        return view(
            'statistics/other/chart-media',
            [
                'chart_title' => $chart_title,
                'chart_url'   => $chart_url,
                'sizes'       => $sizes,
                'color_from'  => $color_from,
                'color_to'    => $color_to,
                'mediaTypes'  => $media,
            ]
        );
    }
}
