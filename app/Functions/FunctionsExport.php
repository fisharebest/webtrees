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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;

/**
 * Class FunctionsExport - common functions
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
                    // Split on a non-space (standard gedcom behaviour)
                    while (mb_substr($line, $pos - 1, 1) === ' ') {
                        --$pos;
                    }
                    if ($pos === strpos($line, ' ', 3)) {
                        // No non-spaces in the data! Can’t split it :-(
                        break;
                    }
                    $newrec .= mb_substr($line, 0, $pos) . Gedcom::EOL;
                    $line = $level . ' CONC ' . mb_substr($line, $pos);
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
        // Default values for a new header
        $HEAD = '0 HEAD';
        $SOUR = "\n1 SOUR " . Webtrees::NAME . "\n2 NAME " . Webtrees::NAME . "\n2 VERS " . Webtrees::VERSION;
        $DEST = "\n1 DEST DISKETTE";
        $DATE = "\n1 DATE " . strtoupper(date('d M Y')) . "\n2 TIME " . date('H:i:s');
        $GEDC = "\n1 GEDC\n2 VERS 5.5.1\n2 FORM Lineage-Linked";
        $CHAR = "\n1 CHAR " . $char;
        $FILE = "\n1 FILE " . $tree->name();
        $LANG = '';
        $COPR = '';
        $SUBN = '';
        $SUBM = "\n1 SUBM @SUBM@\n0 @SUBM@ SUBM\n1 NAME " . Auth::user()->getUserName(); // The SUBM record is mandatory

        // Preserve some values from the original header
        $record = GedcomRecord::getInstance('HEAD', $tree);
        $fact   = $record->getFirstFact('LANG');
        if ($fact instanceof Fact) {
            $LANG = $fact->value();
        }
        $fact = $record->getFirstFact('SUBN');
        if ($fact instanceof Fact) {
            $SUBN = $fact->value();
        }
        $fact = $record->getFirstFact('COPR');
        if ($fact instanceof Fact) {
            $COPR = $fact->value();
        }
        // Link to actual SUBM/SUBN records, if they exist
        $subn =
            Database::prepare("SELECT o_id FROM `##other` WHERE o_type=? AND o_file=?")
                ->execute([
                    'SUBN',
                    $tree->id(),
                ])
                ->fetchOne();
        if ($subn) {
            $SUBN = "\n1 SUBN @{$subn}@";
        }
        $subm =
            Database::prepare("SELECT o_id FROM `##other` WHERE o_type=? AND o_file=?")
                ->execute([
                    'SUBM',
                    $tree->id(),
                ])
                ->fetchOne();
        if ($subm) {
            $SUBM = "\n1 SUBM @{$subm}@";
        }

        return $HEAD . $SOUR . $DEST . $DATE . $GEDC . $CHAR . $FILE . $COPR . $LANG . $SUBN . $SUBM . "\n";
    }

    /**
     * Prepend the GEDCOM_MEDIA_PATH to media filenames.
     *
     * @param string $rec
     * @param string $path
     *
     * @return string
     */
    public static function convertMediaPath($rec, $path): string
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
     * @param resource $gedout       Handle to a writable stream
     * @param int      $access_level Apply privacy filters
     * @param string   $media_path   Add this prefix to media file names
     * @param string   $encoding     UTF-8 or ANSI
     *
     * @return void
     */
    public static function exportGedcom(Tree $tree, $gedout, int $access_level, string $media_path, string $encoding)
    {
        $head = self::gedcomHeader($tree, $encoding);

        if ($encoding === 'ANSI') {
            $head = utf8_decode($head);
        }

        $head = self::reformatRecord($head);
        fwrite($gedout, $head);

        // Buffer the output. Lots of small fwrite() calls can be very slow when writing large gedcoms.
        $buffer = '';

        // Generate the OBJE/SOUR/REPO/NOTE records first, as their privacy calcualations involve
        // database queries, and we wish to avoid large gaps between queries due to MySQL connection timeouts.
        $tmp_gedcom = '';
        $rows       = Database::prepare(
            "SELECT m_id AS xref, m_gedcom AS gedcom" .
            " FROM `##media` WHERE m_file = :tree_id ORDER BY m_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        foreach ($rows as $row) {
            $rec = Media::getInstance($row->xref, $tree, $row->gedcom)->privatizeGedcom($access_level);
            $rec = self::convertMediaPath($rec, $media_path);
            if ($encoding === 'ANSI') {
                $rec = utf8_decode($rec);
            }
            $tmp_gedcom .= self::reformatRecord($rec);
        }

        $rows = Database::prepare(
            "SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom" .
            " FROM `##sources` WHERE s_file = :tree_id ORDER BY s_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        foreach ($rows as $row) {
            $rec = Source::getInstance($row->xref, $tree, $row->gedcom)->privatizeGedcom($access_level);
            if ($encoding === 'ANSI') {
                $rec = utf8_decode($rec);
            }
            $tmp_gedcom .= self::reformatRecord($rec);
        }

        $rows = Database::prepare(
            "SELECT o_type AS type, o_id AS xref, o_gedcom AS gedcom" .
            " FROM `##other` WHERE o_file = :tree_id AND o_type NOT IN ('HEAD', 'TRLR') ORDER BY o_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        foreach ($rows as $row) {
            switch ($row->type) {
                case 'NOTE':
                    $record = Note::getInstance($row->xref, $tree, $row->gedcom);
                    break;
                case 'REPO':
                    $record = Repository::getInstance($row->xref, $tree, $row->gedcom);
                    break;
                default:
                    $record = GedcomRecord::getInstance($row->xref, $tree, $row->gedcom);
                    break;
            }

            $rec = $record->privatizeGedcom($access_level);
            if ($encoding === 'ANSI') {
                $rec = utf8_decode($rec);
            }
            $tmp_gedcom .= self::reformatRecord($rec);
        }

        $rows = Database::prepare(
            "SELECT i_id AS xref, i_gedcom AS gedcom" .
            " FROM `##individuals` WHERE i_file = :tree_id ORDER BY i_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        foreach ($rows as $row) {
            $rec = Individual::getInstance($row->xref, $tree, $row->gedcom)->privatizeGedcom($access_level);
            if ($encoding === 'ANSI') {
                $rec = utf8_decode($rec);
            }
            $buffer .= self::reformatRecord($rec);
            if (strlen($buffer) > 65536) {
                fwrite($gedout, $buffer);
                $buffer = '';
            }
        }

        $rows = Database::prepare(
            "SELECT f_id AS xref, f_gedcom AS gedcom" .
            " FROM `##families` WHERE f_file = :tree_id ORDER BY f_id"
        )->execute([
            'tree_id' => $tree->id(),
        ])->fetchAll();

        foreach ($rows as $row) {
            $rec = Family::getInstance($row->xref, $tree, $row->gedcom)->privatizeGedcom($access_level);
            if ($encoding === 'ANSI') {
                $rec = utf8_decode($rec);
            }
            $buffer .= self::reformatRecord($rec);
            if (strlen($buffer) > 65536) {
                fwrite($gedout, $buffer);
                $buffer = '';
            }
        }

        fwrite($gedout, $buffer);
        fwrite($gedout, $tmp_gedcom);
        fwrite($gedout, '0 TRLR' . Gedcom::EOL);
    }
}
