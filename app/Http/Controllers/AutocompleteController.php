<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_array;
use function json_decode;
use function preg_match_all;
use function preg_quote;
use function rawurlencode;
use function response;
use function str_contains;

/**
 * Controller for the autocomplete callbacks
 */
class AutocompleteController extends AbstractBaseController
{
    // Options for fetching files using GuzzleHTTP
    private const GUZZLE_OPTIONS = [
        'connect_timeout' => 3,
        'read_timeout'    => 3,
        'timeout'         => 3,
    ];

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

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $query = $request->getQueryParams()['query'] ?? '';

        $media_filesystem = $tree->mediaFilesystem($data_filesystem);

        $contents = new Collection($media_filesystem->listContents('', true));

        $folders = $contents
            ->filter(static function (array $object) use ($query): bool {
                return $object['type'] === 'dir' && str_contains($object['path'], $query);
            })
            ->values()
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

        $source = Factory::source()->make($xref, $tree);
        $source = Auth::checkSourceAccess($source);

        $regex_query = preg_quote(strtr($query, [' ' => '.+']), '/');

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
            ->map(Factory::individual()->mapper($tree))
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
            ->map(Factory::family()->mapper($tree))
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

        $pages = $pages
            ->uniqueStrict()
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

        $query = $request->getQueryParams()['query'] ?? '';
        $data  = [];

        foreach ($this->search_service->searchPlaces($tree, $query) as $place) {
            $data[] = ['value' => $place->gedcomName()];
        }

        $geonames = Site::getPreference('geonames');

        if ($data === [] && $geonames !== '') {
            // No place found? Use an external gazetteer
            $url =
                'https://secure.geonames.org/searchJSON' .
                '?name_startsWith=' . rawurlencode($query) .
                '&lang=' . I18N::languageTag() .
                '&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC' .
                '&style=full' .
                '&username=' . rawurlencode($geonames);

            // Read from the URL
            $client = new Client();
            try {
                $json = $client->get($url, self::GUZZLE_OPTIONS)->getBody()->__toString();
                $places = json_decode($json, true);
                if (isset($places['geonames']) && is_array($places['geonames'])) {
                    foreach ($places['geonames'] as $k => $place) {
                        $data[] = ['value' => $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName']];
                    }
                }
            } catch (RequestException $ex) {
                // Service down?  Quota exceeded?
            }
        }

        return response($data);
    }
}
