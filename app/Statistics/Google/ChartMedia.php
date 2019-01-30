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
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;

/**
 *
 */
class ChartMedia extends AbstractGoogle
{
    /**
     * Create a chart of media types.
     *
     * @param int         $tot        The total number of media files
     * @param array       $media      The list of media types to display
     * @param string|null $size
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartMedia(
        int $tot,
        array $media,
        string $size       = null,
        string $color_from = null,
        string $color_to   = null
    ): string {
        $chart_color1 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-no-values');
        $chart_color2 = (string) app()->make(ModuleThemeInterface::class)->parameter('distribution-chart-high-values');
        $chart_x      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-x');
        $chart_y      = app()->make(ModuleThemeInterface::class)->parameter('stats-small-chart-y');

        $size       = $size ?? ($chart_x . 'x' . $chart_y);
        $color_from = $color_from ?? $chart_color1;
        $color_to   = $color_to ?? $chart_color2;
        $sizes      = explode('x', $size);

        // Beware divide by zero
        if ($tot === 0) {
            return I18N::translate('None');
        }

        // Build a table listing only the media types actually present in the GEDCOM
        $mediaCounts = [];
        $mediaTypes  = '';
        $chart_title = '';

        foreach ($media as $type => $count) {
            $mediaCounts[] = intdiv(100 * $count, $tot);
            $mediaTypes    .= GedcomTag::getFileFormTypeValue($type) . ' - ' . I18N::number($count) . '|';
            $chart_title   .= GedcomTag::getFileFormTypeValue($type) . ' (' . $count . '), ';
        }

        $chart_title = substr($chart_title, 0, -2);
        $chd         = $this->arrayToExtendedEncoding($mediaCounts);
        $chl         = substr($mediaTypes, 0, -1);
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
