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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function json_decode;
use function preg_match_all;
use function preg_quote;
use function rawurlencode;
use function urlencode;
use function response;

/**
 * Controller for the autocomplete callbacks
 */
class AutocompleteController extends AbstractBaseController
{

    // Gazetteer urls
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    private const OPENCAGE_URL  = 'https://api.opencagedata.com/geocode/v1/json';

    // Options for fetching files using GuzzleHTTP
    private const GUZZLE_OPTIONS = [
        'connect_timeout' => 3,
        'read_timeout'    => 3,
        'timeout'         => 3,
    ];

    /** @var SearchService */
    private $search_service;
    /** @var UserService */
    private $user_service;

    /**
     * AutocompleteController constructor.
     *
     * @param SearchService $search_service
     * @param UserService $user_service
     */
    public function __construct(SearchService $search_service, UserService $user_service)
    {
        $this->search_service = $search_service;
        $this->user_service   = $user_service;
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
                return $object['type'] === 'dir' && Str::contains($object['path'], $query);
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

        $source = Source::getInstance($xref, $tree);
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
            ->map(Individual::rowMapper($tree))
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
            ->map(Family::rowMapper($tree))
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

        if ($data === [] && (bool) Site::getPreference('use_gazetteer')) {
            // No place found? Use an external gazetteer
            $data = $this->searchGazetteer($query, 5);
        }

        return response($data);
    }

    /**
     *
     * @param string $query
     * @param int $limit
     *
     * @return mixed[]
     */
    private function searchGazetteer($query, $limit = 10): array
    {
        $key          = Site::getPreference('opencage');
        $use_opencage = $key !== '';
        $data         = [];

        if ($use_opencage) {
            $url   = self::OPENCAGE_URL;
            $query = [
                'q'              => urlencode($query), // don't use rawurlencode - it breaks things
                'format'         => 'json',
                'limit'          => $limit,
                'language'       => I18N::languageTag(),
                'key'            => $key,
                'no_annotations' => 1,
                // 'min_confidence' => 4,
            ];
        } else {
            $url   = self::NOMINATIM_URL;
            $query = [
                'q'               => rawurlencode($query),
                'format'          => 'jsonv2',
                'limit'           => $limit,
                'accept-language' => I18N::languageTag(),
                'featuretype'     => 'settlement',
                'email'           => rawurlencode($this->user_service->administrators()->first()->email()),
            ];
        }
        // Read from the URL
        $client = new Client();
        try {
            $json     = $client->get($url, self::GUZZLE_OPTIONS + ['query' => $query])->getBody()->__toString();
            $results  = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
            if ($use_opencage) {
                $results = $results->results;
            }
            foreach ($results as $result) {
                $data[] = [
                    'value' => isset($result->display_name) ? $result->display_name : $result->formatted
                ];
            }
        } catch (JsonException | RequestException $ex) {
            // Json error? Service down?  Quota exceeded?
        }

        return $data;
    }
}
