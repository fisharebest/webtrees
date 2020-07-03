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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Factories\AbstractGedcomRecordFactory;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;

use function app;
use function assert;
use function date;
use function explode;
use function fwrite;
use function mb_convert_encoding;
use function pathinfo;
use function str_contains;
use function str_starts_with;
use function strpos;
use function strtolower;
use function strtoupper;
use function utf8_decode;

use const PATHINFO_EXTENSION;

/**
 * Export data in GEDCOM format
 */
class GedcomExportService
{
    /**
     * Write GEDCOM data to a stream.
     *
     * @param Tree                    $tree         - Export data from this tree
     * @param resource                $stream       - Write to this stream
     * @param bool                    $sort_by_xref - Write GEDCOM records in XREF order
     * @param string                  $encoding     - Convert from UTF-8 to other encoding
     * @param int                     $access_level - Apply privacy filtering
     * @param string                  $media_path   - Prepend path to media filenames
     * @param Collection<string>|null $records      - Just export these records
     */
    public function export(
        Tree $tree,
        $stream,
        bool $sort_by_xref = false,
        string $encoding = 'UTF-8',
        int $access_level = Auth::PRIV_HIDE,
        string $media_path = '',
        Collection $records = null
    ): void {
        if ($records instanceof Collection) {
            // Export just these records - e.g. from clippings cart.
            $data = [
                new Collection([$this->createHeader($tree, $encoding, false)]),
                $records,
                new Collection(['0 TRLR']),
            ];
        } elseif ($access_level === Auth::PRIV_HIDE) {
            // If we will be applying privacy filters, then we will need the GEDCOM record objects.
            $data = [
                new Collection([$this->createHeader($tree, $encoding, true)]),
                $this->individualQuery($tree, $sort_by_xref)->cursor(),
                $this->familyQuery($tree, $sort_by_xref)->cursor(),
                $this->sourceQuery($tree, $sort_by_xref)->cursor(),
                $this->otherQuery($tree, $sort_by_xref)->cursor(),
                $this->mediaQuery($tree, $sort_by_xref)->cursor(),
                new Collection(['0 TRLR']),
            ];
        } else {
            // Disable the pending changes before creating GEDCOM records.
            $cache = app('cache.array');
            assert($cache instanceof Cache);
            $cache->remember(AbstractGedcomRecordFactory::class . $tree->id(), static function (): Collection {
                return new Collection();
            });

            $data = [
                new Collection([$this->createHeader($tree, $encoding, true)]),
                $this->individualQuery($tree, $sort_by_xref)->get()->map(Factory::individual()->mapper($tree)),
                $this->familyQuery($tree, $sort_by_xref)->get()->map(Factory::family()->mapper($tree)),
                $this->sourceQuery($tree, $sort_by_xref)->get()->map(Factory::source()->mapper($tree)),
                $this->otherQuery($tree, $sort_by_xref)->get()->map(Factory::gedcomRecord()->mapper($tree)),
                $this->mediaQuery($tree, $sort_by_xref)->get()->map(Factory::media()->mapper($tree)),
                new Collection(['0 TRLR']),
            ];
        }

        foreach ($data as $rows) {
            foreach ($rows as $datum) {
                if (is_string($datum)) {
                    $gedcom = $datum;
                } elseif ($datum instanceof GedcomRecord) {
                    $gedcom = $datum->privatizeGedcom($access_level);
                } else {
                    $gedcom =
                        $datum->i_gedcom ??
                        $datum->f_gedcom ??
                        $datum->s_gedcom ??
                        $datum->m_gedcom ??
                        $datum->o_gedcom;
                }

                if ($media_path !== '') {
                    $gedcom = $this->convertMediaPath($gedcom, $media_path);
                }

                $gedcom = $this->wrapLongLines($gedcom, Gedcom::LINE_LENGTH) . Gedcom::EOL;
                $gedcom = $this->convertEncoding($encoding, $gedcom);

                fwrite($stream, $gedcom);
            }
        }
    }

    /**
     * Create a header record for a gedcom file.
     *
     * @param Tree   $tree
     * @param string $encoding
     * @param bool   $include_sub
     *
     * @return string
     */
    public function createHeader(Tree $tree, string $encoding, bool $include_sub): string
    {
        // Force a ".ged" suffix
        $filename = $tree->name();

        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'ged') {
            $filename .= '.ged';
        }

        // Build a new header record
        $gedcom = '0 HEAD';
        $gedcom .= "\n1 SOUR " . Webtrees::NAME;
        $gedcom .= "\n2 NAME " . Webtrees::NAME;
        $gedcom .= "\n2 VERS " . Webtrees::VERSION;
        $gedcom .= "\n1 DEST DISKETTE";
        $gedcom .= "\n1 DATE " . strtoupper(date('d M Y'));
        $gedcom .= "\n2 TIME " . date('H:i:s');
        $gedcom .= "\n1 GEDC\n2 VERS 5.5.1\n2 FORM Lineage-Linked";
        $gedcom .= "\n1 CHAR " . $encoding;
        $gedcom .= "\n1 FILE " . $filename;

        // Preserve some values from the original header
        $header = Factory::header()->make('HEAD', $tree) ?? Factory::header()->new('HEAD', '0 HEAD', null, $tree);

        foreach ($header->facts(['COPR', 'LANG', 'PLAC', 'NOTE']) as $fact) {
            $gedcom .= "\n" . $fact->gedcom();
        }

        if ($include_sub) {
            foreach ($header->facts(['SUBM', 'SUBN']) as $fact) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }

        return $gedcom;
    }

    /**
     * Prepend a media path, such as might have been removed during import.
     *
     * @param string $gedcom
     * @param string $media_path
     *
     * @return string
     */
    private function convertMediaPath(string $gedcom, string $media_path): string
    {
        if (preg_match('/^0 @[^@]+@ OBJE/', $gedcom)) {
            return preg_replace_callback('/\n1 FILE (.+)/', static function (array $match) use ($media_path): string {
                $filename = $match[1];

                // Convert separators to match new path.
                if (str_contains($media_path, '\\')) {
                    $filename = strtr($filename, ['/' => '\\']);
                }

                if (!str_starts_with($filename, $media_path)) {
                    return $media_path . $filename;
                }

                return $filename;
            }, $gedcom);
        }

        return $gedcom;
    }

    /**
     * @param string $encoding
     * @param string $gedcom
     *
     * @return string
     */
    private function convertEncoding(string $encoding, string $gedcom): string
    {
        switch ($encoding) {
            case 'ANSI':
                // Many desktop applications interpret ANSI as ISO-8859-1
                return utf8_decode($gedcom);

            case 'ANSEL':
                // coming soon...?
            case 'ASCII':
                // Might be needed by really old software?
                return mb_convert_encoding($gedcom, 'UTF-8', 'ASCII');

            default:
                return $gedcom;
        }
    }

    /**
     * Wrap long lines using concatenation records.
     *
     * @param string $gedcom
     * @param int    $max_line_length
     *
     * @return string
     */
    public function wrapLongLines(string $gedcom, int $max_line_length): string
    {
        $lines = [];

        foreach (explode("\n", $gedcom) as $line) {
            // Split long lines
            // The total length of a GEDCOM line, including level number, cross-reference number,
            // tag, value, delimiters, and terminator, must not exceed 255 (wide) characters.
            if (mb_strlen($line) > $max_line_length) {
                [$level, $tag] = explode(' ', $line, 3);
                if ($tag !== 'CONT') {
                    $level++;
                }
                do {
                    // Split after $pos chars
                    $pos = $max_line_length;
                    // Split on a non-space (standard gedcom behavior)
                    while (mb_substr($line, $pos - 1, 1) === ' ') {
                        --$pos;
                    }
                    if ($pos === strpos($line, ' ', 3)) {
                        // No non-spaces in the data! Can’t split it :-(
                        break;
                    }
                    $lines[] = mb_substr($line, 0, $pos);
                    $line    = $level . ' CONC ' . mb_substr($line, $pos);
                } while (mb_strlen($line) > $max_line_length);
            }
            $lines[] = $line;
        }

        return implode(Gedcom::EOL, $lines);
    }

    /**
     * @param Tree $tree
     * @param bool $sort_by_xref
     *
     * @return Builder
     */
    private function familyQuery(Tree $tree, bool $sort_by_xref): Builder
    {
        $query = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->select(['f_gedcom', 'f_id']);


        if ($sort_by_xref) {
            $query
                ->orderBy(new Expression('LENGTH(f_id)'))
                ->orderBy('f_id');
        }

        return $query;
    }

    /**
     * @param Tree $tree
     * @param bool $sort_by_xref
     *
     * @return Builder
     */
    private function individualQuery(Tree $tree, bool $sort_by_xref): Builder
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->select(['i_gedcom', 'i_id']);

        if ($sort_by_xref) {
            $query
                ->orderBy(new Expression('LENGTH(i_id)'))
                ->orderBy('i_id');
        }

        return $query;
    }

    /**
     * @param Tree $tree
     * @param bool $sort_by_xref
     *
     * @return Builder
     */
    private function sourceQuery(Tree $tree, bool $sort_by_xref): Builder
    {
        $query = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->select(['s_gedcom', 's_id']);

        if ($sort_by_xref) {
            $query
                ->orderBy(new Expression('LENGTH(s_id)'))
                ->orderBy('s_id');
        }

        return $query;
    }

    /**
     * @param Tree $tree
     * @param bool $sort_by_xref
     *
     * @return Builder
     */
    private function mediaQuery(Tree $tree, bool $sort_by_xref): Builder
    {
        $query = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->select(['m_gedcom', 'm_id']);

        if ($sort_by_xref) {
            $query
                ->orderBy(new Expression('LENGTH(m_id)'))
                ->orderBy('m_id');
        }

        return $query;
    }

    /**
     * @param Tree $tree
     * @param bool $sort_by_xref
     *
     * @return Builder
     */
    private function otherQuery(Tree $tree, bool $sort_by_xref): Builder
    {
        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', [Header::RECORD_TYPE, 'TRLR'])
            ->select(['o_gedcom', 'o_id']);

        if ($sort_by_xref) {
            $query
                ->orderBy('o_type')
                ->orderBy(new Expression('LENGTH(o_id)'))
                ->orderBy('o_id');
        }

        return $query;
    }
}
