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
class ChartMarriageAge extends AbstractGoogle
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
     * General query on ages at marriage.
     *
     * @param string $size
     *
     * @return string
     */
    public function chartMarriageAge($size = '200x250'): string
    {
        $sex   = 'BOTH';
        $sizes = explode('x', $size);

        $rows  = $this->runSql(
            "SELECT " .
            " ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
            " FLOOR(married.d_year/100+1) AS century, " .
            " 'M' AS sex " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_husb AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('M', 'BOTH') AND " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
            "GROUP BY century, sex " .
            "UNION ALL " .
            "SELECT " .
            " ROUND(AVG(married.d_julianday2-birth.d_julianday1-182.5)/365.25,1) AS age, " .
            " FLOOR(married.d_year/100+1) AS century, " .
            " 'F' AS sex " .
            "FROM `##dates` AS married " .
            "JOIN `##families` AS fam ON (married.d_gid=fam.f_id AND married.d_file=fam.f_file) " .
            "JOIN `##dates` AS birth ON (birth.d_gid=fam.f_wife AND birth.d_file=fam.f_file) " .
            "WHERE " .
            " '{$sex}' IN ('F', 'BOTH') AND " .
            " married.d_file={$this->tree->id()} AND married.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND married.d_fact='MARR' AND " .
            " birth.d_type IN ('@#DGREGORIAN@', '@#DJULIAN@') AND birth.d_fact='BIRT' AND " .
            " married.d_julianday1>birth.d_julianday1 AND birth.d_julianday1<>0 " .
            " GROUP BY century, sex ORDER BY century"
        );

        if (empty($rows)) {
            return '';
        }

        $max = 0;

        foreach ($rows as $values) {
            $values->age = (int) $values->age;
            if ($max < $values->age) {
                $max = $values->age;
            }
        }

        $chxl    = '0:|';
        $chmm    = '';
        $chmf    = '';
        $i       = 0;
        $countsm = '';
        $countsf = '';
        $countsa = '';
        $out     = [];

        foreach ($rows as $values) {
            $out[(int) $values->century][$values->sex] = $values->age;
        }

        foreach ($out as $century => $values) {
            if ($sizes[0] < 1000) {
                $sizes[0] += 50;
            }
            $chxl .= $this->centuryHelper->centuryName($century) . '|';
            $average = 0;
            if (isset($values['F'])) {
                if ($max <= 50) {
                    $value = $values['F'] * 2;
                } else {
                    $value = $values['F'];
                }
                $countsf .= $value . ',';
                $average = $value;
                $chmf    .= 't' . $values['F'] . ',000000,1,' . $i . ',11,1|';
            } else {
                $countsf .= '0,';
                $chmf    .= 't0,000000,1,' . $i . ',11,1|';
            }
            if (isset($values['M'])) {
                if ($max <= 50) {
                    $value = $values['M'] * 2;
                } else {
                    $value = $values['M'];
                }
                $countsm .= $value . ',';
                if ($average === 0) {
                    $countsa .= $value . ',';
                } else {
                    $countsa .= (($value + $average) / 2) . ',';
                }
                $chmm .= 't' . $values['M'] . ',000000,0,' . $i . ',11,1|';
            } else {
                $countsm .= '0,';
                if ($average === 0) {
                    $countsa .= '0,';
                } else {
                    $countsa .= $value . ',';
                }
                $chmm .= 't0,000000,0,' . $i . ',11,1|';
            }
            $i++;
        }

        $countsm = substr($countsm, 0, -1);
        $countsf = substr($countsf, 0, -1);
        $countsa = substr($countsa, 0, -1);
        $chmf    = substr($chmf, 0, -1);
        $chd     = 't2:' . $countsm . '|' . $countsf . '|' . $countsa;

        if ($max <= 50) {
            $chxl .= '1:||' . I18N::translate('century') . '|2:|0|10|20|30|40|50|3:||' . I18N::translate('Age') . '|';
        } else {
            $chxl .= '1:||' . I18N::translate('century') . '|2:|0|10|20|30|40|50|60|70|80|90|100|3:||' . I18N::translate('Age') . '|';
        }

        if (\count($rows) > 4 || mb_strlen(I18N::translate('Average age in century of marriage')) < 30) {
            $chtt = I18N::translate('Average age in century of marriage');
        } else {
            $offset  = 0;
            $counter = [];

            while ($offset = strpos(I18N::translate('Average age in century of marriage'), ' ', $offset + 1)) {
                $counter[] = $offset;
            }

            $half = intdiv(\count($counter), 2);
            $chtt = substr_replace(I18N::translate('Average age in century of marriage'), '|', $counter[$half], 1);
        }

        return '<img src="' . "https://chart.googleapis.com/chart?cht=bvg&amp;chs={$sizes[0]}x{$sizes[1]}&amp;chm=D,FF0000,2,0,3,1|{$chmm}{$chmf}&amp;chf=bg,s,ffffff00|c,s,ffffff00&amp;chtt=" . rawurlencode($chtt) . "&amp;chd={$chd}&amp;chco=0000FF,FFA0CB,FF0000&amp;chbh=20,3&amp;chxt=x,x,y,y&amp;chxl=" . rawurlencode($chxl) . '&amp;chdl=' . rawurlencode(I18N::translate('Males') . '|' . I18N::translate('Females') . '|' . I18N::translate('Average age')) . "\" width=\"{$sizes[0]}\" height=\"{$sizes[1]}\" alt=\"" . I18N::translate('Average age in century of marriage') . '" title="' . I18N::translate('Average age in century of marriage') . '" />';
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
