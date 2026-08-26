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
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;

use function date;
use function response;
use function route;
use function view;

final class SitemapIndexXml
{
    public const int RECORDS_PER_VOLUME = 500; // Keep sitemap files small, for memory, CPU and max_allowed_packet limits.

    private TreeService $tree_service;

    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    public function get(): ResponseInterface
    {
        // Which trees have sitemaps enabled?
        // Filter private trees, so logged-in users see the same as search-engines.
        $public_trees = $this->tree_service->all()
            ->filter(static fn (Tree $tree): bool => !$tree->private())
            ->filter(static fn (Tree $tree): bool => $tree->getPreference('include_in_sitemap') === '1');

        $tree_ids = $public_trees->map(static fn (Tree $tree): int => $tree->id());

        $count_families = DB::table('families')
            ->join('gedcom', 'f_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_individuals = DB::table('individuals')
            ->join('gedcom', 'i_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_media = DB::table('media')
            ->join('gedcom', 'm_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_notes = DB::table('other')
            ->join('gedcom', 'o_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_repositories = DB::table('other')
            ->join('gedcom', 'o_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_sources = DB::table('sources')
            ->join('gedcom', 's_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $count_submitters = DB::table('other')
            ->join('gedcom', 'o_file', '=', 'gedcom_id')
            ->whereIn('gedcom_id', $tree_ids)
            ->where('o_type', '=', Submitter::RECORD_TYPE)
            ->groupBy(['gedcom_name'])
            ->pluck(new Expression('COUNT(*) AS total'), 'gedcom_name');

        $content = view('sitemap/sitemap-index-xml', [
            'all_trees'          => $public_trees,
            'count_families'     => $count_families,
            'count_individuals'  => $count_individuals,
            'count_media'        => $count_media,
            'count_notes'        => $count_notes,
            'count_repositories' => $count_repositories,
            'count_sources'      => $count_sources,
            'count_submitters'   => $count_submitters,
            'last_mod'           => date('Y-m-d'),
            'records_per_volume' => self::RECORDS_PER_VOLUME,
            'sitemap_xsl'        => route(SitemapXsl::class),
        ]);

        return response($content, HttpStatusCode::OK, [
            'content-type'  => 'application/xml',
            'cache-control' => 'public, max-age=1209600',
        ]);
    }
}
