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

namespace Fisharebest\Webtrees\Statistics;

use Fisharebest\Webtrees\Statistics\Helper\Sql;

/**
 * Base class for all google charts.
 *
 * @deprecated The pie chart API is outdated and should be replaced
 *             by the newer version https://developers.google.com/chart/ or
 *             an open source one like chart.js
 *
 * @see https://developers.google.com/chart/image/docs/gallery/pie_charts
 */
abstract class AbstractGoogle
{
    // Used in Google charts
    public const GOOGLE_CHART_ENCODING = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';

    /**
     * Convert numbers to Google's custom encoding.
     *
     * @link http://bendodson.com/news/google-extended-encoding-made-easy
     *
     * @param int[] $a
     *
     * @return string
     */
    protected function arrayToExtendedEncoding(array $a): string
    {
        $xencoding = self::GOOGLE_CHART_ENCODING;
        $encoding  = '';

        foreach ($a as $value) {
            if ($value < 0) {
                $value = 0;
            }

            $first     = intdiv($value, 64);
            $second    = $value % 64;
            $encoding .= $xencoding[$first] . $xencoding[$second];
        }

        return $encoding;
    }

    /**
     * Returns the three-dimensional pie chart url.
     *
     * @param string $data
     * @param string $size
     * @param array  $colors
     * @param string $labels
     *
     * @return string
     */
    protected function getPieChartUrl(string $data, string $size, array $colors, string $labels): string
    {
        return 'https://chart.googleapis.com/chart?cht=p3&chd=e:' . $data
            . '&chs=' . $size . '&chco=' . implode(',', $colors) . '&chf=bg,s,ffffff00&chl='
            . $labels;
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    protected function runSql(string $sql): array
    {
        return Sql::runSql($sql);
    }
}
