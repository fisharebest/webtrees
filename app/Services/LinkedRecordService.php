<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

use function addcslashes;

/**
 * Find records linked to other records
 */
class LinkedRecordService
{
    /**
     * Find all records linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Family>
     */
    public function allLinkedRecords(GedcomRecord $record): Collection
    {
        $like = addcslashes($record->xref(), '\\%_');

        $union = DB::table('change')
            ->where('gedcom_id', '=', $record->tree()->id())
            ->where('new_gedcom', 'LIKE', '%@' . $like . '@%')
            ->where('new_gedcom', 'NOT LIKE', '0 @' . $like . '@%')
            ->whereIn('change_id', function (Builder $query) use ($record): void {
                $query
                    ->select(new Expression('MAX(change_id)'))
                    ->from('change')
                    ->where('gedcom_id', '=', $record->tree()->id())
                    ->where('status', '=', 'pending')
                    ->groupBy(['xref']);
            })
            ->select(['xref']);

        $xrefs = DB::table('link')
            ->where('l_file', '=', $record->tree()->id())
            ->where('l_to', '=', $record->xref())
            ->select(['l_from'])
            ->union($union)
            ->pluck('l_from');

        return $xrefs->map(static fn (string $xref) => Registry::gedcomRecordFactory()->make($xref, $record->tree()));
    }

    /**
     * Find families linked to a record.
     *
     * @param GedcomRecord $record
     * @param string|null  $link_type
     *
     * @return Collection<int,Family>
     */
    public function linkedFamilies(GedcomRecord $record, string $link_type = null): Collection
    {
        $query = DB::table('families')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'f_file')
                    ->on('l_from', '=', 'f_id');
            })
            ->where('f_file', '=', $record->tree()->id())
            ->where('l_to', '=', $record->xref());

        if ($link_type !== null) {
            $query->where('l_type', '=', $link_type);
        }

        return $query
            ->distinct()
            ->select(['families.*'])
            ->get()
            ->map(Registry::familyFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find individuals linked to a record.
     *
     * @param GedcomRecord $record
     * @param string|null  $link_type
     *
     * @return Collection<int,Individual>
     */
    public function linkedIndividuals(GedcomRecord $record, string $link_type = null): Collection
    {
        $query = DB::table('individuals')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'i_file')
                    ->on('l_from', '=', 'i_id');
            })
            ->where('i_file', '=', $record->tree()->id())
            ->where('l_to', '=', $record->xref());

        if ($link_type !== null) {
            $query->where('l_type', '=', $link_type);
        }

        return $query
            ->distinct()
            ->select(['individuals.*'])
            ->get()
            ->map(Registry::individualFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find locations linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Location>
     */
    public function linkedLocations(GedcomRecord $record): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $record->tree()->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['other.*'])
            ->get()
            ->map(Registry::locationFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find media objects linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Media>
     */
    public function linkedMedia(GedcomRecord $record): Collection
    {
        return DB::table('media')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'm_file')
                    ->on('l_from', '=', 'm_id');
            })
            ->where('m_file', '=', $record->tree()->id())
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['media.*'])
            ->get()
            ->map(Registry::mediaFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find notes linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Note>
     */
    public function linkedNotes(GedcomRecord $record): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $record->tree()->id())
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['other.*'])
            ->get()
            ->map(Registry::noteFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find repositories linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Repository>
     */
    public function linkedRepositories(GedcomRecord $record): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $record->tree()->id())
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['other.*'])
            ->get()
            ->map(Registry::repositoryFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find sources linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Source>
     */
    public function linkedSources(GedcomRecord $record): Collection
    {
        return DB::table('sources')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 's_file')
                    ->on('l_from', '=', 's_id');
            })
            ->where('s_file', '=', $record->tree()->id())
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['sources.*'])
            ->get()
            ->map(Registry::sourceFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Find submitters linked to a record.
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Repository>
     */
    public function linkedSubmitters(GedcomRecord $record): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $record->tree()->id())
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->where('l_to', '=', $record->xref())
            ->distinct()
            ->select(['other.*'])
            ->distinct()
            ->get()
            ->map(Registry::repositoryFactory()->mapper($record->tree()))
            ->filter(GedcomRecord::accessFilter());
    }
}
