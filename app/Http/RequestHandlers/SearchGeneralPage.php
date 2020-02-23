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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function assert;
use function in_array;
use function preg_match;
use function preg_replace;
use function redirect;
use function str_replace;
use function trim;

/**
 * Search for genealogy data
 */
class SearchGeneralPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var SearchService */
    private $search_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * SearchController constructor.
     *
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = $request->getQueryParams();
        $query  = $params['query'] ?? '';

        // What type of records to search?
        $search_individuals  = (bool) ($params['search_individuals'] ?? false);
        $search_families     = (bool) ($params['search_families'] ?? false);
        $search_repositories = (bool) ($params['search_repositories'] ?? false);
        $search_sources      = (bool) ($params['search_sources'] ?? false);
        $search_notes        = (bool) ($params['search_notes'] ?? false);

        // Default to families and individuals only
        if (!$search_individuals && !$search_families && !$search_repositories && !$search_sources && !$search_notes) {
            $search_families    = true;
            $search_individuals = true;
        }

        // What to search for?
        $search_terms = $this->extractSearchTerms($query);

        // What trees to seach?
        if (Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1') {
            $all_trees = $this->tree_service->all()->all();
        } else {
            $all_trees = [$tree];
        }

        $search_tree_names = $params['search_trees'] ?? [];

        $search_trees = array_filter($all_trees, static function (Tree $tree) use ($search_tree_names): bool {
            return in_array($tree->name(), $search_tree_names, true);
        });

        if ($search_trees === []) {
            $search_trees = [$tree];
        }

        // Do the search
        $individuals  = new Collection();
        $families     = new Collection();
        $repositories = new Collection();
        $sources      = new Collection();
        $notes        = new Collection();

        if ($search_terms !== []) {
            if ($search_individuals) {
                $individuals = $this->search_service->searchIndividuals($search_trees, $search_terms);
            }

            if ($search_families) {
                $tmp1 = $this->search_service->searchFamilies($search_trees, $search_terms);
                $tmp2 = $this->search_service->searchFamilyNames($search_trees, $search_terms);

                $families = $tmp1->merge($tmp2)->unique(static function (Family $family): string {
                    return $family->xref() . '@' . $family->tree()->id();
                });
            }

            if ($search_repositories) {
                $repositories = $this->search_service->searchRepositories($search_trees, $search_terms);
            }

            if ($search_sources) {
                $sources = $this->search_service->searchSources($search_trees, $search_terms);
            }

            if ($search_notes) {
                $notes = $this->search_service->searchNotes($search_trees, $search_terms);
            }
        }

        // If only 1 item is returned, automatically forward to that item
        if ($individuals->count() === 1 && $families->isEmpty() && $sources->isEmpty() && $notes->isEmpty()) {
            return redirect($individuals->first()->url());
        }

        if ($individuals->isEmpty() && $families->count() === 1 && $sources->isEmpty() && $notes->isEmpty()) {
            return redirect($families->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->count() === 1 && $notes->isEmpty()) {
            return redirect($sources->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->isEmpty() && $notes->count() === 1) {
            return redirect($notes->first()->url());
        }

        $title = I18N::translate('General search');

        return $this->viewResponse('search-general-page', [
            'all_trees'           => $all_trees,
            'families'            => $families,
            'individuals'         => $individuals,
            'notes'               => $notes,
            'query'               => $query,
            'repositories'        => $repositories,
            'search_families'     => $search_families,
            'search_individuals'  => $search_individuals,
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
     * @return string[]
     */
    private function extractSearchTerms(string $query): array
    {
        $search_terms = [];

        // Words in double quotes stay together
        while (preg_match('/"([^"]+)"/', $query, $match)) {
            $search_terms[] = trim($match[1]);
            $query          = str_replace($match[0], '', $query);
        }

        // Treat CJK characters as separate words, not as characters.
        $query = preg_replace('/\p{Han}/u', '$0 ', $query);

        // Other words get treated separately
        while (preg_match('/[\S]+/', $query, $match)) {
            $search_terms[] = trim($match[0]);
            $query          = str_replace($match[0], '', $query);
        }

        return $search_terms;
    }
}
