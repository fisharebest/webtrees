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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Services\DatatablesService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for fixing media links.
 */
class FixLevel0MediaController extends AbstractAdminController
{
    /**
     * If media objects are wronly linked to top-level records, reattach them
     * to facts/events.
     *
     * @return Response
     */
    public function fixLevel0Media(): Response
    {
        return $this->viewResponse('admin/fix-level-0-media', [
            'title' => I18N::translate('Link media objects to facts and events'),
        ]);
    }

    /**
     * Move a link to a media object from a level 0 record to a level 1 record.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function fixLevel0MediaAction(Request $request): Response
    {
        $fact_id   = $request->get('fact_id', '');
        $indi_xref = $request->get('indi_xref', '');
        $obje_xref = $request->get('obje_xref', '');
        $tree_id   = (int) $request->get('tree_id');

        $tree       = Tree::findById($tree_id);
        $individual = Individual::getInstance($indi_xref, $tree);
        $media      = Media::getInstance($obje_xref, $tree);

        if ($individual !== null && $media !== null) {
            foreach ($individual->facts() as $fact1) {
                if ($fact1->id() === $fact_id) {
                    $individual->updateFact($fact_id, $fact1->gedcom() . "\n2 OBJE @" . $obje_xref . '@', false);
                    foreach ($individual->facts(['OBJE']) as $fact2) {
                        if ($fact2->target() === $media) {
                            $individual->deleteFact($fact2->id(), false);
                        }
                    }
                    break;
                }
            }
        }

        return new Response();
    }

    /**
     * If media objects are wronly linked to top-level records, reattach them
     * to facts/events.
     *
     * @param Request           $request
     * @param DatatablesService $datatables_service
     *
     * @return JsonResponse
     */
    public function fixLevel0MediaData(Request $request, DatatablesService $datatables_service): JsonResponse
    {
        $ignore_facts = [
            'FAMC',
            'FAMS',
            'NAME',
            'SEX',
            'CHAN',
            'NOTE',
            'OBJE',
            'SOUR',
            'RESN',
        ];

        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('media')
            ->join('media_file', function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('link', function (JoinClause $join): void {
                $join
                    ->on('link.l_file', '=', 'media.m_file')
                    ->on('link.l_to', '=', 'media.m_id');
            })
            ->join('individuals', function (JoinClause $join): void {
                $join
                    ->on('individuals.i_file', '=', 'link.l_file')
                    ->on('individuals.i_id', '=', 'link.l_from');
            })
            ->where('i_gedcom', 'LIKE', DB::raw("CONCAT('%\n1 OBJE @', " . $prefix . "media.m_id, '@%')"))
            ->orderBy('individuals.i_file')
            ->orderBy('individuals.i_id')
            ->orderBy('media.m_id')
            ->select(['media.m_file', 'media.m_id', 'media.m_gedcom', 'individuals.i_id', 'individuals.i_gedcom']);

        return $datatables_service->handle($request, $query, [], [], function (stdClass $datum) use ($ignore_facts): array {
            $tree       = Tree::findById((int) $datum->m_file);
            $media      = Media::getInstance($datum->m_id, $tree, $datum->m_gedcom);
            $individual = Individual::getInstance($datum->i_id, $tree, $datum->i_gedcom);

            $facts = $individual->facts([], true)
                ->filter(function (Fact $fact) use ($ignore_facts): bool {
                    return !$fact->isPendingDeletion() && !in_array($fact->getTag(), $ignore_facts);
                });

            // The link to the media object may have been deleted in a pending change.
            $deleted = true;
            foreach ($individual->facts(['OBJE']) as $fact) {
                if ($fact->target() === $media && !$fact->isPendingDeletion()) {
                    $deleted = false;
                }
            }
            if ($deleted) {
                $facts = [];
            }

            $facts = $facts->map(function (Fact $fact) use ($individual, $media): string {
                return view('admin/fix-level-0-media-action', [
                    'fact'       => $fact,
                    'individual' => $individual,
                    'media'      => $media,
                ]);
            });

            return [
                $tree->name(),
                $media->displayImage(100, 100, 'fit', ['class' => 'img-thumbnail']),
                '<a href="' . e($media->url()) . '">' . $media->fullName() . '</a>',
                '<a href="' . e($individual->url()) . '">' . $individual->fullName() . '</a>',
                $facts->implode(' '),
            ];
        });
    }
}
