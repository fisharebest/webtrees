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
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function e;
use function str_contains;
use function strtoupper;

class FixPrimaryTag extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    private LinkedRecordService $linked_record_service;

    /**
     * @param LinkedRecordService $linked_record_service
     */
    public function __construct(LinkedRecordService $linked_record_service)
    {
        $this->linked_record_service = $linked_record_service;
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Convert %s tags to GEDCOM 5.5.1', 'OBJE:_PRIM');
    }

    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('“Highlighted image” (_PRIM) tags are used by some genealogy applications to indicate the preferred image for an individual. An alternative is to re-order the images so that the preferred one is listed first.');
    }

    /**
     * XREFs of media records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<int,string>
     */
    public function mediaToFix(Tree $tree, array $params): Collection
    {
        return $this->mediaToFixQuery($tree, $params)
            ->where('m_file', '=', $tree->id())
            ->where('m_gedcom', 'LIKE', "%\n1 _PRIM %")
            ->pluck('m_id');
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
        return str_contains($record->gedcom(), "\n1 _PRIM ");
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
        $html = '';
        foreach ($record->facts(['_PRIM']) as $prim) {
            $html = '<p>' . I18N::translate('Delete') . ' – <code>' . e($prim->gedcom()) . '</code></p>';
        }

        $html .= '<ul>';
        foreach ($this->linked_record_service->linkedIndividuals($record) as $individual) {
            $html .= '<li>' . I18N::translate('Re-order media') . ' – <a href="' . e($individual->url()) . '">' . $individual->fullName() . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
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
        $facts = $record->facts(['_PRIM'])->filter(static fn (Fact $fact): bool => !$fact->isPendingDeletion());

        foreach ($facts as $fact) {
            $primary = strtoupper($fact->value()) !== 'N';

            foreach ($this->linked_record_service->linkedIndividuals($record) as $individual) {
                $this->updateMediaLinks($individual, $record->xref(), $primary);
            }

            $record->deleteFact($fact->id(), false);
        }
    }

    /**
     * @param Individual $individual
     * @param string     $xref
     * @param bool       $primary
     */
    private function updateMediaLinks(Individual $individual, string $xref, bool $primary): void
    {
        $facts = $individual->facts([], false, null, true);

        $facts1 = new Collection();
        $facts2 = new Collection();
        $facts3 = new Collection();
        $facts4 = new Collection();

        foreach ($facts as $fact) {
            if ($fact->tag() !== 'INDI:OBJE') {
                $facts1->push($fact);
            } elseif ($fact->value() !== '@' . $xref . '@') {
                $facts3->push($fact);
            } elseif ($primary) {
                $facts2->push($fact);
            } else {
                $facts4->push($fact);
            }
        }

        $sorted_facts = $facts1->concat($facts2)->concat($facts3)->concat($facts4);

        $gedcom = $sorted_facts->map(static fn (Fact $fact): string => "\n" . $fact->gedcom())->implode('');

        $gedcom = '0 @' . $individual->xref() . '@ INDI' . $gedcom;

        $individual->updateRecord($gedcom, false);
    }
}
