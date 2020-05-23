<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function date;
use function explode;
use function fwrite;
use function pathinfo;
use function preg_match;
use function preg_replace;
use function preg_split;
use function str_replace;
use function strpos;
use function strtolower;
use function strtoupper;

use const PATHINFO_EXTENSION;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Class FunctionsExport - common functions
 *
 * @deprecated since 2.0.5.  Will be removed in 2.1.0
 */
class FunctionsExport
{
    /**
     * Tidy up a gedcom record on export, for compatibility/portability.
     *
     * @param string $rec
     *
     * @return string
     */
    public static function reformatRecord($rec): string
    {
        $newrec = '';
        foreach (preg_split('/[\r\n]+/', $rec, -1, PREG_SPLIT_NO_EMPTY) as $line) {
            // Split long lines
            // The total length of a GEDCOM line, including level number, cross-reference number,
            // tag, value, delimiters, and terminator, must not exceed 255 (wide) characters.
            if (mb_strlen($line) > Gedcom::LINE_LENGTH) {
                [$level, $tag] = explode(' ', $line, 3);
                if ($tag !== 'CONT' && $tag !== 'CONC') {
                    $level++;
                }
                do {
                    // Split after $pos chars
                    $pos = Gedcom::LINE_LENGTH;
                    // Split on a non-space (standard gedcom behavior)
                    while (mb_substr($line, $pos - 1, 1) === ' ') {
                        --$pos;
                    }
                    if ($pos === strpos($line, ' ', 3)) {
                        // No non-spaces in the data! Can’t split it :-(
                        break;
                    }
                    $newrec .= mb_substr($line, 0, $pos) . Gedcom::EOL;
                    $line   = $level . ' CONC ' . mb_substr($line, $pos);
                } while (mb_strlen($line) > Gedcom::LINE_LENGTH);
            }
            $newrec .= $line . Gedcom::EOL;
        }

        return $newrec;
    }

    /**
     * Create a header for a (newly-created or already-imported) gedcom file.
     *
     * @param Tree   $tree
     * @param string $char "UTF-8" or "ANSI"
     *
     * @return string
     */
    public static function gedcomHeader(Tree $tree, string $char): string
    {
        // Force a ".ged" suffix
        $filename = $tree->name();

        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'ged') {
            $filename .= '.ged';
        }

        $today = strtoupper(date('d M Y'));
        $now   = date('H:i:s');

        // Default values for a new header
        $HEAD = '0 HEAD';
        $SOUR = "\n1 SOUR " . Webtrees::NAME . "\n2 NAME " . Webtrees::NAME . "\n2 VERS " . Webtrees::VERSION;
        $DEST = "\n1 DEST DISKETTE";
        $DATE = "\n1 DATE " . $today . "\n2 TIME " . $now;
        $GEDC = "\n1 GEDC\n2 VERS 5.5.1\n2 FORM Lineage-Linked";
        $CHAR = "\n1 CHAR " . $char;
        $FILE = "\n1 FILE " . $filename;
        $COPR = '';
        $LANG = '';

        // Preserve some values from the original header
        $header = Factory::header()->make('HEAD', $tree) ?? Factory::header()->new('HEAD', '0 HEAD', null, $tree);

        $fact   = $header->facts(['COPR'])->first();

        if ($fact instanceof Fact) {
            $COPR = "\n1 COPR " . $fact->value();
        }

        $fact = $header->facts(['LANG'])->first();

        if ($fact instanceof Fact) {
            $LANG = "\n1 LANG " . $fact->value();
        }

        // Link to actual SUBM/SUBN records, if they exist
        $subn = DB::table('other')
            ->where('o_type', '=', 'SUBN')
            ->where('o_file', '=', $tree->id())
            ->value('o_id');
        if ($subn !== null) {
            $SUBN = "\n1 SUBN @{$subn}@";
        } else {
            $SUBN = '';
        }

        $subm = DB::table('other')
            ->where('o_type', '=', 'SUBM')
            ->where('o_file', '=', $tree->id())
            ->value('o_id');
        if ($subm !== null) {
            $SUBM          = "\n1 SUBM @{$subm}@";
            $new_submitter = '';
        } else {
            // The SUBM record is mandatory
            $SUBM          = "\n1 SUBM @SUBM@";
            $new_submitter = "\n0 @SUBM@ SUBM\n1 NAME " . Auth::user()->userName(); // The SUBM record is mandatory
        }

        return $HEAD . $SOUR . $DEST . $DATE . $SUBM . $SUBN . $FILE . $COPR . $GEDC . $CHAR . $LANG . $new_submitter . "\n";
    }

    /**
     * Prepend the GEDCOM_MEDIA_PATH to media filenames.
     *
     * @param string $rec
     * @param string $path
     *
     * @return string
     */
    private static function convertMediaPath($rec, $path): string
    {
        if ($path && preg_match('/\n1 FILE (.+)/', $rec, $match)) {
            $old_file_name = $match[1];
            // Don’t modify external links
            if (strpos($old_file_name, '://') === false) {
                // Adding a windows path? Convert the slashes.
                if (strpos($path, '\\') !== false) {
                    $new_file_name = preg_replace('~/+~', '\\', $old_file_name);
                } else {
                    $new_file_name = $old_file_name;
                }
                // Path not present - add it.
                if (strpos($new_file_name, $path) === false) {
                    $new_file_name = $path . $new_file_name;
                }
                $rec = str_replace("\n1 FILE " . $old_file_name, "\n1 FILE " . $new_file_name, $rec);
            }
        }

        return $rec;
    }

    /**
     * Export the database in GEDCOM format
     *
     * @param Tree     $tree         Which tree to export
     * @param resource $stream       Handle to a writable stream
     * @param int      $access_level Apply privacy filters
     * @param string   $media_path   Add this prefix to media file names
     * @param string   $encoding     UTF-8 or ANSI
     *
     * @return void
     */
    public static function exportGedcom(Tree $tree, $stream, int $access_level, string $media_path, string $encoding): void
    {
        $header = new Collection([self::gedcomHeader($tree, $encoding)]);

        // Generate the OBJE/SOUR/REPO/NOTE records first, as their privacy calcualations involve
        // database queries, and we wish to avoid large gaps between queries due to MySQL connection timeouts.
        $media = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->orderBy('m_id')
            ->get()
            ->map(Factory::media()->mapper($tree))
            ->map(static function (Media $record) use ($access_level): string {
                return $record->privatizeGedcom($access_level);
            })
            ->map(static function (string $gedcom) use ($media_path): string {
                return self::convertMediaPath($gedcom, $media_path);
            });

        $sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->orderBy('s_id')
            ->get()
            ->map(Factory::source()->mapper($tree))
            ->map(static function (Source $record) use ($access_level): string {
                return $record->privatizeGedcom($access_level);
            });

        $other = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
            ->orderBy('o_id')
            ->get()
            ->map(Factory::gedcomRecord()->mapper($tree))
            ->map(static function (GedcomRecord $record) use ($access_level): string {
                return $record->privatizeGedcom($access_level);
            });

        $individuals = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->orderBy('i_id')
            ->get()
            ->map(Factory::individual()->mapper($tree))
            ->map(static function (Individual $record) use ($access_level): string {
                return $record->privatizeGedcom($access_level);
            });

        $families = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->orderBy('f_id')
            ->get()
            ->map(Factory::family()->mapper($tree))
            ->map(static function (Family $record) use ($access_level): string {
                return $record->privatizeGedcom($access_level);
            });

        $trailer = new Collection(['0 TRLR' . Gedcom::EOL]);

        $records = $header
            ->merge($media)
            ->merge($sources)
            ->merge($other)
            ->merge($individuals)
            ->merge($families)
            ->merge($trailer)
            ->map(static function (string $gedcom) use ($encoding): string {
                return $encoding === 'ANSI' ? utf8_decode($gedcom) : $gedcom;
            })
            ->map(static function (string $gedcom): string {
                return self::reformatRecord($gedcom);
            });

        fwrite($stream, $records->implode(''));
    }
}
