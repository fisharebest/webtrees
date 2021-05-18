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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function preg_match;
use function preg_replace;

/**
 * Class FixDuplicateLinks
 */
class FixDuplicateLinks extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    private DataFixService $data_fix_service;

    /**
     * FixMissingDeaths constructor.
     *
     * @param DataFixService $data_fix_service
     */
    public function __construct(DataFixService $data_fix_service)
    {
        $this->data_fix_service = $data_fix_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Remove duplicate links');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('A common error is to have multiple links to the same record, for example listing the same child more than once in a family record.');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree          $tree
     * @param array<string> $params
     *
     * @return Collection<string>
     */
    protected function familiesToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->familiesToFixQuery($tree, $params)
            ->pluck('f_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function individualsToFix(Tree $tree, array $params): ?Collection
    {
        // No DB querying possible?  Select all.
        return $this->individualsToFixQuery($tree, $params)
            ->pluck('i_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>
     */
    protected function mediaToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->mediaToFixQuery($tree, $params)
            ->pluck('m_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>
     */
    protected function notesToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->notesToFixQuery($tree, $params)
            ->pluck('o_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>
     */
    protected function repositoriesToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->repositoriesToFixQuery($tree, $params)
            ->pluck('o_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>
     */
    protected function sourcesToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->sourcesToFixQuery($tree, $params)
            ->pluck('s_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>
     */
    protected function submittersToFix(Tree $tree, array $params): Collection
    {
        // No DB querying possible?  Select all.
        return $this->submittersToFixQuery($tree, $params)
            ->pluck('o_id');
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
        $gedcom = $record->gedcom();

        return
            preg_match('/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))(?:\n1.*(?:\n[2-9].*)*)*\1/', $gedcom) ||
            preg_match('/(\n2.*@.+@.*(?:(?:\n[3-9].*)*))(?:\n2.*(?:\n[3-9].*)*)*\1/', $gedcom) ||
            preg_match('/(\n3.*@.+@.*(?:(?:\n[4-9].*)*))(?:\n3.*(?:\n[4-9].*)*)*\1/', $gedcom);
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
        $old = $record->gedcom();
        $new = $this->updateGedcom($record);

        return $this->data_fix_service->gedcomDiff($record->tree(), $old, $new);
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
        $record->updateRecord($this->updateGedcom($record), false);
    }

    /**
     * @param GedcomRecord $record
     *
     * @return string
     */
    private function updateGedcom(GedcomRecord $record): string
    {
        return preg_replace([
            '/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
            '/(\n2.*@.+@.*(?:(?:\n[3-9].*)*))((?:\n2.*(?:\n[3-9].*)*)*\1)/',
            '/(\n3.*@.+@.*(?:(?:\n[4-9].*)*))((?:\n3.*(?:\n[4-9].*)*)*\1)/',
        ], '$2', $record->gedcom());
    }
}
