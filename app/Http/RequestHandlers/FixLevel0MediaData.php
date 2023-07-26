<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function e;
use function in_array;
use function preg_match;
use function view;

/**
 * Move media links from records to facts.
 */
class FixLevel0MediaData implements RequestHandlerInterface
{
    private DatatablesService $datatables_service;

    private TreeService $tree_service;

    /**
     * FixLevel0MediaController constructor.
     *
     * @param DatatablesService $datatables_service
     * @param TreeService       $tree_service
     */
    public function __construct(DatatablesService $datatables_service, TreeService $tree_service)
    {
        $this->datatables_service = $datatables_service;
        $this->tree_service       = $tree_service;
    }

    /**
     * If media objects are wronly linked to top-level records, reattach them
     * to facts/events.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ignore_facts = [
            'INDI:NAME',
            'INDI:SEX',
            'INDI:CHAN',
            'INDI:NOTE',
            'INDI:SOUR',
            'INDI:SUBM',
            'INDI:RESN',
        ];

        $prefix = DB::connection()->getTablePrefix();

        $search = Validator::queryParams($request)->array('search')['value'] ?? '';

        $query = DB::table('media')
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('link.l_file', '=', 'media.m_file')
                    ->on('link.l_to', '=', 'media.m_id')
                    ->where('link.l_type', '=', 'OBJE');
            })
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('individuals.i_file', '=', 'link.l_file')
                    ->on('individuals.i_id', '=', 'link.l_from');
            })
            ->where('i_gedcom', 'LIKE', new Expression("('%\n1 OBJE @' || " . $prefix . "media.m_id || '@%')"))
            ->orderBy('individuals.i_file')
            ->orderBy('individuals.i_id')
            ->orderBy('media.m_id')
            ->where('descriptive_title', 'LIKE', '%' . addcslashes($search, '\\%_') . '%')
            ->select(['media.m_file', 'media.m_id', 'media.m_gedcom', 'individuals.i_id', 'individuals.i_gedcom']);

        return $this->datatables_service->handleQuery($request, $query, [], [], function (object $datum) use ($ignore_facts): array {
            $tree       = $this->tree_service->find((int) $datum->m_file);
            $media      = Registry::mediaFactory()->make($datum->m_id, $tree, $datum->m_gedcom);
            $individual = Registry::individualFactory()->make($datum->i_id, $tree, $datum->i_gedcom);

            $facts = $individual->facts([], true)
                ->filter(static function (Fact $fact) use ($ignore_facts): bool {
                    return
                        !$fact->isPendingDeletion() &&
                        !preg_match('/^@' . Gedcom::REGEX_XREF . '@$/', $fact->value()) &&
                        !in_array($fact->tag(), $ignore_facts, true);
                });

            // The link to the media object may have been deleted in a pending change.
            $deleted = true;
            foreach ($individual->facts(['OBJE']) as $fact) {
                if ($fact->target() === $media && !$fact->isPendingDeletion()) {
                    $deleted = false;
                }
            }
            if ($deleted) {
                $facts = new Collection();
            }

            $facts = $facts->map(static function (Fact $fact) use ($individual, $media): string {
                return view('admin/fix-level-0-media-action', [
                    'fact'       => $fact,
                    'individual' => $individual,
                    'media'      => $media,
                ]);
            });

            return [
                $tree->name(),
                $media->displayImage(100, 100, 'contain', ['class' => 'img-thumbnail']),
                '<a href="' . e($media->url()) . '">' . $media->fullName() . '</a>',
                '<a href="' . e($individual->url()) . '">' . $individual->fullName() . '</a>',
                $facts->implode(' '),
            ];
        });
    }
}
