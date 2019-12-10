<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function view;

/**
 * Autocomplete for notes.
 */
class Select2Note extends AbstractSelect2Handler
{
    /**
     * Perform the search
     *
     * @param Tree   $tree
     * @param string $query
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection<array<string,string>>
     */
    protected function search(Tree $tree, string $query, int $offset, int $limit): Collection
    {
        // Search by XREF
        $note = Note::getInstance($query, $tree);

        if ($note instanceof Note) {
            $results = new Collection([$note]);
        } else {
            $results = $this->search_service->searchNotes([$tree], [$query], $offset, $limit);
        }

        return $results->map(static function (Note $note): array {
            return [
                'id'    => $note->xref(),
                'text'  => view('selects/note', ['note' => $note]),
                'title' => ' ',
            ];
        });
    }
}
