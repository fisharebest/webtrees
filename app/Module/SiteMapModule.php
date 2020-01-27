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

namespace Fisharebest\Webtrees\Module;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function assert;
use function date;
use function redirect;
use function response;
use function route;
use function view;

/**
 * Class SiteMapModule
 */
class SiteMapModule extends AbstractModule implements ModuleConfigInterface, RequestHandlerInterface
{
    use ModuleConfigTrait;

    private const RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
    private const CACHE_LIFE         = 1;//209600; // Two weeks

    /** @var TreeService */
    private $tree_service;

    /**
     * TreesMenuModule constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        $router_container = app(RouterContainer::class);
        assert($router_container instanceof RouterContainer);

        $router_container->getMap()
            ->get('sitemap-index', '/sitemap.xml', $this);

        $router_container->getMap()
            ->get('sitemap-file', '/sitemap-{tree}-{records}-{page}.xml', $this)
            ->tokens([
                'records' => 'INDI|NOTE|OBJE|REPO|SOUR',
                'page'    => '\d+',
            ]);
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Sitemaps” module */
        return I18N::translate('Generate sitemap files for search engines.');
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $sitemap_url = route('sitemap-index');

        // This list comes from https://en.wikipedia.org/wiki/Sitemaps
        $submit_urls = [
            'Bing/Yahoo' => Html::url('https://www.bing.com/webmaster/ping.aspx', ['siteMap' => $sitemap_url]),
            'Google'     => Html::url('https://www.google.com/webmasters/tools/ping', ['sitemap' => $sitemap_url]),
        ];

        return $this->viewResponse('modules/sitemap/config', [
            'all_trees'   => $this->tree_service->all(),
            'sitemap_url' => $sitemap_url,
            'submit_urls' => $submit_urls,
            'title'       => $this->title(),
        ]);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module - see http://en.wikipedia.org/wiki/Sitemaps */
        return I18N::translate('Sitemaps');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        foreach ($this->tree_service->all() as $tree) {
            $include_in_sitemap = (bool) ($params['sitemap' . $tree->id()] ?? false);
            $tree->setPreference('include_in_sitemap', (string) $include_in_sitemap);
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return redirect($this->getConfigLink());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $request->getAttribute('route');
        assert($route instanceof Route);

        if ($route->name === 'sitemap-index') {
            return $this->siteMapIndex($request);
        }

        return $this->siteMapFile($request);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    private function siteMapIndex(ServerRequestInterface $request): ResponseInterface
    {
        $cache = app('cache.files');
        assert($cache instanceof Cache);

        $content = $cache->remember('sitemap.xml', function (): string {
            // Which trees have sitemaps enabled?
            $tree_ids = $this->tree_service->all()->filter(static function (Tree $tree): bool {
                return $tree->getPreference('include_in_sitemap') === '1';
            })->map(static function (Tree $tree): int {
                return $tree->id();
            });

            $count_individuals = DB::table('individuals')
                ->join('gedcom', 'i_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->select([new Expression('COUNT(*) AS total'), 'gedcom_name'])
                ->pluck('total', 'gedcom_name');

            $count_media = DB::table('media')
                ->join('gedcom', 'm_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->select([new Expression('COUNT(*) AS total'), 'gedcom_name'])
                ->pluck('total', 'gedcom_name');

            $count_notes = DB::table('other')
                ->join('gedcom', 'o_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->where('o_type', '=', 'NOTE')
                ->groupBy(['gedcom_id'])
                ->select([new Expression('COUNT(*) AS total'), 'gedcom_name'])
                ->pluck('total', 'gedcom_name');

            $count_repositories = DB::table('other')
                ->join('gedcom', 'o_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->where('o_type', '=', 'REPO')
                ->groupBy(['gedcom_id'])
                ->select([new Expression('COUNT(*) AS total'), 'gedcom_name'])
                ->pluck('total', 'gedcom_name');

            $count_sources = DB::table('sources')
                ->join('gedcom', 's_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->select([new Expression('COUNT(*) AS total'), 'gedcom_name'])
                ->pluck('total', 'gedcom_name');

            // Versions 2.0.1 and earlier of this module stored large amounts of data in the settings.
            DB::table('module_setting')
                ->where('module_name', '=', $this->name())
                ->delete();

            return view('modules/sitemap/sitemap-index.xml', [
                'all_trees'          => $this->tree_service->all(),
                'count_individuals'  => $count_individuals,
                'count_media'        => $count_media,
                'count_notes'        => $count_notes,
                'count_repositories' => $count_repositories,
                'count_sources'      => $count_sources,
                'last_mod'           => date('Y-m-d'),
                'records_per_volume' => self::RECORDS_PER_VOLUME,
            ]);
        }, self::CACHE_LIFE);

        return response($content, StatusCodeInterface::STATUS_OK, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    private function siteMapFile(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $records = $request->getAttribute('records');
        $page    = $request->getAttribute('page');

        if ($tree->getPreference('include_in_sitemap') !== '1') {
            throw new HttpNotFoundException();
        }

        $cache = app('cache.files');
        assert($cache instanceof Cache);

        $cache_key = 'sitemap/' . $tree->id() . '/' . $records . '/' . $page . '.xml';

        $content = $cache->remember($cache_key, function () use ($tree, $records, $page): string {
            $records = $this->sitemapRecords($tree, $records, self::RECORDS_PER_VOLUME, self::RECORDS_PER_VOLUME * $page);

            return view('modules/sitemap/sitemap-file.xml', [
                'records' => $records,
                'tree'    => $tree,
            ]);
        }, self::CACHE_LIFE);

        return response($content, StatusCodeInterface::STATUS_OK, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * @param Tree   $tree
     * @param string $type
     * @param int    $limit
     * @param int    $offset
     *
     * @return Collection<GedcomRecord>
     */
    private function sitemapRecords(Tree $tree, string $type, int $limit, int $offset): Collection
    {
        switch ($type) {
            case Individual::RECORD_TYPE:
                $records = $this->sitemapIndividuals($tree, $limit, $offset);
                break;

            case Media::RECORD_TYPE:
                $records = $this->sitemapMedia($tree, $limit, $offset);
                break;

            case Note::RECORD_TYPE:
                $records = $this->sitemapNotes($tree, $limit, $offset);
                break;

            case Repository::RECORD_TYPE:
                $records = $this->sitemapRepositories($tree, $limit, $offset);
                break;

            case Source::RECORD_TYPE:
                $records = $this->sitemapSources($tree, $limit, $offset);
                break;

            default:
                throw new HttpNotFoundException('Invalid record type: ' . $type);
        }

        // Skip private records.
        $records = $records->filter(static function (GedcomRecord $record): bool {
            return $record->canShow(Auth::PRIV_PRIVATE);
        });

        return $records;
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<Individual>
     */
    private function sitemapIndividuals(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->orderBy('i_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Individual::rowMapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<Media>
     */
    private function sitemapMedia(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->orderBy('m_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Media::rowMapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<Note>
     */
    private function sitemapNotes(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'NOTE')
            ->orderBy('o_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Note::rowMapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<Repository>
     */
    private function sitemapRepositories(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'REPO')
            ->orderBy('o_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Repository::rowMapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<Source>
     */
    private function sitemapSources(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->orderBy('s_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Source::rowMapper($tree));
    }
}
