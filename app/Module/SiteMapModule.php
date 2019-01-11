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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SiteMapModule
 */
class SiteMapModule extends AbstractModule implements ModuleConfigInterface
{
    private const RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.
    private const CACHE_LIFE         = 1209600; // Two weeks

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module - see http://en.wikipedia.org/wiki/Sitemaps */
        return I18N::translate('Sitemaps');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Sitemaps” module */
        return I18N::translate('Generate sitemap files for search engines.');
    }

    /**
     * The URL to a page where the user can modify the configuration of this module.
     *
     * @return string
     */
    public function getConfigLink(): string
    {
        return route('module', [
            'module' => $this->getName(),
            'action' => 'Admin',
        ]);
    }

    /**
     * @return Response
     */
    public function getAdminAction(): Response
    {
        $this->layout = 'layouts/administration';

        $sitemap_url = route('module', [
            'module' => 'sitemap',
            'action' => 'Index',
        ]);

        // This list comes from http://en.wikipedia.org/wiki/Sitemaps
        $submit_urls = [
            'Bing/Yahoo' => Html::url('https://www.bing.com/webmaster/ping.aspx', ['siteMap' => $sitemap_url]),
            'Google'     => Html::url('https://www.google.com/webmasters/tools/ping', ['sitemap' => $sitemap_url]),
        ];

        return $this->viewResponse('modules/sitemap/config', [
            'all_trees'   => Tree::getAll(),
            'sitemap_url' => $sitemap_url,
            'submit_urls' => $submit_urls,
            'title'       => $this->getTitle(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function postAdminAction(Request $request): RedirectResponse
    {
        foreach (Tree::getAll() as $tree) {
            $include_in_sitemap = (bool) $request->get('sitemap' . $tree->id());
            $tree->setPreference('include_in_sitemap', (string) $include_in_sitemap);
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->getTitle()), 'success');

        return new RedirectResponse($this->getConfigLink());
    }

    /**
     * @return Response
     */
    public function getIndexAction(): Response
    {
        $timestamp = (int) $this->getPreference('sitemap.timestamp');

        if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
            $content = $this->getPreference('sitemap.xml');
        } else {
            $count_individuals = DB::table('individuals')
                ->groupBy('i_file')
                ->select([DB::raw('COUNT(*) AS total'), 'i_file'])
                ->pluck('total', 'i_file');

            $count_media = DB::table('media')
                ->groupBy('m_file')
                ->select([DB::raw('COUNT(*) AS total'), 'm_file'])
                ->pluck('total', 'm_file');

            $count_notes = DB::table('other')
                ->where('o_type', '=', 'NOTE')
                ->groupBy('o_file')
                ->select([DB::raw('COUNT(*) AS total'), 'o_file'])
                ->pluck('total', 'o_file');

            $count_repositories = DB::table('other')
                ->where('o_type', '=', 'REPO')
                ->groupBy('o_file')
                ->select([DB::raw('COUNT(*) AS total'), 'o_file'])
                ->pluck('total', 'o_file');

            $count_sources = DB::table('sources')
                ->groupBy('s_file')
                ->select([DB::raw('COUNT(*) AS total'), 's_file'])
                ->pluck('total', 's_file');

            $content = view('modules/sitemap/sitemap-index.xml', [
                'all_trees'          => Tree::getAll(),
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

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getFileAction(Request $request): Response
    {
        $file = $request->get('file', '');

        if (!preg_match('/^(\d+)-([imnrs])-(\d+)$/', $file, $match)) {
            throw new NotFoundHttpException('Bad sitemap file');
        }

        $timestamp = (int) $this->getPreference('sitemap-' . $file . '.timestamp');

        if ($timestamp > WT_TIMESTAMP - self::CACHE_LIFE) {
            $content = $this->getPreference('sitemap-' . $file . '.xml');
        } else {
            $tree = Tree::findById((int) $match[1]);

            if ($tree === null) {
                throw new NotFoundHttpException('No such tree');
            }

            $records = $this->sitemapRecords($tree, $match[2], self::RECORDS_PER_VOLUME, self::RECORDS_PER_VOLUME * $match[3]);

            $content = view('modules/sitemap/sitemap-file.xml', ['records' => $records]);

            $this->setPreference('sitemap.xml', $content);
        }

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * @param Tree   $tree
     * @param string $type
     * @param int    $limit
     * @param int    $offset
     *
     * @return Collection|GedcomRecord[]
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
                throw new NotFoundHttpException('Invalid record type: ' . $type);
        }

        // Skip private records.
        $records = $records->filter(GedcomRecord::accessFilter());

        return $records;
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection|Individual[]
     */
    private function sitemapIndividuals(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->orderBy('i_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Individual::rowMapper());
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection|Media[]
     */
    private function sitemapMedia(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->orderBy('m_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Media::rowMapper());
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection|Note[]
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
            ->map(Note::rowMapper());
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection|Repository[]
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
            ->map(Repository::rowMapper());
    }

    /**
     * @param Tree $tree
     * @param int  $limit
     * @param int  $offset
     *
     * @return Collection|Source[]
     */
    private function sitemapSources(Tree $tree, int $limit, int $offset): Collection
    {
        return DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->orderBy('s_id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(Source::rowMapper());
    }
}
