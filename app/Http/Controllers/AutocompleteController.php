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

namespace Fisharebest\Webtrees\Http\Controllers;

use FilesystemIterator;
use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Select2;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the autocomplete callbacks
 */
class AutocompleteController extends AbstractBaseController
{
    // For clients that request one page of data at a time.
    private const RESULTS_PER_PAGE = 20;
    
    /** @var SearchService */
    private $search_service;

    /**
     * AutocompleteController constructor.
     *
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    /**
     * Autocomplete for media folders.
     *
     * @param Request    $request
     * @param Tree       $tree
     * @param Filesystem $filesystem
     *
     * @return JsonResponse
     */
    public function folder(Request $request, Tree $tree, Filesystem $filesystem): JsonResponse
    {
        $query = $request->get('query', '');

        $prefix = $tree->getPreference('MEDIA_DIRECTORY', '');

        $media_filesystem = new Filesystem(new ChrootAdapter($filesystem, $prefix));

        $contents = new Collection($media_filesystem->listContents('', true));

        $folders = $contents
            ->filter(function (array $object) use ($query): bool {
                return $object['type'] === 'dir' && Str::contains($object['path'], $query);
            })
            ->map(function (array $object): array {
                return ['value' => $object['path']];
            });

        return new JsonResponse($folders);
    }

    /**
     * Autocomplete for source citations.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function page(Request $request, Tree $tree): JsonResponse
    {
        $query = $request->get('query', '');
        $xref  = $request->get('extra', '');

        $source = Source::getInstance($xref, $tree);

        Auth::checkSourceAccess($source);

        $regex_query = preg_quote(strtr($query, [' ' => '.+']), '/');

        // Fetch all records with a link to this source
        $individuals = DB::table('individuals')
            ->join('link', function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'i_file')
                    ->on('l_from', '=', 'i_id');
            })
            ->where('i_file', '=', $tree->id())
            ->where('l_to', '=', $xref)
            ->where('l_type', '=', 'SOUR')
            ->distinct()
            ->select(['individuals.*'])
            ->get()
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter());

        $families = DB::table('families')
            ->join('link', function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'f_file')
                    ->on('l_from', '=', 'f_id')
                    ->where('l_type', '=', 'SOUR');
            })
            ->where('f_file', '=', $tree->id())
            ->where('l_to', '=', $xref)
            ->where('l_type', '=', 'SOUR')
            ->distinct()
            ->select(['families.*'])
            ->get()
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter());

        $pages = [];

        foreach ($individuals->merge($families) as $record) {
            if (preg_match_all('/\n1 SOUR @' . $xref . '@(?:\n[2-9].*)*\n2 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = array_merge($pages, $matches[1]);
            }

            if (preg_match_all('/\n2 SOUR @' . $xref . '@(?:\n[3-9].*)*\n3 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = array_merge($pages, $matches[1]);
            }
        }

        $pages = array_unique($pages);

        $pages = array_map(function (string $page): array {
            return ['value' => $page];
        }, $pages);

        return new JsonResponse($pages);
    }

    /**
     * /**
     * Autocomplete for place names.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function place(Request $request, Tree $tree): JsonResponse
    {
        $query = $request->get('query', '');
        $data  = [];

        foreach ($this->search_service->searchPlaces($tree, $query) as $place) {
            $data[] = ['value' => $place->gedcomName()];
        }

        if (empty($data) && $tree->getPreference('GEONAMES_ACCOUNT')) {
            // No place found? Use an external gazetteer
            $url =
                "http://api.geonames.org/searchJSON" .
                "?name_startsWith=" . urlencode($query) .
                "&lang=" . WT_LOCALE .
                "&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
                "&style=full" .
                "&username=" . $tree->getPreference('GEONAMES_ACCOUNT');

            // try to use curl when file_get_contents not allowed
            if (ini_get('allow_url_fopen')) {
                $json = file_get_contents($url);
            } elseif (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $json = curl_exec($ch);
                curl_close($ch);
            } else {
                return new JsonResponse([]);
            }

            $places = json_decode($json, true);
            if (isset($places['geonames']) && is_array($places['geonames'])) {
                foreach ($places['geonames'] as $k => $place) {
                    $data[] = ['value' => $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName']];
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Family(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchFamilyNames([$tree], [$query], $offset, $limit)
            ->map(function (Family $family): array {
                return [
                    'id'    => $family->xref(),
                    'text'  => view('selects/family', ['family' => $family]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function select2Flag(Request $request): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        return new JsonResponse(Select2::flagSearch($page, $query));
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Individual(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchIndividualNames([$tree], [$query], $offset, $limit)
            ->map(function (Individual $individual): array {
                return [
                    'id'    => $individual->xref(),
                    'text'  => view('selects/individual', ['individual' => $individual]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2MediaObject(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchMedia([$tree], [$query], $offset, $limit)
            ->map(function (Media $media): array {
                return [
                    'id'    => $media->xref(),
                    'text'  => view('selects/media', ['media' => $media]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Note(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchNotes([$tree], [$query], $offset, $limit)
            ->map(function (Note $note): array {
                return [
                    'id'    => $note->xref(),
                    'text'  => view('selects/note', ['note' => $note]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Place(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        return new JsonResponse($this->placeSearch($tree, $page, $query, true));
    }


    /**
     * Look up a place name.
     *
     * @param Tree   $tree   Search this tree.
     * @param int    $page   Skip this number of pages.  Starts with zero.
     * @param string $query  Search terms.
     * @param bool   $create if true, include the query in the results so it can be created.
     *
     * @return mixed[]
     */
    private function placeSearch(Tree $tree, int $page, string $query, bool $create): array
    {
        $offset  = $page * self::RESULTS_PER_PAGE;
        $results = [];
        $found   = false;

        foreach ($this->search_service->searchPlaces($tree, $query) as $place) {
            $place_name = $place->gedcomName();
            if ($place_name === $query) {
                $found = true;
            }
            $results[] = [
                'id'    => $place_name,
                'text'  => $place_name,
                'title' => ' ',
            ];
        }

        // No place found? Use an external gazetteer
        if (empty($results) && $tree->getPreference('GEONAMES_ACCOUNT')) {
            $url =
                "http://api.geonames.org/searchJSON" .
                "?name_startsWith=" . urlencode($query) .
                "&lang=" . WT_LOCALE .
                "&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
                "&style=full" .
                "&username=" . $tree->getPreference('GEONAMES_ACCOUNT');
            // try to use curl when file_get_contents not allowed
            if (ini_get('allow_url_fopen')) {
                $json   = file_get_contents($url);
                $places = json_decode($json, true);
            } elseif (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $json   = curl_exec($ch);
                $places = json_decode($json, true);
                curl_close($ch);
            } else {
                $places = [];
            }
            if (isset($places['geonames']) && is_array($places['geonames'])) {
                foreach ($places['geonames'] as $k => $place) {
                    $place_name = $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName'];
                    if ($place_name === $query) {
                        $found = true;
                    }
                    $results[] = [
                        'id'    => $place_name,
                        'text'  => $place_name,
                        'title' => ' ',
                    ];
                }
            }
        }

        // Include the query term in the results.  This allows the user to select a
        // place that doesn't already exist in the database.
        if (!$found && $create) {
            array_unshift($results, [
                'id'   => $query,
                'text' => $query,
            ]);
        }

        $more    = count($results) > $offset + self::RESULTS_PER_PAGE;
        $results = array_slice($results, $offset, self::RESULTS_PER_PAGE);

        return [
            'results'    => $results,
            'pagination' => [
                'more' => $more,
            ],
        ];
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Repository(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchRepositories([$tree], [$query], $offset, $limit)
            ->map(function (Repository $repository): array {
                return [
                    'id'    => $repository->xref(),
                    'text'  => view('selects/repository', ['repository' => $repository]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function select2Source(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchSourcesByName([$tree], [$query], $offset, $limit)
            ->map(function (Source $source): array {
                return [
                    'id'    => $source->xref(),
                    'text'  => view('selects/source', ['source' => $source]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param Request       $request
     * @param Tree          $tree
     *
     * @return JsonResponse
     */
    public function select2Submitter(Request $request, Tree $tree): JsonResponse
    {
        $page  = (int) $request->get('page');
        $query = $request->get('q', '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = $page * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchSubmitters([$tree], [$query], $offset, $limit)
            ->map(function (GedcomRecord $submitter): array {
                return [
                    'id'    => $submitter->xref(),
                    'text'  => view('selects/submitter', ['submitter' => $submitter]),
                    'title' => ' ',
                ];
            });

        return new JsonResponse([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }
}
