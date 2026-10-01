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

use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\Value;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function addcslashes;
use function e;
use function in_array;
use function preg_match;
use function view;

final class FixLevel0MediaData
{
    public function __construct(
        private DatatablesService $datatables_service,
        private TreeService $tree_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
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

        $search = Validator::parsedBody($request)->array('search')['value'] ?? '';

        $query = DB::query()
            ->select('media.m_file', 'media.m_id', 'media.m_gedcom', 'individuals.i_id', 'individuals.i_gedcom')
            ->from('media')
            ->join('media_file', static fn (JoinOn $j): JoinOn => $j->on('media_file.m_file', 'media.m_file')->on('media_file.m_id', 'media.m_id'))
            ->join('link', static fn (JoinOn $j): JoinOn => $j->on('link.l_file', 'media.m_file')->on('link.l_to', 'media.m_id')->on('link.l_type', DB::value('OBJE')))
            ->join('individuals', static fn (JoinOn $j): JoinOn => $j->on('individuals.i_file', 'link.l_file')->on('individuals.i_id', 'link.l_from'))
            ->whereLike('i_gedcom', DB::concat(DB::value('%\n1 OBJE @'), DB::column('media.m_id'), DB::value('@%')))
            ->whereLike('descriptive_title', '%' . addcslashes($search, '\\%_') . '%');

        return $this->datatables_service->handleQuery(
            $request,
            $query,
            [],
            [],
            function (object $datum) use ($ignore_facts): array {
                $tree       = $this->tree_service->find((int) $datum->m_file);
                $media      = Registry::mediaFactory()->make($datum->m_id, $tree, $datum->m_gedcom);
                $individual = Registry::individualFactory()->make($datum->i_id, $tree, $datum->i_gedcom);

                $facts = $individual
                    ->facts([], true)
                    ->filter(static fn (Fact $fact): bool => !$fact->isPendingDeletion() &&
                        !preg_match('/^@' . Gedcom::REGEX_XREF . '@$/', $fact->value()) &&
                        !in_array($fact->tag(), $ignore_facts, true));

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

                $facts = $facts->map(static fn (Fact $fact): string => view('admin/fix-level-0-media-action', [
                    'fact'       => $fact,
                    'individual' => $individual,
                    'media'      => $media,
                ]));

                return [
                    $tree->name(),
                    $media->displayImage(100, 100, 'contain', ['class' => 'img-thumbnail']),
                    '<a href="' . e($media->url()) . '">' . $media->fullName() . '</a>',
                    '<a href="' . e($individual->url()) . '">' . $individual->fullName() . '</a>',
                    $facts->implode(' '),
                ];
            },
            default_sort: ['individuals.i_file', 'individuals.i_id', 'media.m_id'],
        );
    }
}
