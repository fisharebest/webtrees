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

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function file_get_contents;
use function function_exists;
use function ini_get;
use function is_array;
use function json_decode;
use function preg_match_all;
use function preg_quote;
use function rawurlencode;
use function response;
use function view;

use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function folder(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $query = $request->getQueryParams()['query'] ?? '';

        $media_filesystem = $tree->mediaFilesystem();

        $contents = new Collection($media_filesystem->listContents('', true));

        $folders = $contents
            ->filter(static function (array $object) use ($query): bool {
                return $object['type'] === 'dir' && Str::contains($object['path'], $query);
            })
            ->map(static function (array $object): array {
                return ['value' => $object['path']];
            });

        return response($folders);
    }

    /**
     * Autocomplete for source citations.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function page(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $query = $request->getQueryParams()['query'] ?? '';
        $xref  = $request->getQueryParams()['extra'] ?? '';

        $source = Source::getInstance($xref, $tree);

        Auth::checkSourceAccess($source);

        $regex_query = preg_quote(strtr($query, [' ' => '.+']), '/');

        // Fetch all records with a link to this source
        $individuals = DB::table('individuals')
            ->join('link', static function (JoinClause $join): void {
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
            ->join('link', static function (JoinClause $join): void {
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

        $pages = new Collection();

        foreach ($individuals->merge($families) as $record) {
            if (preg_match_all('/\n1 SOUR @' . $xref . '@(?:\n[2-9].*)*\n2 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = $pages->concat($matches[1]);
            }

            if (preg_match_all('/\n2 SOUR @' . $xref . '@(?:\n[3-9].*)*\n3 PAGE (.*' . $regex_query . '.*)/i', $record->gedcom(), $matches)) {
                $pages = $pages->concat($matches[1]);
            }
        }

        $pages = $pages
            ->unique()
            ->map(static function (string $page): array {
                return ['value' => $page];
            })
            ->all();

        return response($pages);
    }

    /**
     * /**
     * Autocomplete for place names.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function place(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $locale = $request->getAttribute('locale');
        assert($locale instanceof LocaleInterface);

        $query = $request->getQueryParams()['query'] ?? '';
        $data  = [];

        foreach ($this->search_service->searchPlaces($tree, $query) as $place) {
            $data[] = ['value' => $place->gedcomName()];
        }

        $geonames = Site::getPreference('geonames');

        if ($data === [] && $geonames !== '') {
            // No place found? Use an external gazetteer
            $url =
                'http://api.geonames.org/searchJSON' .
                '?name_startsWith=' . rawurlencode($query) .
                '&lang=' . $locale->languageTag() .
                '&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC' .
                '&style=full' .
                '&username=' . rawurlencode($geonames);

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
                return response([]);
            }

            $places = json_decode($json, true);
            if (isset($places['geonames']) && is_array($places['geonames'])) {
                foreach ($places['geonames'] as $k => $place) {
                    $data[] = ['value' => $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName']];
                }
            }
        }

        return response($data);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Family(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        // Allow private records to be selected?
        $filter = $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1' ? null : GedcomRecord::accessFilter();

        $results = $this->search_service
            ->searchFamilyNames([$tree], [$query], $offset, $limit)
            ->prepend(Family::getInstance($query, $tree))
            ->filter($filter)
            ->map(static function (Family $family): array {
                return [
                    'id'    => $family->xref(),
                    'text'  => view('selects/family', ['family' => $family]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Individual(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset  = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit   = self::RESULTS_PER_PAGE + 1;
        $results = $this->search_service->searchIndividualNames([$tree], [$query], $offset, $limit);

        $record = Individual::getInstance($query, $tree);
        if ($record instanceof Individual && ($record->canShow() || $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1')) {
            $results->prepend($record);
        }

        $results = $results
            ->map(static function (Individual $individual): array {
                return [
                    'id'    => $individual->xref(),
                    'text'  => view('selects/individual', ['individual' => $individual]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2MediaObject(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset  = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit   = self::RESULTS_PER_PAGE + 1;
        $results = $this->search_service->searchMedia([$tree], [$query], $offset, $limit);

        $record = Family::getInstance($query, $tree);
        if ($record instanceof Family && ($record->canShow() || $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1')) {
            $results->prepend($record);
        }

        $results = $results
            ->map(static function (Media $media): array {
                return [
                    'id'    => $media->xref(),
                    'text'  => view('selects/media', ['media' => $media]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Note(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchNotes([$tree], [$query], $offset, $limit)
            ->map(static function (Note $note): array {
                return [
                    'id'    => $note->xref(),
                    'text'  => view('selects/note', ['note' => $note]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Place(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service->searchPlaces($tree, $query, $offset, $limit)
            ->map(static function (Place $place): array {
                return [
                    'id'    => $place->gedcomName(),
                    'text'  => $place->gedcomName(),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Repository(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchRepositories([$tree], [$query], $offset, $limit)
            ->map(static function (Repository $repository): array {
                return [
                    'id'    => $repository->xref(),
                    'text'  => view('selects/repository', ['repository' => $repository]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Source(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchSourcesByName([$tree], [$query], $offset, $limit)
            ->map(static function (Source $source): array {
                return [
                    'id'    => $source->xref(),
                    'text'  => view('selects/source', ['source' => $source]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function select2Submitter(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $page  = (int) ($request->getParsedBody()['page'] ?? 1);
        $query = $request->getParsedBody()['q'] ?? '';

        assert($tree instanceof Tree);
        assert($query !== '');

        // Fetch one more row than we need, so we can know if more rows exist.
        $offset = ($page - 1) * self::RESULTS_PER_PAGE;
        $limit  = self::RESULTS_PER_PAGE + 1;

        $results = $this->search_service
            ->searchSubmitters([$tree], [$query], $offset, $limit)
            ->map(static function (GedcomRecord $submitter): array {
                return [
                    'id'    => $submitter->xref(),
                    'text'  => view('selects/submitter', ['submitter' => $submitter]),
                    'title' => ' ',
                ];
            });

        return response([
            'results'    => $results->slice(0, self::RESULTS_PER_PAGE)->all(),
            'pagination' => [
                'more' => $results->count() > self::RESULTS_PER_PAGE,
            ],
        ]);
    }
}
