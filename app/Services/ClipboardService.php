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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Session;
use Illuminate\Support\Collection;

use function array_slice;
use function explode;
use function is_array;

/**
 * Copy and past facts between records.
 */
class ClipboardService
{
    // Maximum number of entries in the clipboard.
    private const int CLIPBOARD_SIZE = 10;

    /**
     * Copy a fact to the clipboard.
     *
     * @param Fact $fact
     */
    public function copyFact(Fact $fact): void
    {
        $clipboard   = Session::get('clipboard');
        $clipboard   = is_array($clipboard) ? $clipboard : [];
        $record_type = $fact->record()->tag();
        $fact_id     = $fact->id();

        // If we are copying the same fact twice, make sure the new one is at the end.
        unset($clipboard[$record_type][$fact_id]);

        $clipboard[$record_type][$fact_id] = $fact->gedcom();

        // The clipboard only holds a limited number of facts.
        $clipboard[$record_type] = array_slice($clipboard[$record_type], -self::CLIPBOARD_SIZE);

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

        $record_type = $record->tag();

        if (isset($clipboard[$record_type][$fact_id])) {
            $record->createFact($clipboard[$record_type][$fact_id], true);

            return true;
        }

        return false;
    }

    /**
     * Empty the clipboard
     *
     * @return void
     */
    public function emptyClipboard(): void
    {
        Session::put('clipboard', []);
    }

    /**
     * Create a list of facts that can be pasted into a given record
     *
     * @param GedcomRecord $record
     *
     * @return Collection<int,Fact>
     */
    public function pastableFacts(GedcomRecord $record): Collection
    {
        $clipboard = Session::get('clipboard');
        $clipboard = is_array($clipboard) ? $clipboard : [];
        $facts     = $clipboard[$record->tag()] ?? [];

        return (new Collection($facts))
            ->reverse()
            ->map(static fn (string $clipping): Fact => new Fact($clipping, $record, md5($clipping)));
    }

    /**
     * Find facts of a given type, from all records.
     *
     * @param GedcomRecord           $record
     * @param Collection<int,string> $types
     *
     * @return Collection<int,Fact>
     */
    public function pastableFactsOfType(GedcomRecord $record, Collection $types): Collection
    {
        $clipboard = Session::get('clipboard');
        $clipboard = is_array($clipboard) ? $clipboard : [];

        // The facts are stored in the session.
        return (new Collection($clipboard))
            ->flatten(1)
            ->reverse()
            ->map(static fn (string $clipping): Fact => new Fact($clipping, $record, md5($clipping)))
            ->filter(static fn (Fact $fact): bool => $types->contains(explode(':', $fact->tag())[1]));
    }
}
