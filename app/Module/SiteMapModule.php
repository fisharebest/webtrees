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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
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

use function redirect;
use function view;

/**
 * Class SiteMapModule
 */
class SiteMapModule extends AbstractModule implements ModuleConfigInterface
{
    use ModuleConfigTrait;

    private const RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
    private const CACHE_LIFE         = 1209600; // Two weeks

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

        $sitemap_url = route('module', [
            'module' => $this->name(),
            'action' => 'Index',
        ]);

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
    public function getIndexAction(ServerRequestInterface $request): ResponseInterface
    {
        $timestamp = (int) $this->getPreference('sitemap.timestamp');

        // Which trees have sitemaps enabled?
        $tree_ids = $this->tree_service->all()->filter(static function (Tree $tree): bool {
            return $tree->getPreference('include_in_sitemap') === '1';
        })->map(static function (Tree $tree): int {
            return $tree->id();
        });

        if ($timestamp > Carbon::now()->subSeconds(self::CACHE_LIFE)->unix()) {
            $content = $this->getPreference('sitemap.xml');
        } else {
            $count_individuals = DB::table('individuals')
                ->whereIn('i_file', $tree_ids)
                ->groupBy(['i_file'])
                ->select([new Expression('COUNT(*) AS total'), 'i_file'])
                ->pluck('total', 'i_file');

            $count_media = DB::table('media')
                ->whereIn('m_file', $tree_ids)
                ->groupBy(['m_file'])
                ->select([new Expression('COUNT(*) AS total'), 'm_file'])
                ->pluck('total', 'm_file');

            $count_notes = DB::table('other')
                ->whereIn('o_file', $tree_ids)
                ->where('o_type', '=', 'NOTE')
                ->groupBy(['o_file'])
                ->select([new Expression('COUNT(*) AS total'), 'o_file'])
                ->pluck('total', 'o_file');

            $count_repositories = DB::table('other')
                ->whereIn('o_file', $tree_ids)
                ->where('o_type', '=', 'REPO')
                ->groupBy(['o_file'])
                ->select([new Expression('COUNT(*) AS total'), 'o_file'])
                ->pluck('total', 'o_file');

            $count_sources = DB::table('sources')
                ->whereIn('s_file', $tree_ids)
                ->groupBy(['s_file'])
                ->select([new Expression('COUNT(*) AS total'), 's_file'])
                ->pluck('total', 's_file');

            $content = view('modules/sitemap/sitemap-index.xml', [
                'all_trees'          => $this->tree_service->all(),
                'count_individuals'  => $count_individuals,
                'count_media'        => $count_media,
                'count_notes'        => $count_notes,
                'count_repositories' => $count_repositories,
                'count_sources'      => $count_sources,
                'last_mod'           => date('Y-m-d'),
                'records_per_volume' => self::RECORDS_PER_VOLUME,
            ]);

            $this->setPreference('sitemap.xml', $content);
        }

        return response($content, StatusCodeInterface::STATUS_OK, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getFileAction(ServerRequestInterface $request): ResponseInterface
    {
        $file = $request->getQueryParams()['file'];

        if (!preg_match('/^(\d+)-([imnrs])-(\d+)$/', $file, $match)) {
            throw new HttpNotFoundException('Bad sitemap file');
        }

        $timestamp   = (int) $this->getPreference('sitemap-' . $file . '.timestamp');
        $expiry_time = Carbon::now()->subSeconds(self::CACHE_LIFE)->unix();

        if ($timestamp > $expiry_time) {
            $content = $this->getPreference('sitemap-' . $file . '.xml');
        } else {
            $tree = $this->tree_service->find((int) $match[1]);

            if ($tree->getPreference('include_in_sitemap') !== '1') {
                throw new HttpNotFoundException();
            }

            $records = $this->sitemapRecords($tree, $match[2], self::RECORDS_PER_VOLUME, self::RECORDS_PER_VOLUME * $match[3]);

            $content = view('modules/sitemap/sitemap-file.xml', ['records' => $records]);

            $this->setPreference('sitemap.xml', $content);
        }

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
            case 'i':
                $records = $this->sitemapIndividuals($tree, $limit, $offset);
                break;

            case 'm':
                $records = $this->sitemapMedia($tree, $limit, $offset);
                break;

            case 'n':
                $records = $this->sitemapNotes($tree, $limit, $offset);
                break;

            case 'r':
                $records = $this->sitemapRepositories($tree, $limit, $offset);
                break;

            case 's':
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
