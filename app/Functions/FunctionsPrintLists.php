<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Tree;

/**
 * Class FunctionsPrintLists - create sortable lists using datatables.net
 */
class FunctionsPrintLists
{
    /**
     * Print a tagcloud of surnames.
     *
     * @param int[][]                  $surnames array (of SURN, of array of SPFX_SURN, of counts)
     * @param ModuleListInterface|null $module
     * @param bool                     $totals   show totals after each name
     * @param Tree                     $tree     generate links to this tree
     *
     * @return string
     */
    public static function surnameTagCloud(array $surnames, ?ModuleListInterface $module, bool $totals, Tree $tree): string
    {
        $minimum = PHP_INT_MAX;
        $maximum = 1;
        foreach ($surnames as $surn => $surns) {
            foreach ($surns as $spfxsurn => $count) {
                $maximum = max($maximum, $count);
                $minimum = min($minimum, $count);
            }
        }

        $html = '';
        foreach ($surnames as $surn => $surns) {
            foreach ($surns as $spfxsurn => $count) {
                if ($maximum === $minimum) {
                    // All surnames occur the same number of times
                    $size = 150.0;
                } else {
                    $size = 75.0 + 125.0 * ($count - $minimum) / ($maximum - $minimum);
                }

                $tag = ($module instanceof ModuleListInterface) ? 'a' : 'span';
                $html .= '<' . $tag . ' style="font-size:' . $size . '%"';
                if ($module instanceof ModuleListInterface) {
                    $url = $module->listUrl($tree, ['surname' => $surn]);
                    $html .= ' href="' . e($url) . '"';
                }
                $html .= '>';
                if ($totals) {
                    $html .= I18N::translate('%1$s (%2$s)', '<span dir="auto">' . $spfxsurn . '</span>', I18N::number($count));
                } else {
                    $html .= $spfxsurn;
                }
                $html .= '</' . $tag . '> ';
            }
        }

        return '<div class="tag_cloud">' . $html . '</div>';
    }

    /**
     * Print a list of surnames.
     *
     * @param int[][]                  $surnames counts of surname by variant
     * @param int                      $style    1=bullet list, 2=semicolon-separated list, 3=tabulated list with up to 4 columns
     * @param bool                     $totals   show totals after each name
     * @param ModuleListInterface|null $module
     * @param Tree                     $tree     Link back to the individual list in this tree
     *
     * @return string
     */
    public static function surnameList($surnames, $style, $totals, ?ModuleListInterface $module, Tree $tree): string
    {
        $html = [];
        foreach ($surnames as $surn => $surns) {
            // Each surname links back to the indilist
            if ($module instanceof ModuleListInterface) {
                if ($surn) {
                    $url = $module->listUrl($tree, ['surname' => $surn]);
                } else {
                    $url = $module->listUrl($tree, ['alpha'  => ',']);
                }
            } else {
                $url = null;
            }

            // If all the surnames are just case variants, then merge them into one
            // Comment out this block if you want SMITH listed separately from Smith
            $tag = ($module instanceof ModuleListInterface) ? 'a' : 'span';
            $subhtml = '<' . $tag;
            if ($url !== null) {
                $subhtml .= ' href="' . e($url) . '"';
            }
            $subhtml .= ' dir="auto">' . e(implode(I18N::$list_separator, array_keys($surns))) . '</' . $tag . '>';

            if ($totals) {
                $subtotal = 0;
                foreach ($surns as $count) {
                    $subtotal += $count;
                }
                $subhtml .= '&nbsp;(' . I18N::number($subtotal) . ')';
            }
            $html[] = $subhtml;
        }
        switch ($style) {
            default:
            case 1:
                return '<ul><li>' . implode('</li><li>', $html) . '</li></ul>';
            case 2:
                return implode(I18N::$list_separator, $html);
            case 3:
                $count = count($html);
                if ($count > 36) {
                    $col = 4;
                } elseif ($count > 18) {
                    $col = 3;
                } elseif ($count > 6) {
                    $col = 2;
                } else {
                    $col = 1;
                }
                $newcol = (int) ceil($count / $col);
                $html2  = '<table class="table list_table"><tr>';
                $html2 .= '<td class="list_value">';

                foreach ($html as $i => $surns) {
                    $html2 .= $surns . '<br>';
                    if (($i + 1) % $newcol === 0) {
                        $html2 .= '</td><td class="list_value">';
                    }
                }
                $html2 .= '</td></tr></table>';

                return $html2;
        }
    }
}
