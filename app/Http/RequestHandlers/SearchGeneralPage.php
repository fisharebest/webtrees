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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;
use function preg_replace;
use function redirect;
use function trim;

use const PREG_SET_ORDER;

/**
 * Search for genealogy data
 */
class SearchGeneralPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private SearchService $search_service;

    private TreeService $tree_service;

    /**
     * @param SearchService $search_service
     * @param TreeService   $tree_service
     */
    public function __construct(SearchService $search_service, TreeService $tree_service)
    {
        $this->search_service = $search_service;
        $this->tree_service   = $tree_service;
    }

    /**
     * The standard search.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        $query = Validator::queryParams($request)->string('query', '');

        // What type of records to search?
        $search_individuals  = Validator::queryParams($request)->boolean('search_individuals', false);
        $search_families     = Validator::queryParams($request)->boolean('search_families', false);
        $search_locations    = Validator::queryParams($request)->boolean('search_locations', false);
        $search_repositories = Validator::queryParams($request)->boolean('search_repositories', false);
        $search_sources      = Validator::queryParams($request)->boolean('search_sources', false);
        $search_notes        = Validator::queryParams($request)->boolean('search_notes', false);

        // Where to search
        $search_tree_names = Validator::queryParams($request)->array('search_trees');

        $exist_notes = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->exists();

        $exist_locations = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->exists();

        $exist_repositories = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->exists();

        $exist_sources = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->exists();

        // Default to families and individuals only
        if (!$search_individuals && !$search_families && !$search_repositories && !$search_sources && !$search_notes) {
            $search_families    = true;
            $search_individuals = true;
        }

        // What to search for?
        $search_terms = $this->extractSearchTerms($query);

        // What trees to search?
        if (Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1') {
            $all_trees = $this->tree_service->all();
        } else {
            $all_trees = new Collection([$tree]);
        }

        $search_trees = $all_trees
            ->filter(static fn (Tree $tree): bool => in_array($tree->name(), $search_tree_names, true));

        if ($search_trees->isEmpty()) {
            $search_trees->add($tree);
        }

        // Do the search
        $individuals  = new Collection();
        $families     = new Collection();
        $locations    = new Collection();
        $repositories = new Collection();
        $sources      = new Collection();
        $notes        = new Collection();

        if ($search_terms !== []) {
            // Log search requests for visitors
            if (Auth::id() === null) {
                Log::addSearchLog('General: ' . $query, $search_trees->all());
            }

            if ($search_individuals) {
                $individuals = $this->search_service->searchIndividuals($search_trees->all(), $search_terms);
            }

            if ($search_families) {
                $tmp1 = $this->search_service->searchFamilies($search_trees->all(), $search_terms);
                $tmp2 = $this->search_service->searchFamilyNames($search_trees->all(), $search_terms);

                $families = $tmp1->merge($tmp2)->unique(static function (Family $family): string {
                    return $family->xref() . '@' . $family->tree()->id();
                });
            }

            if ($search_repositories) {
                $repositories = $this->search_service->searchRepositories($search_trees->all(), $search_terms);
            }

            if ($search_sources) {
                $sources = $this->search_service->searchSources($search_trees->all(), $search_terms);
            }

            if ($search_notes) {
                $notes = $this->search_service->searchNotes($search_trees->all(), $search_terms);
            }

            if ($search_locations) {
                $locations = $this->search_service->searchLocations($search_trees->all(), $search_terms);
            }
        }

        // If only 1 item is returned, automatically forward to that item
        if ($individuals->count() === 1 && $families->isEmpty() && $sources->isEmpty() && $notes->isEmpty() && $locations->isEmpty()) {
            return redirect($individuals->first()->url());
        }

        if ($individuals->isEmpty() && $families->count() === 1 && $sources->isEmpty() && $notes->isEmpty() && $locations->isEmpty()) {
            return redirect($families->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->count() === 1 && $notes->isEmpty() && $locations->isEmpty()) {
            return redirect($sources->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->isEmpty() && $notes->count() === 1 && $locations->isEmpty()) {
            return redirect($notes->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->isEmpty() && $notes->isEmpty() && $locations->count() === 1) {
            return redirect($locations->first()->url());
        }

        $title = I18N::translate('General search');

        return $this->viewResponse('search-general-page', [
            'all_trees'           => $all_trees,
            'exist_locations'     => $exist_locations,
            'exist_notes'         => $exist_notes,
            'exist_repositories'  => $exist_repositories,
            'exist_sources'       => $exist_sources,
            'families'            => $families,
            'individuals'         => $individuals,
            'locations'           => $locations,
            'notes'               => $notes,
            'query'               => $query,
            'repositories'        => $repositories,
            'search_families'     => $search_families,
            'search_individuals'  => $search_individuals,
            'search_locations'    => $search_locations,
            'search_notes'        => $search_notes,
            'search_repositories' => $search_repositories,
            'search_sources'      => $search_sources,
            'search_trees'        => $search_trees,
            'sources'             => $sources,
            'title'               => $title,
            'tree'                => $tree,
        ]);
    }

    /**
     * Convert the query into an array of search terms
     *
     * @param string $query
     *
     * @return array<string>
     */
    private function extractSearchTerms(string $query): array
    {
        $search_terms = [];

        // Words in double quotes stay together
        preg_match_all('/"([^"]+)"/', $query, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $search_terms[] = trim($match[1]);
            // Remove this string from the search query
            $query = strtr($query, [$match[0] => '']);
        }

        // Treat CJK characters as separate words, not as characters.
        $query = preg_replace('/\p{Han}/u', '$0 ', $query);

        // Other words get treated separately
        preg_match_all('/[\S]+/', $query, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $search_terms[] = $match[0];
        }

        return $search_terms;
    }
}
