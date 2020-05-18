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

namespace Fisharebest\Webtrees\Factories;

use Closure;
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use InvalidArgumentException;
use stdClass;

use function assert;

/**
 * Make a GedcomRecord object.
 */
class GedcomRecordFactory extends AbstractGedcomRecordFactory implements GedcomRecordFactoryInterface
{
    /**
     * Create a GedcomRecord object.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return GedcomRecord|null
     */
    public function make(string $xref, Tree $tree, string $gedcom = null): ?GedcomRecord
    {
        // We know the type of the record.  Return it directly.
        if ($gedcom !== null && preg_match('/^0(?: @[^@]+@)? ([A-Z_]+)/', $gedcom, $match)) {
            switch ($match[1]) {
                case Family::RECORD_TYPE:
                    return Factory::family()->make($xref, $tree, $gedcom);
                case Header::RECORD_TYPE:
                    return Factory::header()->make($xref, $tree, $gedcom);
                case Individual::RECORD_TYPE:
                    return Factory::individual()->make($xref, $tree, $gedcom);
                case Location::RECORD_TYPE:
                    return Factory::location()->make($xref, $tree, $gedcom);
                case Media::RECORD_TYPE:
                    return Factory::media()->make($xref, $tree, $gedcom);
                case Note::RECORD_TYPE:
                    return Factory::note()->make($xref, $tree, $gedcom);
                case Repository::RECORD_TYPE:
                    return Factory::repository()->make($xref, $tree, $gedcom);
                case Source::RECORD_TYPE:
                    return Factory::source()->make($xref, $tree, $gedcom);
                case Submitter::RECORD_TYPE:
                    return Factory::submitter()->make($xref, $tree, $gedcom);
                case Submission::RECORD_TYPE:
                    return Factory::submission()->make($xref, $tree, $gedcom);
            }
        }

        // We do not know the type of the record.  Try them all in turn.
        return
            Factory::family()->make($xref, $tree, $gedcom) ??
            Factory::individual()->make($xref, $tree, $gedcom) ??
            Factory::media()->make($xref, $tree, $gedcom) ??
            Factory::note()->make($xref, $tree, $gedcom) ??
            Factory::repository()->make($xref, $tree, $gedcom) ??
            Factory::source()->make($xref, $tree, $gedcom) ??
            Factory::submitter()->make($xref, $tree, $gedcom) ??
            Factory::submission()->make($xref, $tree, $gedcom) ??
            Factory::location()->make($xref, $tree, $gedcom) ??
            Factory::header()->make($xref, $tree, $gedcom) ??
            $this->cache->remember(__CLASS__ . $xref . '@' . $tree->id(), function () use ($xref, $tree, $gedcom) {
                $gedcom = $gedcom ?? $this->gedcom($xref, $tree);

                $pending = $this->pendingChanges($tree)->get($xref);

                if ($gedcom === null && $pending === null) {
                    return null;
                }

                $xref = $this->extractXref($gedcom ?? $pending, $xref);
                $type = $this->extractType($gedcom ?? $pending);

                return $this->newGedcomRecord($type, $xref, $gedcom ?? '', $pending, $tree);
            });
    }

    /**
     * Create a GedcomRecord object from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return GedcomRecord
     */
    public function new(string $xref, string $gedcom, ?string $pending, Tree $tree): GedcomRecord
    {
        return new GedcomRecord($xref, $gedcom, $pending, $tree);
    }

    /**
     * Create a GedcomRecord object from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public function mapper(Tree $tree): Closure
    {
        return function (stdClass $row) use ($tree): GedcomRecord {
            $record = $this->make($row->o_id, $tree, $row->o_gedcom);
            assert($record instanceof GedcomRecord);

            return $record;
        };
    }

    /**
     * @param string      $type
     * @param string      $xref
     * @param string      $gedcom
     * @param string|null $pending
     * @param Tree        $tree
     *
     * @return GedcomRecord
     */
    private function newGedcomRecord(string $type, string $xref, string $gedcom, ?string $pending, Tree $tree): GedcomRecord
    {
        switch ($type) {
            case Family::RECORD_TYPE:
                return Factory::family()->new($xref, $gedcom, $pending, $tree);

            case Header::RECORD_TYPE:
                return Factory::header()->new($xref, $gedcom, $pending, $tree);

            case Individual::RECORD_TYPE:
                return Factory::individual()->new($xref, $gedcom, $pending, $tree);

            case Media::RECORD_TYPE:
                return Factory::media()->new($xref, $gedcom, $pending, $tree);

            case Note::RECORD_TYPE:
                return Factory::note()->new($xref, $gedcom, $pending, $tree);

            case Repository::RECORD_TYPE:
                return Factory::repository()->new($xref, $gedcom, $pending, $tree);

            case Source::RECORD_TYPE:
                return Factory::source()->new($xref, $gedcom, $pending, $tree);

            case Submission::RECORD_TYPE:
                return Factory::submission()->new($xref, $gedcom, $pending, $tree);

            case Submitter::RECORD_TYPE:
                return Factory::submitter()->new($xref, $gedcom, $pending, $tree);

            default:
                return $this->new($xref, $gedcom, $pending, $tree);
        }
    }

    /**
     * Extract the type of a GEDCOM record
     *
     * @param string $gedcom
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function extractType(string $gedcom): string
    {
        if (preg_match('/^0(?: @' . Gedcom::REGEX_XREF . '@)? ([_A-Z0-9]+)/', $gedcom, $match)) {
            return $match[1];
        }

        throw new InvalidArgumentException('Invalid GEDCOM record: ' . $gedcom);
    }

    /**
     * Fetch GEDCOM data from the database.
     *
     * @param string $xref
     * @param Tree   $tree
     *
     * @return string|null
     */
    private function gedcom(string $xref, Tree $tree): ?string
    {
        return DB::table('other')
            ->where('o_id', '=', $xref)
            ->where('o_file', '=', $tree->id())
            ->whereNotIn('o_type', [
                Header::RECORD_TYPE,
                Note::RECORD_TYPE,
                Repository::RECORD_TYPE,
                Submission::RECORD_TYPE,
                Submitter::RECORD_TYPE,
            ])
            ->value('o_gedcom');
    }
}
