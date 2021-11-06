<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Functions;

/**
 * Class Functions - common functions
 */
class Functions
{
    /**
     * get a gedcom subrecord
     *
     * searches a gedcom record and returns a subrecord of it. A subrecord is defined starting at a
     * line with level N and all subsequent lines greater than N until the next N level is reached.
     * For example, the following is a BIRT subrecord:
     * <code>1 BIRT
     * 2 DATE 1 JAN 1900
     * 2 PLAC Phoenix, Maricopa, Arizona</code>
     * The following example is the DATE subrecord of the above BIRT subrecord:
     * <code>2 DATE 1 JAN 1900</code>
     *
     * @param int    $level   the N level of the subrecord to get
     * @param string $tag     a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
     * @param string $gedrec  the parent gedcom record to search in
     * @param int    $num     this allows you to specify which matching <var>$tag</var> to get. Oftentimes a
     *                        gedcom record will have more that 1 of the same type of subrecord. An individual may have
     *                        multiple events for example. Passing $num=1 would get the first 1. Passing $num=2 would get the
     *                        second one, etc.
     *
     * @return string the subrecord that was found or an empty string "" if not found.
     */
    public static function getSubRecord(int $level, string $tag, string $gedrec, int $num = 1): string
    {
        if ($gedrec === '') {
            return '';
        }
        // -- adding \n before and after gedrec
        $gedrec       = "\n" . $gedrec . "\n";
        $tag          = trim($tag);
        $searchTarget = "~[\n]" . $tag . "[\s]~";
        $ct           = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        if ($ct === 0) {
            return '';
        }
        if ($ct < $num) {
            return '';
        }
        $pos1 = $match[$num - 1][0][1];
        $pos2 = strpos($gedrec, "\n$level", $pos1 + 1);
        if (!$pos2) {
            $pos2 = strpos($gedrec, "\n1", $pos1 + 1);
        }
        if (!$pos2) {
            $pos2 = strpos($gedrec, "\nWT_", $pos1 + 1); // WT_SPOUSE, WT_FAMILY_ID ...
        }
        if (!$pos2) {
            return ltrim(substr($gedrec, $pos1));
        }
        $subrec = substr($gedrec, $pos1, $pos2 - $pos1);

        return ltrim($subrec);
    }

    /**
     * get CONT lines
     *
     * get the N+1 CONT or CONC lines of a gedcom subrecord
     *
     * @param int    $nlevel the level of the CONT lines to get
     * @param string $nrec   the gedcom subrecord to search in
     *
     * @return string a string with all CONT lines merged
     */
    public static function getCont(int $nlevel, string $nrec): string
    {
        $text = '';

        $subrecords = explode("\n", $nrec);
        foreach ($subrecords as $thisSubrecord) {
            if (substr($thisSubrecord, 0, 2) !== $nlevel . ' ') {
                continue;
            }
            $subrecordType = substr($thisSubrecord, 2, 4);
            if ($subrecordType === 'CONT') {
                $text .= "\n" . substr($thisSubrecord, 7);
            }
        }

        return $text;
    }
}
