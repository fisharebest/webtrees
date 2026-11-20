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
use Fisharebest\Collection\Collection;

use function addcslashes;

/**
 * Find records linked to other records
 */
class LinkedRecordService
{
    /**
     * @return Collection<GedcomRecord>
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
            ->from('link')
            ->whereEqual('l_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->union($union)
            ->pluck('l_from', in: Collection::class)
            ->map(static fn (string $xref) => Registry::gedcomRecordFactory()->make($xref, $record->tree()))
            ->filterInstanceOf(GedcomRecord::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Family>
     */
    public function linkedFamilies(GedcomRecord $record, string|null $link_type = null): Collection
    {
        $query = DB::query()
            ->select('families.*')
            ->distinct()
            ->from('families')
            ->join('link', ['l_file' => 'f_file', 'l_from' => 'f_id'])
            ->whereEqual('f_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref());

        if ($link_type !== null) {
            $query = $query->whereEqual('l_type', $link_type);
        }

        return $query
            ->get(in: Collection::class)
            ->map(Registry::familyFactory()->mapper($record->tree()))
            ->filterInstanceOf(Family::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Individual>
     */
    public function linkedIndividuals(GedcomRecord $record, string|null $link_type = null): Collection
    {
        $query = DB::query()
            ->select('individuals.*')
            ->distinct()
            ->from('individuals')
            ->join('link', ['l_file' => 'i_file', 'l_from' => 'i_id'])
            ->whereEqual('i_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref());

        if ($link_type !== null) {
            $query = $query->whereEqual('l_type', $link_type);
        }

        return $query
            ->get(in: Collection::class)
            ->map(Registry::individualFactory()->mapper($record->tree()))
            ->filterInstanceOf(Individual::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Location>
     */
    public function linkedLocations(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', ['l_file' => 'o_file', 'l_from' => 'o_id'])
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Location::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::locationFactory()->mapper($record->tree()))
            ->filterInstanceOf(Location::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Media>
     */
    public function linkedMedia(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('media.*')
            ->distinct()
            ->from('media')
            ->join('link', ['l_file' => 'm_file', 'l_from' => 'm_id'])
            ->whereEqual('m_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::mediaFactory()->mapper($record->tree()))
            ->filterInstanceOf(Media::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Note>
     */
    public function linkedNotes(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', ['l_file' => 'o_file', 'l_from' => 'o_id'])
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Note::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::noteFactory()->mapper($record->tree()))
            ->filterInstanceOf(Note::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Repository>
     */
    public function linkedRepositories(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', ['l_file' => 'o_file', 'l_from' => 'o_id'])
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Repository::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::repositoryFactory()->mapper($record->tree()))
            ->filterInstanceOf(Repository::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Source>
     */
    public function linkedSources(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('sources.*')
            ->distinct()
            ->from('sources')
            ->join('link', ['l_file' => 's_file', 'l_from' => 's_id'])
            ->whereEqual('s_file', $record->tree()->id())
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::sourceFactory()->mapper($record->tree()))
            ->filterInstanceOf(Source::class)
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @return Collection<Submitter>
     */
    public function linkedSubmitters(GedcomRecord $record): Collection
    {
        return DB::query()
            ->select('other.*')
            ->distinct()
            ->from('other')
            ->join('link', ['l_file' => 'o_file', 'l_from' => 'o_id'])
            ->whereEqual('o_file', $record->tree()->id())
            ->whereEqual('o_type', Submitter::RECORD_TYPE)
            ->whereEqual('l_to', $record->xref())
            ->get(in: Collection::class)
            ->map(Registry::submitterFactory()->mapper($record->tree()))
            ->filterInstanceOf(Submitter::class)
            ->filter(GedcomRecord::accessFilter());
    }
}
