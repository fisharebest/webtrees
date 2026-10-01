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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
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
use Illuminate\Support\Collection;

use function addcslashes;

/**
 * Find records linked to other records
 */
class LinkedRecordService
{
    /**
     * @return Collection<int,GedcomRecord>
     */
    public function allLinkedRecords(GedcomRecord $record): Collection
    {
        $like = addcslashes($record->xref(), '\\%_');

        $union = DB::query()
            ->select('xref')
            ->from('change')
            ->whereEqual('gedcom_id', $record->tree()->id())
            ->whereLike('new_gedcom', '%@' . $like . '@%')
            ->whereNotLike('new_gedcom', '0 @' . $like . '@%')
            ->whereInSubquery('change_id', DB::query()
                ->select(DB::max('change_id'))
                ->from('change')
                ->whereEqual('gedcom_id', $record->tree()->id())
                ->whereEqual('status', ChangeStatus::Pending->value)
                ->groupBy('xref'));

        return DB::query()
            ->select('l_from')
            ->from('link')
            ->whereEqual('l_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->union($union)
            ->get(in: Collection::class)
            ->pluck('l_from')
            ->map(static fn (string $xref) => Registry::gedcomRecordFactory()->make($xref, $record->tree()))
            ->whereInstanceOf(GedcomRecord::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Family>
     */
    public function linkedFamilies(GedcomRecord $record, string|null $link_type = null): Collection
    {
        $query = DB::query()
            ->select('families.*')
            ->distinct()
            ->from('families')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'f_file')->on('l_from', 'f_id'))
            ->whereEqual('f_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref());

        if ($link_type !== null) {
            $query = $query->whereEqual('l_type', $link_type);
        }

        return $query
            ->get(in: Collection::class)
            ->map(Registry::familyFactory()->mapper($record->tree()))
            ->whereInstanceOf(Family::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Individual>
     */
    public function linkedIndividuals(GedcomRecord $record, string|null $link_type = null): Collection
    {
        $query = DB::query()
            ->select('individuals.*')
            ->distinct()
            ->from('individuals')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'i_file')->on('l_from', 'i_id'))
            ->whereEqual('i_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref());

        if ($link_type !== null) {
            $query = $query->whereEqual('l_type', $link_type);
        }

        return $query
            ->get(in: Collection::class)
            ->map(Registry::individualFactory()->mapper($record->tree()))
            ->whereInstanceOf(Individual::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Location>
     */
    public function linkedLocations(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'o_file')->on('l_from', 'o_id'))
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Location::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::locationFactory()->mapper($record->tree()))
            ->whereInstanceOf(Location::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Media>
     */
    public function linkedMedia(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('media.*')
            ->distinct()
            ->from('media')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'm_file')->on('l_from', 'm_id'))
            ->whereEqual('m_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::mediaFactory()->mapper($record->tree()))
            ->whereInstanceOf(Media::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Note>
     */
    public function linkedNotes(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'o_file')->on('l_from', 'o_id'))
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Note::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::noteFactory()->mapper($record->tree()))
            ->whereInstanceOf(Note::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Repository>
     */
    public function linkedRepositories(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'o_file')->on('l_from', 'o_id'))
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Repository::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::repositoryFactory()->mapper($record->tree()))
            ->whereInstanceOf(Repository::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Source>
     */
    public function linkedSources(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('sources.*')
            ->distinct()
            ->from('sources')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 's_file')->on('l_from', 's_id'))
            ->whereEqual('s_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::sourceFactory()->mapper($record->tree()))
            ->whereInstanceOf(Source::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<int,Submitter>
     */
    public function linkedSubmitters(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'o_file')->on('l_from', 'o_id'))
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Submitter::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::submitterFactory()->mapper($record->tree()))
            ->whereInstanceOf(Submitter::class)
            ->filter(GedcomRecord::accessFilter());
    }
}
