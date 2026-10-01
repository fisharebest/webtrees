<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\Value;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function preg_match_all;
use function preg_quote;

final class AutoCompleteCitation extends AbstractAutocompleteHandler
{
    /**
     * @return Collection<int,string>
     */
    protected function search(Tree $tree, string $query, string $extra): Collection
    {
        $source = Registry::sourceFactory()->make($extra, $tree);
        $source = Auth::checkSourceAccess($source);

        $regex_query = strtr(preg_quote($query, '/'), [' ' => '.+']);

        // Fetch all records with a link to this source
        $individuals = DB::query()
            ->select('individuals.*')
            ->distinct()
            ->from('individuals')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'i_file')->on('l_from', 'i_id'))
            ->whereEqual('i_file', $tree->id())
            ->whereEqual('l_to', $source->xref())
            ->whereEqual('l_type', 'SOUR')
            ->get(in: Collection::class)
            ->map(Registry::individualFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());

        $families = DB::query()
            ->select('families.*')
            ->distinct()
            ->from('families')
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('l_file', 'f_file')->on('l_from', 'f_id')->on('l_type', DB::value('SOUR')))
            ->whereEqual('f_file', $tree->id())
            ->whereEqual('l_to', $source->xref())
            ->whereEqual('l_type', 'SOUR')
            ->get(in: Collection::class)
            ->map(Registry::familyFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());

        $pages = new Collection();

        foreach ($individuals->merge($families) as $record) {
            if (preg_match_all('/\n1 SOUR @' . $source->xref() . '@(?:\n[2-9].*)*\n2 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = $pages->concat($matches[1]);
            }

            if (preg_match_all('/\n2 SOUR @' . $source->xref() . '@(?:\n[3-9].*)*\n3 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = $pages->concat($matches[1]);
            }
        }

        return $pages->uniqueStrict();
    }
}
