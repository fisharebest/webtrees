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
use Fisharebest\Webtrees\Statistics\Helper\Century;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class ChartAge extends AbstractGoogle
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
        $this->tree = $tree;
        $this->centuryHelper = new Century();
    }

    /**
     * General query on ages.
     *
     * @param string $size
     *
     * @return string
     */
    public function chartAge($size = '230x250'): string
    {
        $sizes = explode('x', $size);
        $rows  = $this->runSql(
            "SELECT" .
            " ROUND(AVG(death.d_julianday2-birth.d_julianday1)/365.25,1) AS age," .
            " FLOOR(death.d_year/100+1) AS century," .
            " i_sex AS sex" .
            " FROM" .
            " `##dates` AS death," .
            " `##dates` AS birth," .
            " `##individuals` AS indi" .
            " WHERE" .
            " indi.i_id=birth.d_gid AND" .
            " birth.d_gid=death.d_gid AND" .
            " death.d_file={$this->tree->id()} AND" .
            " birth.d_file=death.d_file AND" .
            " birth.d_file=indi.i_file AND" .
            " birth.d_fact='BIRT' AND" .
            " death.d_fact='DEAT' AND" .
            " birth.d_julianday1<>0 AND" .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND" .
            " death.d_julianday1>birth.d_julianday2" .
            " GROUP BY century, sex ORDER BY century, sex"
        );

        if (empty($rows)) {
            return '';
        }

        $chxl    = '0:|';
        $countsm = '';
        $countsf = '';
        $countsa = '';
        $out     = [];

        foreach ($rows as $values) {
            $out[$values->century][$values->sex] = $values->age;
        }

        foreach ($out as $century => $values) {
            if ($sizes[0] < 980) {
                $sizes[0] += 50;
            }
            $chxl .= $this->centuryHelper->centuryName($century) . '|';

            $female_age  = $values['F'] ?? 0;
            $male_age    = $values['M'] ?? 0;
            $average_age = $female_age + $male_age;

            if ($female_age > 0 && $male_age > 0) {
                $average_age /= 2.0;
            }

            $countsf .= $female_age . ',';
            $countsm .= $male_age . ',';
            $countsa .= $average_age . ',';
        }

        $countsm = substr($countsm, 0, -1);
        $countsf = substr($countsf, 0, -1);
        $countsa = substr($countsa, 0, -1);
        $chd     = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;
        $decades = '';

        for ($i = 0; $i <= 100; $i += 10) {
            $decades .= '|' . I18N::number($i);
        }

        $chxl  .= '1:||' . I18N::translate('century') . '|2:' . $decades . '|3:||' . I18N::translate('Age') . '|';
        $title = I18N::translate('Average age related to death century');

        if (count($rows) > 6 || mb_strlen($title) < 30) {
            $chtt = $title;
        } else {
            $offset  = 0;
            $counter = [];

            while ($offset = strpos($title, ' ', $offset + 1)) {
                $counter[] = $offset;
            }

            $half = intdiv(count($counter), 2);
            $chtt = substr_replace($title, '|', $counter[$half], 1);
        }

        return '<img src="' . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|N*f1*,000000,0,-1,11,1|N*f1*,000000,1,-1,11,1&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . '&amp;chdl=' . rawurlencode(I18N::translate('Males') . '|' . I18N::translate('Females') . '|' . I18N::translate('Average age at death')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Average age related to death century') . '" title="' . I18N::translate('Average age related to death century') . '" />';
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }
}
