<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function date;
use function redirect;
use function response;
use function route;
use function view;

class SiteMapModule extends AbstractModule implements ModuleConfigInterface, RequestHandlerInterface
{
    use ModuleConfigTrait;

    private const int RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
    private const int CACHE_LIFE         = 209600; // Two weeks

    private const array PRIORITY = [
        Family::RECORD_TYPE     => 0.7,
        Individual::RECORD_TYPE => 0.9,
        Media::RECORD_TYPE      => 0.5,
        Note::RECORD_TYPE       => 0.3,
        Repository::RECORD_TYPE => 0.5,
        Source::RECORD_TYPE     => 0.5,
        Submitter::RECORD_TYPE  => 0.3,
    ];

    private TreeService $tree_service;

    /**
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
        Registry::routeFactory()->routeMap()
            ->get('sitemap-style', '/sitemap.xsl', $this);

        Registry::routeFactory()->routeMap()
            ->get('sitemap-index', '/sitemap.xml', $this);

        Registry::routeFactory()->routeMap()
            ->get('sitemap-file', '/sitemap-{tree}-{type}-{page}.xml', $this);
    }

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

        return $this->viewResponse('modules/sitemap/config', [
            'all_trees'   => $this->tree_service->all(),
            'sitemap_url' => $sitemap_url,
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
        /* I18N: Name of a module - see https://en.wikipedia.org/wiki/Sitemaps */
        return I18N::translate('Sitemaps');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->tree_service->all() as $tree) {
            $include_in_sitemap = Validator::parsedBody($request)->boolean('sitemap' . $tree->id(), false);
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
        $route = Validator::attributes($request)->route();

        if ($route->name === 'sitemap-style') {
            $content = view('modules/sitemap/sitemap-xsl');

            return response($content, StatusCodeInterface::STATUS_OK, [
                'content-type' => 'application/xml',
            ]);
        }

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
        $content = Registry::cache()->file()->remember('sitemap.xml', function (): string {
            // Which trees have sitemaps enabled?
            $tree_ids = $this->tree_service->all()
                ->filter(static fn (Tree $tree): bool => $tree->getPreference('include_in_sitemap') === '1')
                ->map(static fn (Tree $tree): int => $tree->id());

            $count_families = DB::table('families')
                ->join('gedcom', 'f_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_individuals = DB::table('individuals')
                ->join('gedcom', 'i_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_media = DB::table('media')
                ->join('gedcom', 'm_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_notes = DB::table('other')
                ->join('gedcom', 'o_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->where('o_type', '=', Note::RECORD_TYPE)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_repositories = DB::table('other')
                ->join('gedcom', 'o_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->where('o_type', '=', Repository::RECORD_TYPE)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_sources = DB::table('sources')
                ->join('gedcom', 's_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            $count_submitters = DB::table('other')
                ->join('gedcom', 'o_file', '=', 'gedcom_id')
                ->whereIn('gedcom_id', $tree_ids)
                ->where('o_type', '=', Submitter::RECORD_TYPE)
                ->groupBy(['gedcom_id'])
                ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

            // Versions 2.0.1 and earlier of this module stored large amounts of data in the settings.
            DB::table('module_setting')
                ->where('module_name', '=', $this->name())
                ->delete();

            return view('modules/sitemap/sitemap-index-xml', [
                'all_trees'          => $this->tree_service->all(),
                'count_families'     => $count_families,
                'count_individuals'  => $count_individuals,
                'count_media'        => $count_media,
                'count_notes'        => $count_notes,
                'count_repositories' => $count_repositories,
                'count_sources'      => $count_sources,
                'count_submitters'   => $count_submitters,
                'last_mod'           => date('Y-m-d'),
                'records_per_volume' => self::RECORDS_PER_VOLUME,
                'sitemap_xsl'        => route('sitemap-style'),
            ]);
        }, self::CACHE_LIFE);

        return response($content, StatusCodeInterface::STATUS_OK, [
            'content-type' => 'application/xml',
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    private function siteMapFile(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree('tree');
        $type = Validator::attributes($request)->string('type');
        $page = Validator::attributes($request)->integer('page');

        if ($tree->getPreference('include_in_sitemap') !== '1') {
            throw new HttpNotFoundException();
        }

        $cache_key = 'sitemap/' . $tree->id() . '/' . $type . '/' . $page . '.xml';

        $content = Registry::cache()->file()->remember($cache_key, function () use ($tree, $type, $page): string {
            $records = $this->sitemapRecords($tree, $type, self::RECORDS_PER_VOLUME, self::RECORDS_PER_VOLUME * $page);

            return view('modules/sitemap/sitemap-file-xml', [
                'priority'    => self::PRIORITY[$type],
                'records'     => $records,
                'sitemap_xsl' => route('sitemap-style'),
                'tree'        => $tree,
            ]);
        }, self::CACHE_LIFE);

        return response($content, StatusCodeInterface::STATUS_OK, [
            'content-type' => 'application/xml',
        ]);
    }

    /**
     * @param Tree   $tree
     * @param string $type
     * @param int    $limit
     * @param int    $offset
     *
     * @return Collection<int,GedcomRecord>
     */
    private function sitemapRecords(Tree $tree, string $type, int $limit, int $offset): Collection
    {
        switch ($type) {
            case Family::RECORD_TYPE:
                $records = $this->sitemapFamilies($tree, $limit, $offset);
                break;

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

            case Submitter::RECORD_TYPE:
                $records = $this->sitemapSubmitters($tree, $limit, $offset);
                break;

            default:
                throw new HttpNotFoundException('Invalid record type: ' . $type);
        }

        // Skip private records.
        $records = $records->filter(static fn (GedcomRecord $record): bool => $record->canShow(Auth::PRIV_PRIVATE));

        return $records;
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Family>
     */
    private function sitemapFamilies(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->orderBy('f_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::familyFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Individual>
     */
    private function sitemapIndividuals(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->orderBy('i_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::individualFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Media>
     */
    private function sitemapMedia(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->orderBy('m_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::mediaFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Note>
     */
    private function sitemapNotes(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->orderBy('o_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::noteFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Repository>
     */
    private function sitemapRepositories(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->orderBy('o_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::repositoryFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Source>
     */
    private function sitemapSources(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->orderBy('s_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::sourceFactory()->mapper($tree));
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection<int,Submitter>
     */
    private function sitemapSubmitters(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->orderBy('o_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Registry::submitterFactory()->mapper($tree));
    }
}
