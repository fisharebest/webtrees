<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function explode;
use function ltrim;
use function preg_match;
use function preg_match_all;
use function strpos;
use function strtr;
use function substr;
use function trim;

/**
 * Pure utility methods for extracting data from raw GEDCOM record strings.
 *
 * These operate on the textual representation of GEDCOM records (as stored
 * in the database) without requiring parsed objects.  They have no external
 * state or side effects.
 */
final class GedcomTextReader
{
    /**
     * Extract a sub-record from a GEDCOM record string.
     *
     * @param int    $level  The level of the sub-record to find
     * @param string $tag    The level+tag prefix to search for (e.g. "1 BIRT")
     * @param string $gedrec The GEDCOM record to search within
     * @param int    $num    Which occurrence to return (1-based)
     */
    public static function getSubRecord(int $level, string $tag, string $gedrec, int $num = 1): string
    {
        if ($gedrec === '') {
            return '';
        }
        // Adding \n before and after gedrec to simplify boundary matching
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
        $pos1 = (int) $match[$num - 1][0][1];
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
     * Get CONT lines from a GEDCOM sub-record.
     *
     * Extracts and merges all CONT continuation lines at the given level,
     * returning them as a single string with newlines preserved.
     *
     * @param int    $level The level of the CONT lines to extract
     * @param string $record The GEDCOM sub-record to search within
     */
    public static function getCont(int $level, string $record): string
    {
        $text = '';

        $subrecords = explode("\n", $record);
        foreach ($subrecords as $thisSubrecord) {
            if (substr($thisSubrecord, 0, 2) !== $level . ' ') {
                continue;
            }
            $subrecordType = substr($thisSubrecord, 2, 4);
            if ($subrecordType === 'CONT') {
                $text .= "\n" . substr($thisSubrecord, 7);
            }
        }

        return $text;
    }

    /**
     * Extract a value from a GEDCOM record, walking a colon-delimited tag path.
     *
     * For example, getGedcomValue('BIRT:DATE', 1, $gedrec) returns the date
     * string from the first BIRT event.  When the final tag is NOTE and the
     * value is a cross-reference, the linked note text is returned.
     *
     * @param string $tag    Colon-delimited tag path (e.g. "BIRT:DATE")
     * @param int    $level  Starting level (0 means auto-detect from record)
     * @param string $gedrec The GEDCOM record to search within
     * @param Tree   $tree   The tree context (used to resolve NOTE cross-references)
     */
    public static function getGedcomValue(string $tag, int $level, string $gedrec, Tree $tree): string
    {
        if ($gedrec === '') {
            return '';
        }
        $tags          = explode(':', $tag);
        $original_level = $level;
        if ($level === 0) {
            $level = 1 + (int) $gedrec[0];
        }

        $subrec = $gedrec;
        $t      = 'XXXX';
        foreach ($tags as $t) {
            $last_subrec = $subrec;
            $subrec      = self::getSubRecord($level, "$level $t", $subrec);
            if (empty($subrec) && $original_level == 0) {
                $level--;
                $subrec = self::getSubRecord($level, "$level $t", $last_subrec);
            }
            if (empty($subrec)) {
                if ($t === 'TITL') {
                    $subrec = self::getSubRecord($level, "$level ABBR", $last_subrec);
                    if (!empty($subrec)) {
                        $t = 'ABBR';
                    }
                }
                if ($subrec === '') {
                    if ($level > 0) {
                        $level--;
                    }
                    $subrec = self::getSubRecord($level, "@ $t", $gedrec);
                    if ($subrec === '') {
                        return '';
                    }
                }
            }
            $level++;
        }
        $level--;
        $ct = preg_match("/$level $t(.*)/", $subrec, $match);
        if ($ct === 0) {
            $ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
        }
        if ($ct === 0) {
            $ct = preg_match("/@ $t (.+)/", $subrec, $match);
        }
        if ($ct > 0) {
            $value = trim($match[1]);
            // Resolve linked NOTE records to their text content
            if ($t === 'NOTE' && preg_match('/^@(.+)@$/', $value, $match)) {
                $note = Registry::noteFactory()->make($match[1], $tree);
                if ($note instanceof Note) {
                    $value = $note->getNote();
                } else {
                    $value = $match[1];
                }
            }
            if ($level !== 0 || $t !== 'NOTE') {
                $value .= self::getCont($level + 1, $subrec);
            }

            // Strip name-delimiting slashes from NAME-type values
            if ($tag === 'NAME' || $tag === '_MARNM' || $tag === '_AKA') {
                return strtr($value, ['/' => '']);
            }

            return $value;
        }

        return '';
    }
}
