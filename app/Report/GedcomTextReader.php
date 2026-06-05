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
        $gedrec            = "\n" . $gedrec . "\n";
        $tag               = trim($tag);
        $searchTarget      = "~[\n]" . $tag . "[\s]~";
        $match_count       = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        if ($match_count < $num) {
            return '';
        }
        $start_position = (int) $match[$num - 1][0][1];
        $end_position   = strpos($gedrec, "\n$level", $start_position + 1);
        if (!$end_position) {
            $end_position = strpos($gedrec, "\n1", $start_position + 1);
        }
        if (!$end_position) {
            return ltrim(substr($gedrec, $start_position));
        }
        $subrecord = substr($gedrec, $start_position, $end_position - $start_position);

        return ltrim($subrecord);
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
        $tags           = explode(':', $tag);
        $original_level = $level;
        if ($level === 0) {
            $level = 1 + (int) $gedrec[0];
        }

        $subrecord = $gedrec;
        $final_tag = 'XXXX';
        foreach ($tags as $final_tag) {
            $previous_subrecord = $subrecord;
            $subrecord          = self::getSubRecord($level, "$level $final_tag", $subrecord);
            if ($subrecord === '' && $original_level === 0) {
                $level--;
                $subrecord = self::getSubRecord($level, "$level $final_tag", $previous_subrecord);
            }
            if ($subrecord === '') {
                if ($final_tag === 'TITL') {
                    $subrecord = self::getSubRecord($level, "$level ABBR", $previous_subrecord);
                    if ($subrecord !== '') {
                        $final_tag = 'ABBR';
                    }
                }
                if ($subrecord === '') {
                    if ($level > 0) {
                        $level--;
                    }
                    $subrecord = self::getSubRecord($level, "@ $final_tag", $gedrec);
                    if ($subrecord === '') {
                        return '';
                    }
                }
            }
            $level++;
        }
        $level--;
        $match_count = preg_match("/$level $final_tag(.*)/", $subrecord, $match);
        if ($match_count === 0) {
            $match_count = preg_match("/$level @.+@ (.+)/", $subrecord, $match);
        }
        if ($match_count === 0) {
            $match_count = preg_match("/@ $final_tag (.+)/", $subrecord, $match);
        }
        if ($match_count > 0) {
            $value = trim($match[1]);
            // Resolve linked NOTE records to their text content
            if ($final_tag === 'NOTE' && preg_match('/^@(.+)@$/', $value, $match)) {
                $note = Registry::noteFactory()->make($match[1], $tree);
                if ($note instanceof Note) {
                    $value = $note->getNote();
                } else {
                    $value = $match[1];
                }
            }
            if ($level !== 0 || $final_tag !== 'NOTE') {
                $value .= self::getCont($level + 1, $subrecord);
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
