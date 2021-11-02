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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Exceptions\GedcomErrorException;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

use function app;
use function array_chunk;
use function array_intersect_key;
use function array_map;
use function array_unique;
use function assert;
use function date;
use function explode;
use function max;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function round;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;

use const PREG_SET_ORDER;

/**
 * Class FunctionsImport - common functions
 */
class FunctionsImport
{
    /**
     * Tidy up a gedcom record on import, so that we can access it consistently/efficiently.
     *
     * @param string $rec
     * @param Tree   $tree
     *
     * @return string
     */
    public static function reformatRecord(string $rec, Tree $tree): string
    {
        $gedcom_service = app(GedcomService::class);
        assert($gedcom_service instanceof GedcomService);

        // Strip out mac/msdos line endings
        $rec = preg_replace("/[\r\n]+/", "\n", $rec);

        // Extract lines from the record; lines consist of: level + optional xref + tag + optional data
        $num_matches = preg_match_all('/^[ \t]*(\d+)[ \t]*(@[^@]*@)?[ \t]*(\w+)[ \t]?(.*)$/m', $rec, $matches, PREG_SET_ORDER);

        // Process the record line-by-line
        $newrec = '';
        foreach ($matches as $n => $match) {
            [, $level, $xref, $tag, $data] = $match;

            $tag = $gedcom_service->canonicalTag($tag);

            switch ($tag) {
                case 'AFN':
                    // AFN values are upper case
                    $data = strtoupper($data);
                    break;
                case 'DATE':
                    // Preserve text from INT dates
                    if (str_contains($data, '(')) {
                        [$date, $text] = explode('(', $data, 2);
                        $text = ' (' . $text;
                    } else {
                        $date = $data;
                        $text = '';
                    }
                    // Capitals
                    $date = strtoupper($date);
                    // Temporarily add leading/trailing spaces, to allow efficient matching below
                    $date = " {$date} ";
                    // Ensure space digits and letters
                    $date = preg_replace('/([A-Z])(\d)/', '$1 $2', $date);
                    $date = preg_replace('/(\d)([A-Z])/', '$1 $2', $date);
                    // Ensure space before/after calendar escapes
                    $date = preg_replace('/@#[^@]+@/', ' $0 ', $date);
                    // "BET." => "BET"
                    $date = preg_replace('/(\w\w)\./', '$1', $date);
                    // "CIR" => "ABT"
                    $date = str_replace(' CIR ', ' ABT ', $date);
                    $date = str_replace(' APX ', ' ABT ', $date);
                    // B.C. => BC (temporarily, to allow easier handling of ".")
                    $date = str_replace(' B.C. ', ' BC ', $date);
                    // TMG uses "EITHER X OR Y"
                    $date = preg_replace('/^ EITHER (.+) OR (.+)/', ' BET $1 AND $2', $date);
                    // "BET X - Y " => "BET X AND Y"
                    $date = preg_replace('/^(.* BET .+) - (.+)/', '$1 AND $2', $date);
                    $date = preg_replace('/^(.* FROM .+) - (.+)/', '$1 TO $2', $date);
                    // "@#ESC@ FROM X TO Y" => "FROM @#ESC@ X TO @#ESC@ Y"
                    $date = preg_replace('/^ +(@#[^@]+@) +FROM +(.+) +TO +(.+)/', ' FROM $1 $2 TO $1 $3', $date);
                    $date = preg_replace('/^ +(@#[^@]+@) +BET +(.+) +AND +(.+)/', ' BET $1 $2 AND $1 $3', $date);
                    // "@#ESC@ AFT X" => "AFT @#ESC@ X"
                    $date = preg_replace('/^ +(@#[^@]+@) +(FROM|BET|TO|AND|BEF|AFT|CAL|EST|INT|ABT) +(.+)/', ' $2 $1 $3', $date);
                    // Ignore any remaining punctuation, e.g. "14-MAY, 1900" => "14 MAY 1900"
                    // (don't change "/" - it is used in NS/OS dates)
                    $date = preg_replace('/[.,:;-]/', ' ', $date);
                    // BC => B.C.
                    $date = str_replace(' BC ', ' B.C. ', $date);
                    // Append the "INT" text
                    $data = $date . $text;
                    break;
                case '_FILE':
                    $tag = 'FILE';
                    break;
                case 'FORM':
                    // Consistent commas
                    $data = preg_replace('/ *, */', ', ', $data);
                    break;
                case 'HEAD':
                    // HEAD records don't have an XREF or DATA
                    if ($level === '0') {
                        $xref = '';
                        $data = '';
                    }
                    break;
                case 'NAME':
                    // Tidy up non-printing characters
                    $data = preg_replace('/  +/', ' ', trim($data));
                    break;
                case 'PEDI':
                    // PEDI values are lower case
                    $data = strtolower($data);
                    break;
                case 'PLAC':
                    // Consistent commas
                    $data = preg_replace('/ *[,，،] */u', ', ', $data);
                    // The Master Genealogist stores LAT/LONG data in the PLAC field, e.g. Pennsylvania, USA, 395945N0751013W
                    if (preg_match('/(.*), (\d\d)(\d\d)(\d\d)([NS])(\d\d\d)(\d\d)(\d\d)([EW])$/', $data, $match)) {
                        $data =
                            $match[1] . "\n" .
                            ($level + 1) . " MAP\n" .
                            ($level + 2) . ' LATI ' . ($match[5] . round($match[2] + ($match[3] / 60) + ($match[4] / 3600), 4)) . "\n" .
                            ($level + 2) . ' LONG ' . ($match[9] . round($match[6] + ($match[7] / 60) + ($match[8] / 3600), 4));
                    }
                    break;
                case 'RESN':
                    // RESN values are lower case (confidential, privacy, locked, none)
                    $data = strtolower($data);
                    if ($data === 'invisible') {
                        $data = 'confidential'; // From old versions of Legacy.
                    }
                    break;
                case 'SEX':
                    $data = strtoupper($data);
                    break;
                case 'STAT':
                    if ($data === 'CANCELLED') {
                        // PhpGedView mis-spells this tag - correct it.
                        $data = 'CANCELED';
                    }
                    break;
                case 'TEMP':
                    // Temple codes are upper case
                    $data = strtoupper($data);
                    break;
                case 'TRLR':
                    // TRLR records don't have an XREF or DATA
                    if ($level === '0') {
                        $xref = '';
                        $data = '';
                    }
                    break;
            }
            // Suppress "Y", for facts/events with a DATE or PLAC
            if ($data === 'y') {
                $data = 'Y';
            }
            if ($level === '1' && $data === 'Y') {
                for ($i = $n + 1; $i < $num_matches - 1 && $matches[$i][1] !== '1'; ++$i) {
                    if ($matches[$i][3] === 'DATE' || $matches[$i][3] === 'PLAC') {
                        $data = '';
                        break;
                    }
                }
            }
            // Reassemble components back into a single line
            switch ($tag) {
                default:
                    // Remove tabs and multiple/leading/trailing spaces
                    $data = strtr($data, ["\t" => ' ']);
                    $data = trim($data, ' ');
                    while (str_contains($data, '  ')) {
                        $data = strtr($data, ['  ' => ' ']);
                    }
                    $newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level === '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag !== 'NOTE' ? '' : ' ' . $data);
                    break;
                case 'NOTE':
                case 'TEXT':
                case 'DATA':
                case 'CONT':
                    $newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level === '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag !== 'NOTE' ? '' : ' ' . $data);
                    break;
                case 'FILE':
                    // Strip off the user-defined path prefix
                    $GEDCOM_MEDIA_PATH = $tree->getPreference('GEDCOM_MEDIA_PATH');
                    if ($GEDCOM_MEDIA_PATH !== '' && str_starts_with($data, $GEDCOM_MEDIA_PATH)) {
                        $data = substr($data, strlen($GEDCOM_MEDIA_PATH));
                    }
                    // convert backslashes in filenames to forward slashes
                    $data = preg_replace("/\\\\/", '/', $data);

                    $newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level === '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag !== 'NOTE' ? '' : ' ' . $data);
                    break;
                case 'CONC':
                    // Merge CONC lines, to simplify access later on.
                    $newrec .= ($tree->getPreference('WORD_WRAPPED_NOTES') ? ' ' : '') . $data;
                    break;
            }
        }

        return $newrec;
    }

    /**
     * import record into database
     * this function will parse the given gedcom record and add it to the database
     *
     * @param string $gedrec the raw gedcom record to parse
     * @param Tree   $tree   import the record into this tree
     * @param bool   $update whether or not this is an updated record that has been accepted
     *
     * @return void
     * @throws GedcomErrorException
     */
    public static function importRecord(string $gedrec, Tree $tree, bool $update): void
    {
        $tree_id = $tree->id();

        // Escaped @ signs (only if importing from file)
        if (!$update) {
            $gedrec = str_replace('@@', '@', $gedrec);
        }

        // Standardise gedcom format
        $gedrec = self::reformatRecord($gedrec, $tree);

        // import different types of records
        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@ (' . Gedcom::REGEX_TAG . ')/', $gedrec, $match)) {
            [, $xref, $type] = $match;
        } elseif (preg_match('/0 (HEAD|TRLR|_PLAC |_PLAC_DEFN)/', $gedrec, $match)) {
            $type = $match[1];
            $xref = $type; // For records without an XREF, use the type as a pseudo XREF.
        } else {
            throw new GedcomErrorException($gedrec);
        }

        // Add a _UID
        if ($tree->getPreference('GENERATE_UIDS') === '1' && !str_contains($gedrec, "\n1 _UID ")) {
            $element = Registry::elementFactory()->make($type . ':_UID');
            if (!$element instanceof UnknownElement) {
                $gedrec .= "\n1 _UID " . $element->default($tree);
            }
        }

        // If the user has downloaded their GEDCOM data (containing media objects) and edited it
        // using an application which does not support (and deletes) media objects, then add them
        // back in.
        if ($tree->getPreference('keep_media')) {
            $old_linked_media = DB::table('link')
                ->where('l_from', '=', $xref)
                ->where('l_file', '=', $tree_id)
                ->where('l_type', '=', 'OBJE')
                ->pluck('l_to');

            // Delete these links - so that we do not insert them again in updateLinks()
            DB::table('link')
                ->where('l_from', '=', $xref)
                ->where('l_file', '=', $tree_id)
                ->where('l_type', '=', 'OBJE')
                ->delete();

            foreach ($old_linked_media as $media_id) {
                $gedrec .= "\n1 OBJE @" . $media_id . '@';
            }
        }

        // Convert inline media into media objects
        $gedrec = self::convertInlineMedia($tree, $gedrec);

        switch ($type) {
            case Individual::RECORD_TYPE:
                $record = Registry::individualFactory()->new($xref, $gedrec, null, $tree);

                if (preg_match('/\n1 RIN (.+)/', $gedrec, $match)) {
                    $rin = $match[1];
                } else {
                    $rin = $xref;
                }

                DB::table('individuals')->insert([
                    'i_id'     => $xref,
                    'i_file'   => $tree_id,
                    'i_rin'    => $rin,
                    'i_sex'    => $record->sex(),
                    'i_gedcom' => $gedrec,
                ]);

                // Update the cross-reference/index tables.
                self::updatePlaces($xref, $tree, $gedrec);
                self::updateDates($xref, $tree_id, $gedrec);
                self::updateNames($xref, $tree_id, $record);
                break;

            case Family::RECORD_TYPE:
                if (preg_match('/\n1 HUSB @(' . Gedcom::REGEX_XREF . ')@/', $gedrec, $match)) {
                    $husb = $match[1];
                } else {
                    $husb = '';
                }
                if (preg_match('/\n1 WIFE @(' . Gedcom::REGEX_XREF . ')@/', $gedrec, $match)) {
                    $wife = $match[1];
                } else {
                    $wife = '';
                }
                $nchi = preg_match_all('/\n1 CHIL @(' . Gedcom::REGEX_XREF . ')@/', $gedrec, $match);
                if (preg_match('/\n1 NCHI (\d+)/', $gedrec, $match)) {
                    $nchi = max($nchi, $match[1]);
                }

                DB::table('families')->insert([
                    'f_id'      => $xref,
                    'f_file'    => $tree_id,
                    'f_husb'    => $husb,
                    'f_wife'    => $wife,
                    'f_gedcom'  => $gedrec,
                    'f_numchil' => $nchi,
                ]);

                // Update the cross-reference/index tables.
                self::updatePlaces($xref, $tree, $gedrec);
                self::updateDates($xref, $tree_id, $gedrec);
                break;

            case Source::RECORD_TYPE:
                if (preg_match('/\n1 TITL (.+)/', $gedrec, $match)) {
                    $name = $match[1];
                } elseif (preg_match('/\n1 ABBR (.+)/', $gedrec, $match)) {
                    $name = $match[1];
                } else {
                    $name = $xref;
                }

                DB::table('sources')->insert([
                    's_id'     => $xref,
                    's_file'   => $tree_id,
                    's_name'   => mb_substr($name, 0, 255),
                    's_gedcom' => $gedrec,
                ]);
                break;

            case Repository::RECORD_TYPE:
            case Note::RECORD_TYPE:
            case Submission::RECORD_TYPE:
            case Submitter::RECORD_TYPE:
            case Location::RECORD_TYPE:
                DB::table('other')->insert([
                    'o_id'     => $xref,
                    'o_file'   => $tree_id,
                    'o_type'   => $type,
                    'o_gedcom' => $gedrec,
                ]);
                break;

            case Header::RECORD_TYPE:
                // Force HEAD records to have a creation date.
                if (!str_contains($gedrec, "\n1 DATE ")) {
                    $today = strtoupper(date('d M Y'));
                    $gedrec .= "\n1 DATE " . $today;
                }

                DB::table('other')->insert([
                    'o_id'     => $xref,
                    'o_file'   => $tree_id,
                    'o_type'   => Header::RECORD_TYPE,
                    'o_gedcom' => $gedrec,
                ]);
                break;


            case Media::RECORD_TYPE:
                $record = Registry::mediaFactory()->new($xref, $gedrec, null, $tree);

                DB::table('media')->insert([
                    'm_id'     => $xref,
                    'm_file'   => $tree_id,
                    'm_gedcom' => $gedrec,
                ]);

                foreach ($record->mediaFiles() as $media_file) {
                    DB::table('media_file')->insert([
                        'm_id'                 => $xref,
                        'm_file'               => $tree_id,
                        'multimedia_file_refn' => mb_substr($media_file->filename(), 0, 248),
                        'multimedia_format'    => mb_substr($media_file->format(), 0, 4),
                        'source_media_type'    => mb_substr($media_file->type(), 0, 15),
                        'descriptive_title'    => mb_substr($media_file->title(), 0, 248),
                    ]);
                }
                break;

            case '_PLAC ':
                self::importTNGPlac($gedrec);
                return;

            case '_PLAC_DEFN':
                self::importLegacyPlacDefn($gedrec);
                return;

            default: // Custom record types.
                DB::table('other')->insert([
                    'o_id'     => $xref,
                    'o_file'   => $tree_id,
                    'o_type'   => mb_substr($type, 0, 15),
                    'o_gedcom' => $gedrec,
                ]);
                break;
        }

        // Update the cross-reference/index tables.
        self::updateLinks($xref, $tree_id, $gedrec);
    }

    /**
     * Legacy Family Tree software generates _PLAC_DEFN records containing LAT/LONG values
     *
     * @param string $gedcom
     */
    private static function importLegacyPlacDefn(string $gedcom): void
    {
        $gedcom_service = new GedcomService();

        if (preg_match('/\n1 PLAC (.+)/', $gedcom, $match)) {
            $place_name = $match[1];
        } else {
            return;
        }

        if (preg_match('/\n3 LATI ([NS].+)/', $gedcom, $match)) {
            $latitude = $gedcom_service->readLatitude($match[1]);
        } else {
            return;
        }

        if (preg_match('/\n3 LONG ([EW].+)/', $gedcom, $match)) {
            $longitude = $gedcom_service->readLongitude($match[1]);
        } else {
            return;
        }

        $location = new PlaceLocation($place_name);

        if ($location->latitude() === null && $location->longitude() === null) {
            DB::table('place_location')
                ->where('id', '=', $location->id())
                ->update([
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);
        }
    }

    /**
     * Legacy Family Tree software generates _PLAC records containing LAT/LONG values
     *
     * @param string $gedcom
     */
    private static function importTNGPlac(string $gedcom): void
    {
        if (preg_match('/^0 _PLAC (.+)/', $gedcom, $match)) {
            $place_name = $match[1];
        } else {
            return;
        }

        if (preg_match('/\n2 LATI (.+)/', $gedcom, $match)) {
            $latitude = (float) $match[1];
        } else {
            return;
        }

        if (preg_match('/\n2 LONG (.+)/', $gedcom, $match)) {
            $longitude = (float) $match[1];
        } else {
            return;
        }

        $location = new PlaceLocation($place_name);

        if ($location->latitude() === null && $location->longitude() === null) {
            DB::table('place_location')
                ->where('id', '=', $location->id())
                ->update([
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                ]);
        }
    }

    /**
     * Extract all level 2 places from the given record and insert them into the places table
     *
     * @param string $xref
     * @param Tree   $tree
     * @param string $gedrec
     *
     * @return void
     */
    public static function updatePlaces(string $xref, Tree $tree, string $gedrec): void
    {
        // Insert all new rows together
        $rows = [];

        preg_match_all('/\n2 PLAC (.+)/', $gedrec, $matches);

        $places = array_unique($matches[1]);

        foreach ($places as $place_name) {
            $place = new Place($place_name, $tree);

            // Calling Place::id() will create the entry in the database, if it doesn't already exist.
            while ($place->id() !== 0) {
                $rows[] = [
                    'pl_p_id' => $place->id(),
                    'pl_gid'  => $xref,
                    'pl_file' => $tree->id(),
                ];

                $place = $place->parent();
            }
        }

        // array_unique doesn't work with arrays of arrays
        $rows = array_intersect_key($rows, array_unique(array_map('serialize', $rows)));

        // PDO has a limit of 65535 placeholders, and each row requires 3 placeholders.
        foreach (array_chunk($rows, 20000) as $chunk) {
            DB::table('placelinks')->insert($chunk);
        }
    }

    /**
     * Extract all the dates from the given record and insert them into the database.
     *
     * @param string $xref
     * @param int    $ged_id
     * @param string $gedrec
     *
     * @return void
     */
    public static function updateDates(string $xref, int $ged_id, string $gedrec): void
    {
        // Insert all new rows together
        $rows = [];

        preg_match_all("/\n1 (\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))(?:\n[2-9].*)*/", $gedrec, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fact = $match[1];
            $date = new Date($match[2]);
            $rows[] = [
                'd_day'        => $date->minimumDate()->day,
                'd_month'      => $date->minimumDate()->format('%O'),
                'd_mon'        => $date->minimumDate()->month,
                'd_year'       => $date->minimumDate()->year,
                'd_julianday1' => $date->minimumDate()->minimumJulianDay(),
                'd_julianday2' => $date->minimumDate()->maximumJulianDay(),
                'd_fact'       => $fact,
                'd_gid'        => $xref,
                'd_file'       => $ged_id,
                'd_type'       => $date->minimumDate()->format('%@'),
            ];

            $rows[] = [
                'd_day'        => $date->maximumDate()->day,
                'd_month'      => $date->maximumDate()->format('%O'),
                'd_mon'        => $date->maximumDate()->month,
                'd_year'       => $date->maximumDate()->year,
                'd_julianday1' => $date->maximumDate()->minimumJulianDay(),
                'd_julianday2' => $date->maximumDate()->maximumJulianDay(),
                'd_fact'       => $fact,
                'd_gid'        => $xref,
                'd_file'       => $ged_id,
                'd_type'       => $date->minimumDate()->format('%@'),
            ];
        }

        // array_unique doesn't work with arrays of arrays
        $rows = array_intersect_key($rows, array_unique(array_map('serialize', $rows)));

        DB::table('dates')->insert($rows);
    }

    /**
     * Extract all the links from the given record and insert them into the database
     *
     * @param string $xref
     * @param int    $ged_id
     * @param string $gedrec
     *
     * @return void
     */
    public static function updateLinks(string $xref, int $ged_id, string $gedrec): void
    {
        // Insert all new rows together
        $rows = [];

        preg_match_all('/\n\d+ (' . Gedcom::REGEX_TAG . ') @(' . Gedcom::REGEX_XREF . ')@/', $gedrec, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            // Take care of "duplicates" that differ on case/collation, e.g. "SOUR @S1@" and "SOUR @s1@"
            $rows[$match[1] . strtoupper($match[2])] = [
                'l_from' => $xref,
                'l_to'   => $match[2],
                'l_type' => $match[1],
                'l_file' => $ged_id,
            ];
        }

        DB::table('link')->insert($rows);
    }

    /**
     * Extract all the names from the given record and insert them into the database.
     *
     * @param string     $xref
     * @param int        $ged_id
     * @param Individual $record
     *
     * @return void
     */
    public static function updateNames(string $xref, int $ged_id, Individual $record): void
    {
        // Insert all new rows together
        $rows = [];

        foreach ($record->getAllNames() as $n => $name) {
            if ($name['givn'] === Individual::PRAENOMEN_NESCIO) {
                $soundex_givn_std = null;
                $soundex_givn_dm  = null;
            } else {
                $soundex_givn_std = Soundex::russell($name['givn']);
                $soundex_givn_dm  = Soundex::daitchMokotoff($name['givn']);
            }

            if ($name['surn'] === Individual::NOMEN_NESCIO) {
                $soundex_surn_std = null;
                $soundex_surn_dm  = null;
            } else {
                $soundex_surn_std = Soundex::russell($name['surname']);
                $soundex_surn_dm  = Soundex::daitchMokotoff($name['surname']);
            }

            $rows[] = [
                'n_file'             => $ged_id,
                'n_id'               => $xref,
                'n_num'              => $n,
                'n_type'             => $name['type'],
                'n_sort'             => mb_substr($name['sort'], 0, 255),
                'n_full'             => mb_substr($name['fullNN'], 0, 255),
                'n_surname'          => mb_substr($name['surname'], 0, 255),
                'n_surn'             => mb_substr($name['surn'], 0, 255),
                'n_givn'             => mb_substr($name['givn'], 0, 255),
                'n_soundex_givn_std' => $soundex_givn_std,
                'n_soundex_surn_std' => $soundex_surn_std,
                'n_soundex_givn_dm'  => $soundex_givn_dm,
                'n_soundex_surn_dm'  => $soundex_surn_dm,
            ];
        }

        DB::table('name')->insert($rows);
    }

    /**
     * Extract inline media data, and convert to media objects.
     *
     * @param Tree   $tree
     * @param string $gedcom
     *
     * @return string
     */
    public static function convertInlineMedia(Tree $tree, string $gedcom): string
    {
        while (preg_match('/\n1 OBJE(?:\n[2-9].+)+/', $gedcom, $match)) {
            $xref   = self::createMediaObject($match[0], $tree);
            $gedcom = strtr($gedcom, [$match[0] =>  "\n1 OBJE @" . $xref . '@']);
        }
        while (preg_match('/\n2 OBJE(?:\n[3-9].+)+/', $gedcom, $match)) {
            $xref   = self::createMediaObject($match[0], $tree);
            $gedcom = strtr($gedcom, [$match[0] =>  "\n2 OBJE @" . $xref . '@']);
        }
        while (preg_match('/\n3 OBJE(?:\n[4-9].+)+/', $gedcom, $match)) {
            $xref   = self::createMediaObject($match[0], $tree);
            $gedcom = strtr($gedcom, [$match[0] =>  "\n3 OBJE @" . $xref . '@']);
        }

        return $gedcom;
    }

    /**
     * Create a new media object, from inline media data.
     *
     * GEDCOM 5.5.1 specifies: +1 FILE / +2 FORM / +3 MEDI / +1 TITL
     * GEDCOM 5.5 specifies: +1 FILE / +1 FORM / +1 TITL
     * GEDCOM 5.5.1 says that GEDCOM 5.5 specifies:  +1 FILE / +1 FORM / +2 MEDI
     *
     * Legacy generates: +1 FORM / +1 FILE / +1 TITL / +1 _SCBK / +1 _PRIM / +1 _TYPE / +1 NOTE
     * RootsMagic generates: +1 FILE / +1 FORM / +1 TITL
     *
     * @param string $gedcom
     * @param Tree   $tree
     *
     * @return string
     */
    public static function createMediaObject(string $gedcom, Tree $tree): string
    {
        preg_match('/\n\d FILE (.+)/', $gedcom, $match);
        $file = $match[1] ?? '';

        preg_match('/\n\d TITL (.+)/', $gedcom, $match);
        $title = $match[1] ?? '';

        preg_match('/\n\d FORM (.+)/', $gedcom, $match);
        $format = $match[1] ?? '';

        preg_match('/\n\d MEDI (.+)/', $gedcom, $match);
        $media = $match[1] ?? '';

        preg_match('/\n\d _SCBK (.+)/', $gedcom, $match);
        $scrapbook = $match[1] ?? '';

        preg_match('/\n\d _PRIM (.+)/', $gedcom, $match);
        $primary = $match[1] ?? '';

        preg_match('/\n\d _TYPE (.+)/', $gedcom, $match);
        if ($media === '') {
            // Legacy uses _TYPE instead of MEDI
            $media = $match[1] ?? '';
            $type  = '';
        } else {
            $type = $match[1] ?? '';
        }

        preg_match_all('/\n\d NOTE (.+(?:\n\d CONT.*)*)/', $gedcom, $matches);
        $notes = $matches[1] ?? [];

        // Have we already created a media object with the same title/filename?
        $xref = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('descriptive_title', '=', mb_substr($title, 0, 248))
            ->where('multimedia_file_refn', '=', mb_substr($file, 0, 248))
            ->value('m_id');

        if ($xref === null) {
            $xref = Registry::xrefFactory()->make(Media::RECORD_TYPE);

            // convert to a media-object
            $gedcom = '0 @' . $xref . "@ OBJE\n1 FILE " . $file;

            if ($format !== '') {
                $gedcom .= "\n2 FORM " . $format;

                if ($media !== '') {
                    $gedcom .= "\n3 TYPE " . $media;
                }
            }

            if ($title !== '') {
                $gedcom .= "\n3 TITL " . $title;
            }

            if ($scrapbook !== '') {
                $gedcom .= "\n1 _SCBK " . $scrapbook;
            }

            if ($primary !== '') {
                $gedcom .= "\n1 _PRIM " . $primary;
            }

            if ($type !== '') {
                $gedcom .= "\n1 _TYPE " . $type;
            }

            foreach ($notes as $note) {
                $gedcom .= "\n1 NOTE " . strtr($note, ["\n3" => "\n2", "\n4" => "\n2", "\n5" => "\n2"]);
            }

            DB::table('media')->insert([
                'm_id'     => $xref,
                'm_file'   => $tree->id(),
                'm_gedcom' => $gedcom,
            ]);

            DB::table('media_file')->insert([
                'm_id'                 => $xref,
                'm_file'               => $tree->id(),
                'multimedia_file_refn' => mb_substr($file, 0, 248),
                'multimedia_format'    => mb_substr($format, 0, 4),
                'source_media_type'    => mb_substr($media, 0, 15),
                'descriptive_title'    => mb_substr($title, 0, 248),
            ]);
        }

        return $xref;
    }

    /**
     * update a record in the database
     *
     * @param string $gedrec
     * @param Tree   $tree
     * @param bool   $delete
     *
     * @return void
     * @throws GedcomErrorException
     */
    public static function updateRecord(string $gedrec, Tree $tree, bool $delete): void
    {
        if (preg_match('/^0 @(' . Gedcom::REGEX_XREF . ')@ (' . Gedcom::REGEX_TAG . ')/', $gedrec, $match)) {
            [, $gid, $type] = $match;
        } elseif (preg_match('/^0 (HEAD)(?:\n|$)/', $gedrec, $match)) {
            // The HEAD record has no XREF.  Any others?
            $gid  = $match[1];
            $type = $match[1];
        } else {
            throw new GedcomErrorException($gedrec);
        }

        // Place links
        DB::table('placelinks')
            ->where('pl_gid', '=', $gid)
            ->where('pl_file', '=', $tree->id())
            ->delete();

        // Orphaned places.  If we're deleting  "Westminster, London, England",
        // then we may also need to delete "London, England" and "England".
        do {
            $affected = DB::table('places')
                ->leftJoin('placelinks', static function (JoinClause $join): void {
                    $join
                        ->on('p_id', '=', 'pl_p_id')
                        ->on('p_file', '=', 'pl_file');
                })
                ->whereNull('pl_p_id')
                ->delete();
        } while ($affected > 0);

        DB::table('dates')
            ->where('d_gid', '=', $gid)
            ->where('d_file', '=', $tree->id())
            ->delete();

        DB::table('name')
            ->where('n_id', '=', $gid)
            ->where('n_file', '=', $tree->id())
            ->delete();

        DB::table('link')
            ->where('l_from', '=', $gid)
            ->where('l_file', '=', $tree->id())
            ->delete();

        switch ($type) {
            case Individual::RECORD_TYPE:
                DB::table('individuals')
                    ->where('i_id', '=', $gid)
                    ->where('i_file', '=', $tree->id())
                    ->delete();
                break;

            case Family::RECORD_TYPE:
                DB::table('families')
                    ->where('f_id', '=', $gid)
                    ->where('f_file', '=', $tree->id())
                    ->delete();
                break;

            case Source::RECORD_TYPE:
                DB::table('sources')
                    ->where('s_id', '=', $gid)
                    ->where('s_file', '=', $tree->id())
                    ->delete();
                break;

            case Media::RECORD_TYPE:
                DB::table('media_file')
                    ->where('m_id', '=', $gid)
                    ->where('m_file', '=', $tree->id())
                    ->delete();

                DB::table('media')
                    ->where('m_id', '=', $gid)
                    ->where('m_file', '=', $tree->id())
                    ->delete();
                break;

            default:
                DB::table('other')
                    ->where('o_id', '=', $gid)
                    ->where('o_file', '=', $tree->id())
                    ->delete();
                break;
        }

        if (!$delete) {
            self::importRecord($gedrec, $tree, true);
        }
    }
}
