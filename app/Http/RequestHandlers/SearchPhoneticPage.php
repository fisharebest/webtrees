<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use function assert;

/**
 * Search for (and optionally replace) genealogy data
 */
class SearchPhoneticPage implements RequestHandlerInterface
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
     * The phonetic search.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params    = $request->getQueryParams();
        $firstname = $params['firstname'] ?? '';
        $lastname  = $params['lastname'] ?? '';
        $place     = $params['place'] ?? '';
        $soundex   = $params['soundex'] ?? 'Russell';

        // What trees to search?
        if (Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1') {
            $all_trees = $this->tree_service->all();
        } else {
            $all_trees = new Collection([$tree]);
        }

        $search_tree_names = new Collection($params['search_trees'] ?? []);

        $search_trees = $all_trees
            ->filter(static function (Tree $tree) use ($search_tree_names): bool {
                return $search_tree_names->containsStrict($tree->name());
            });

        if ($search_trees->isEmpty()) {
            $search_trees->add($tree);
        }

        $individuals = new Collection();

        if ($lastname !== '' || $firstname !== '' || $place !== '') {
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
