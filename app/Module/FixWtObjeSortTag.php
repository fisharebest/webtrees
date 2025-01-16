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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

use function str_contains;

use const PHP_INT_MAX;

/**
 * Class FixPrimaryTag
 */
class FixWtObjeSortTag extends AbstractModule implements ModuleDataFixInterface
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

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Convert %s tags to GEDCOM 5.5.1', 'INDI:_WT_OBJE_SORT');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('_WT_OBJE_SORT tags were used by old versions of webtrees to indicate the preferred image for an individual. An alternative is to re-order the images so that the preferred one is listed first.');
    }

    /**
     * XREFs of media records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<int,string>
     */
    public function individualsToFix(Tree $tree, array $params): Collection
    {
        return $this->individualsToFixQuery($tree, $params)
            ->where('i_file', '=', $tree->id())
            ->where(function (Builder $query): void {
                $query
                    ->where('i_gedcom', 'LIKE', "%\n1 _WT_OBJE_SORT %")
                    ->orWhere('i_gedcom', 'LIKE', "%\n1 _WT_OBJE_SORT %");
            })
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
        return str_contains($record->gedcom(), "\n1 _WT_OBJE_SORT ") || str_contains($record->gedcom(), "\n1 _PGV_OBJE_SORT ");
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
        $new = $this->reorderMediaLinks($record);

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
        $record->updateRecord($this->reorderMediaLinks($record), false);
    }

    /**
     * @param GedcomRecord $record
     *
     * @return string
     */
    private function reorderMediaLinks(GedcomRecord $record): string
    {
        // Sort the level 1 media links in this order
        $wt_obje_sort = $record->facts(['_PGV_OBJE_SORT', '_WT_OBJE_SORT'], false, null, true)
            ->map(static fn (Fact $fact): string => $fact->value());

        $callback = static function (Fact $x, Fact $y) use ($wt_obje_sort): int {
            $sort1 = $wt_obje_sort->search($x->value(), true);
            $sort2 = $wt_obje_sort->search($y->value(), true);
            $sort1 = $sort1 === false ? PHP_INT_MAX : $sort1;
            $sort2 = $sort2 === false ? PHP_INT_MAX : $sort2;

            return $sort1 <=> $sort2;
        };

        $obje = $record
            ->facts(['OBJE'], false, null, true)
            ->sort($callback);

        $gedcom = '0 @' . $record->xref() . "@ INDI\n" . $record->facts([], false, null, true)
            ->filter(static fn (Fact $fact): bool => $fact->tag() !== 'INDI:OBJE' && $fact->tag() !== 'INDI:_WT_OBJE_SORT' && $fact->tag() !== '_PGV_OBJE_SORT:OBJE')
            ->map(static fn (Fact $fact): string => $fact->gedcom())
            ->implode("\n");

        return $gedcom . $obje->map(static fn (Fact $fact): string => "\n" . $fact->gedcom())->implode('');
    }
}
