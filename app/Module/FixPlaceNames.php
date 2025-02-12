<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use function addcslashes;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function view;

/**
 * Class FixPlaceNames
 */
class FixPlaceNames extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    private DataFixService $data_fix_service;

    /**
     * @param DataFixService $data_fix_service
     */
    public function __construct(DataFixService $data_fix_service)
    {
        $this->data_fix_service = $data_fix_service;
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Update place names');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('Update the higher-level parts of place names, while keeping the lower-level parts.');
    }

    /**
     * Options form.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function fixOptions(Tree $tree): string
    {
        return view('modules/fix-place-names/options', []);
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<int,string>|null
     */
    protected function familiesToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['search-for'] === '' || $params['replace-with'] === '') {
            return null;
        }

        $search = '%' . addcslashes($params['search-for'], '\\%_') . '%';

        return  $this->familiesToFixQuery($tree, $params)
            ->where('f_gedcom', 'LIKE', $search)
            ->pluck('f_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<int,string>|null
     */
    protected function individualsToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['search-for'] === '' || $params['replace-with'] === '') {
            return null;
        }

        $search = '%' . addcslashes($params['search-for'], '\\%_') . '%';

        return  $this->individualsToFixQuery($tree, $params)
            ->where('i_file', '=', $tree->id())
            ->where('i_gedcom', 'LIKE', $search)
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
        $search = preg_quote($params['search-for'], '/');
        $regex  = '/\n2 PLAC (?:.*, )?' . $search . '(\n|$)/';

        return preg_match($regex, $record->gedcom()) === 1;
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
        $new = $this->updateGedcom($record, $params);

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
        $record->updateRecord($this->updateGedcom($record, $params), false);
    }

    /**
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    private function updateGedcom(GedcomRecord $record, array $params): string
    {
        $search  = preg_quote($params['search-for'], '/');
        $regex   = '/(\n2 PLAC (?:.*, )?)' . $search . '(\n|$)/';
        $replace = '$1' . addcslashes($params['replace-with'], '$\\') . '$2';

        return preg_replace($regex, $replace, $record->gedcom());
    }
}
