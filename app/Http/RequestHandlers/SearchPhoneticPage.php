<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

/**
 * Search for (and optionally replace) genealogy data
 */
class SearchPhoneticPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private SearchService $search_service;

    private TreeService $tree_service;

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
     * The phonetic search.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $firstname = Validator::queryParams($request)->string('firstname', '');
        $lastname  = Validator::queryParams($request)->string('lastname', '');
        $place     = Validator::queryParams($request)->string('place', '');
        $soundex   = Validator::queryParams($request)->isInArray(['DaitchM', 'Russell'])->string('soundex', 'Russell');

        // Where to search
        $search_tree_names = Validator::queryParams($request)->array('search_trees');

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

        $individuals = new Collection();

        if ($lastname !== '' || $firstname !== '' || $place !== '') {
            // Log search requests for visitors
            if (Auth::id() === null) {
                $message = 'Phonetic: first=' . $firstname . ', last=' . $lastname . ', place=' . $place;
                Log::addSearchLog($message, $search_trees->all());
            }

            $individuals = $this->search_service->searchIndividualsPhonetic($soundex, $lastname, $firstname, $place, $search_trees->all());
        }

        $title = I18N::translate('Phonetic search');

        return $this->viewResponse('search-phonetic-page', [
            'all_trees'    => $all_trees,
            'firstname'    => $firstname,
            'individuals'  => $individuals,
            'lastname'     => $lastname,
            'place'        => $place,
            'search_trees' => $search_trees,
            'soundex'      => $soundex,
            'title'        => $title,
            'tree'         => $tree,
        ]);
    }
}
