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

    /**
     * Copy a fact to the clipboard.
     *
     * @param Fact $fact
     */
    public function copyFact(Fact $fact): void
    {
        $clipboard = Session::get('clipboard', []);

        switch ($fact->getTag()) {
            case 'NOTE':
            case 'SOUR':
            case 'OBJE':
                // paste this anywhere
                $type = 'all';
                break;
            default:
                // paste only to the same record type
                $type = $fact->record()::RECORD_TYPE;
                break;
        }

        // If we are copying the same fact twice, make sure the new one is at the top.
        $fact_id = $fact->id();

        unset($clipboard[$fact_id]);

        $clipboard[$fact_id] = [
            'type'    => $type,
            'factrec' => $fact->gedcom(),
            'fact'    => $fact->getTag(),
        ];

        // The clipboard only holds a limited number of facts.
        $clipboard = array_slice($clipboard, -self::CLIPBOARD_SIZE);

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

        if (isset($clipboard[$fact_id])) {
            $record->createFact($clipboard[$fact_id]['factrec'], true);
            return true;
        }

        return false;
    }

    /**
     * Createa a list of facts that can be pasted into a given record
     *
     * @param GedcomRecord $gedcom_record
     *
     * @return Fact[]
     */
    public function pastableFacts(GedcomRecord $gedcom_record): array
    {
        // The facts are stored in the session.
        $clipboard = Session::get('clipboard', []);

        // Put the most recently copied fact at the top of the list.
        $clipboard = array_reverse($clipboard);

        // Only include facts that can be pasted onto this record.
        $clipboard = array_filter($clipboard, function (array $clipping) use ($gedcom_record): bool {
            return $clipping['type'] == $gedcom_record::RECORD_TYPE || $clipping['type'] == 'all';
        });

        // Create facts for the record.
        $facts = array_map(function (array $clipping) use ($gedcom_record): Fact {
            return new Fact($clipping['factrec'], $gedcom_record, md5($clipping['factrec']));
        }, $clipboard);

        return $facts;
    }
}
