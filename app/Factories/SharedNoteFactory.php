<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Closure;
use Fisharebest\Webtrees\Contracts\SharedNoteFactoryInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\SharedNote;
use Fisharebest\Webtrees\Tree;

use function preg_match;

/**
 * Make a SharedNote object.
 */
class SharedNoteFactory extends AbstractGedcomRecordFactory implements SharedNoteFactoryInterface
{
    private const TYPE_CHECK_REGEX = '/^0 @[^@]+@ ' . SharedNote::RECORD_TYPE . '/';

    /**
     * Create a note.
     */
    public function make(string $xref, Tree $tree, string|null $gedcom = null): SharedNote|null
    {
        return Registry::cache()->array()->remember(self::class . $xref . '@' . $tree->id(), function () use ($xref, $tree, $gedcom) {
            $gedcom ??= $this->gedcom($xref, $tree);
            $pending = $this->pendingChanges($tree)->get($xref);

            if ($gedcom === null && ($pending === null || !preg_match(self::TYPE_CHECK_REGEX, $pending))) {
                return null;
            }

            $xref = $this->extractXref($gedcom ?? $pending, $xref);

            return new SharedNote($xref, $gedcom ?? '', $pending, $tree);
        });
    }

    /**
     * Create a note from a row in the database.
     *
     * @param Tree $tree
     *
     * @return Closure(object):SharedNote
     */
    public function mapper(Tree $tree): Closure
    {
        return fn (object $row): SharedNote => $this->make($row->o_id, $tree, $row->o_gedcom);
    }

    /**
     * Create a note from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     *
     * @return SharedNote
     */
    public function new(string $xref, string $gedcom, string|null $pending, Tree $tree): SharedNote
    {
        return new SharedNote($xref, $gedcom, $pending, $tree);
    }

    /**
     * Fetch GEDCOM data from the database.
     *
     * @param string $xref
     * @param Tree   $tree
     *
     * @return string|null
     */
    protected function gedcom(string $xref, Tree $tree): string|null
    {
        return DB::table('other')
            ->where('o_id', '=', $xref)
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', SharedNote::RECORD_TYPE)
            ->value('o_gedcom');
    }
}
