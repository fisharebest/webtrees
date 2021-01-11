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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function preg_match;
use function preg_replace;

/**
 * Class FixNameSlashesAndSpaces
 */
class FixNameSlashesAndSpaces extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    /** @var DataFixService */
    private $data_fix_service;

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
        return I18N::translate('Fix name slashes and spaces');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('Correct NAME records of the form “John/DOE/” or “John /DOE”, as produced by older genealogy programs.');
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
            preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*$/m', $gedcom) ||
            preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ]\//m', $gedcom);
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
            '/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*)$/m',
            '/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ])(\/)/m',
        ], [
            '$1/',
            '$1 $2',
        ], $record->gedcom());
    }
}
