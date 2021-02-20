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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Trait ModuleDataFixTrait - default implementation of ModuleDataFixTrait
 */
trait ModuleDataFixTrait
{
    /**
     * Options form.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function fixOptions(Tree $tree): string
    {
        return '';
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<stdClass>
     */
    public function recordsToFix(Tree $tree, array $params): Collection
    {
        $families     = $this->familiesToFix($tree, $params);
        $individuals  = $this->individualsToFix($tree, $params);
        $locations    = $this->locationsToFix($tree, $params);
        $media        = $this->mediaToFix($tree, $params);
        $notes        = $this->notesToFix($tree, $params);
        $repositories = $this->repositoriesToFix($tree, $params);
        $sources      = $this->sourcesToFix($tree, $params);
        $submitters   = $this->submittersToFix($tree, $params);

        $records = new Collection();

        if ($families !== null) {
            $records = $records->concat($this->mergePendingRecords($families, $tree, Family::RECORD_TYPE));
        }

        if ($individuals !== null) {
            $records = $records->concat($this->mergePendingRecords($individuals, $tree, Individual::RECORD_TYPE));
        }

        if ($locations !== null) {
            $records = $records->concat($this->mergePendingRecords($locations, $tree, Location::RECORD_TYPE));
        }

        if ($media !== null) {
            $records = $records->concat($this->mergePendingRecords($media, $tree, Media::RECORD_TYPE));
        }

        if ($notes !== null) {
            $records = $records->concat($this->mergePendingRecords($notes, $tree, Note::RECORD_TYPE));
        }

        if ($repositories !== null) {
            $records = $records->concat($this->mergePendingRecords($repositories, $tree, Repository::RECORD_TYPE));
        }

        if ($sources !== null) {
            $records = $records->concat($this->mergePendingRecords($sources, $tree, Source::RECORD_TYPE));
        }

        if ($submitters !== null) {
            $records = $records->concat($this->mergePendingRecords($submitters, $tree, Submitter::RECORD_TYPE));
        }

        return $records
            ->unique()
            ->sort(static function (stdClass $x, stdClass $y) {
                return $x->xref <=> $y->xref;
            });
    }

    /**
     * Does a record need updating?
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record, array $params): bool
    {
        return false;
    }

    /**
     * Show the changes we would make
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    public function previewUpdate(GedcomRecord $record, array $params): string
    {
        return $record->fullName();
    }

    /**
     * Fix a record
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return void
     */
    public function updateRecord(GedcomRecord $record, array $params): void
    {
    }

    /**
     * XREFs of family records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function familiesToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function familiesToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('families')
            ->where('f_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('f_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of individual records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function individualsToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function individualsToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('i_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of location records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function locationsToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function locationsToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('other')
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->where('o_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('o_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of media records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function mediaToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function mediaToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('media')
            ->where('m_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('m_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of note records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function notesToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function notesToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('other')
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->where('o_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('o_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of repository records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function repositoriesToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function repositoriesToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('other')
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->where('o_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('o_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of source records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function sourcesToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function sourcesToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('sources')
            ->where('s_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('s_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * XREFs of submitter records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function submittersToFix(Tree $tree, array $params): ?Collection
    {
        return null;
    }

    /**
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Builder
     */
    protected function submittersToFixQuery(Tree $tree, array $params): Builder
    {
        $query = DB::table('other')
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->where('o_file', '=', $tree->id());

        if (isset($params['start'], $params['end'])) {
            $query->whereBetween('o_id', [$params['start'], $params['end']]);
        }

        return $query;
    }

    /**
     * Merge pending changes of a given type.  We need to check all pending records.
     *
     * @param Collection<string> $records
     * @param Tree               $tree
     * @param string             $type
     *
     * @return Collection<stdClass>
     */
    private function mergePendingRecords(Collection $records, Tree $tree, string $type): Collection
    {
        $pending = DB::table('change')
            ->where('gedcom_id', '=', $tree->id())
            ->where('status', '=', 'pending')
            ->where(static function (Builder $query) use ($type): void {
                $query
                    ->where('old_gedcom', 'LIKE', '%@ ' . $type . '%')
                    ->orWhere('new_gedcom', 'LIKE', '%@ ' . $type . '%');
            })
            ->pluck('xref');

        return $records
            ->concat($pending)
            ->map(static function (string $xref) use ($type): stdClass {
                return (object) ['xref' => $xref, 'type' => $type];
            });
    }
}
