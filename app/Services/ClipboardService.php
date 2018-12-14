<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Session;

/**
 * Copy and past facts between records.
 */
class ClipboardService
{
    // Maximum number of entries in the clipboard.
    private const CLIPBOARD_SIZE = 10;

    // Some facts can be copied to multiple types of record.
    // Others can only be copied to the same type.
    // NOTE: just because GEDCOM permits these, it doesn't mean that they are advisable.
    private const DESTINATION_TYPES = [
        'CENS' => ['FAM', 'INDI'],
        'RESI' => ['FAM', 'INDI'],
        'NOTE' => ['FAM', 'INDI', 'OBJE', 'REPO', 'SOUR'],
        'OBJE' => ['FAM', 'INDI', 'NOTE', 'SOUR'],
        'SOUR' => ['FAM', 'INDI', 'NOTE', 'OBJE'],
    ];

    /**
     * Copy a fact to the clipboard.
     *
     * @param Fact $fact
     */
    public function copyFact(Fact $fact): void
    {
        $clipboard = Session::get('clipboard', []);

        $fact_type   = $fact->getTag();
        $record_type = $fact->record()::RECORD_TYPE;

        $destination_types = self::DESTINATION_TYPES[$fact_type] ?? [$record_type];

        $fact_id = $fact->id();

        foreach ($destination_types as $destination_type) {
            // If we are copying the same fact twice, make sure the new one is at the end.
            unset($clipboard[$destination_type][$fact_id]);

            $clipboard[$destination_type][$fact_id] = [
                'factrec' => $fact->gedcom(),
                'fact'    => $fact->getTag(),
            ];

            // The clipboard only holds a limited number of facts.
            $clipboard[$destination_type] = array_slice($clipboard[$destination_type], -self::CLIPBOARD_SIZE);
        }

        Session::put('clipboard', $clipboard);
    }

    /**
     * Copy a fact from the clipboard to a record.
     *
     * @param string       $fact_id
     * @param GedcomRecord $record
     *
     * @return bool
     */
    public function pasteFact(string $fact_id, GedcomRecord $record): bool
    {
        $clipboard = Session::get('clipboard');

        $record_type = $record::RECORD_TYPE;

        if (isset($clipboard[$record_type][$fact_id])) {
            $record->createFact($clipboard[$record_type][$fact_id]['factrec'], true);

            return true;
        }

        return false;
    }

    /**
     * Createa a list of facts that can be pasted into a given record
     *
     * @param GedcomRecord $record
     *
     * @return Fact[]
     */
    public function pastableFacts(GedcomRecord $record): array
    {
        // The facts are stored in the session.
        $clipboard = Session::get('clipboard', []);

        // Put the most recently copied fact at the top of the list.
        $clipboard = array_reverse($clipboard[$record::RECORD_TYPE] ?? []);

        // Create facts for the record.
        $facts = array_map(function (array $clipping) use ($record): Fact {
            return new Fact($clipping['factrec'], $record, md5($clipping['factrec']));
        }, $clipboard);

        return $facts;
    }
}
