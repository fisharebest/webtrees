<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleConfigTrait;
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

use function date;
use function redirect;
use function response;
use function route;
use function view;

class SitemapDataXml
{
    private const array PRIORITY = [
        Family::RECORD_TYPE     => 0.7,
        Individual::RECORD_TYPE => 0.9,
        Media::RECORD_TYPE      => 0.5,
        Note::RECORD_TYPE       => 0.3,
        Repository::RECORD_TYPE => 0.5,
        Source::RECORD_TYPE     => 0.5,
        Submitter::RECORD_TYPE  => 0.3,
    ];

    public function get(Tree $tree, string $type, int $page): ResponseInterface
    {
        if ($tree->getPreference('include_in_sitemap') !== '1') {
            throw new HttpNotFoundException();
        }

        $records = $this->sitemapRecords($tree, $type, SitemapIndexXml::RECORDS_PER_VOLUME, SitemapIndexXml::RECORDS_PER_VOLUME * $page);

        $content = view('sitemap/sitemap-data-xml', [
            'priority'    => self::PRIORITY[$type],
            'records'     => $records,
            'sitemap_xsl' => route(SitemapXsl::class),
            'tree'        => $tree,
        ]);

        return response($content, HttpStatusCode::OK, [
            'content-type'  => 'application/xml',
            'cache-control' => 'public, max-age=1209600',
        ]);
    }

    /**
     *
     * @return Collection<int,GedcomRecord>
     */
    private function sitemapRecords(Tree $tree, string $type, int $limit, int $offset): Collection
    {
        $records = match ($type) {
            Family::RECORD_TYPE     => $this->sitemapFamilies($tree, $limit, $offset),
            Individual::RECORD_TYPE => $this->sitemapIndividuals($tree, $limit, $offset),
            Media::RECORD_TYPE      => $this->sitemapMedia($tree, $limit, $offset),
            Note::RECORD_TYPE       => $this->sitemapNotes($tree, $limit, $offset),
            Repository::RECORD_TYPE => $this->sitemapRepositories($tree, $limit, $offset),
            Source::RECORD_TYPE     => $this->sitemapSources($tree, $limit, $offset),
            Submitter::RECORD_TYPE  => $this->sitemapSubmitters($tree, $limit, $offset),
            default                 => throw new HttpNotFoundException(),
        };

        // Skip private records.
        return $records->filter(static fn (GedcomRecord $record): bool => $record->canShow(AccessLevel::Public));
    }

    /**
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
