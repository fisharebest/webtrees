<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

use function preg_match_all;
use function preg_quote;

/**
 * Autocomplete handler for source citations
 */
class AutoCompleteCitation extends AbstractAutocompleteHandler
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return Collection<int,string>
     */
    protected function search(ServerRequestInterface $request): Collection
    {
        $tree   = Validator::attributes($request)->tree();
        $query  = Validator::queryParams($request)->string('query');
        $xref   = Validator::queryParams($request)->string('extra', '');
        $source = Registry::sourceFactory()->make($xref, $tree);
        $source = Auth::checkSourceAccess($source);

        $regex_query = strtr(preg_quote($query, '/'), [' ' => '.+']);

        // Fetch all records with a link to this source
        $individuals = DB::table('individuals')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'i_file')
                    ->on('l_from', '=', 'i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('l_to', '=', $source->xref())
            ->where('l_type', '=', 'SOUR')
            ->distinct()
            ->select(['individuals.*'])
            ->get()
            ->map(Registry::individualFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());

        $families = DB::table('families')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'f_file')
                    ->on('l_from', '=', 'f_id')
                    ->where('l_type', '=', 'SOUR');
            })
            ->where('f_file', '=', $tree->id())
            ->where('l_to', '=', $source->xref())
            ->where('l_type', '=', 'SOUR')
            ->distinct()
            ->select(['families.*'])
            ->get()
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
