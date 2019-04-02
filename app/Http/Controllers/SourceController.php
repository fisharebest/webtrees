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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for the source page.
 */
class SourceController extends AbstractBaseController
{
    // Show the source's facts in this order:
    private const FACT_ORDER = [
        1 => 'TITL',
        'ABBR',
        'AUTH',
        'DATA',
        'PUBL',
        'TEXT',
        'REPO',
        'NOTE',
        'OBJE',
        'REFN',
        'RIN',
        '_UID',
        'CHAN',
        'RESN',
    ];

    /**
     * Show a repository's page.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ClipboardService       $clipboard_service
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, Tree $tree, ClipboardService $clipboard_service): ResponseInterface
    {
        $xref   = $request->get('xref', '');
        $record = Source::getInstance($xref, $tree);

        Auth::checkSourceAccess($record, false);

        return $this->viewResponse('source-page', [
            'clipboard_facts' => $clipboard_service->pastableFacts($record, new Collection()),
            'facts'           => $this->facts($record),
            'families'        => $record->linkedFamilies('SOUR'),
            'individuals'     => $record->linkedIndividuals('SOUR'),
            'meta_robots'     => 'index,follow',
            'notes'           => $record->linkedNotes('SOUR'),
            'media_objects'   => $record->linkedMedia('SOUR'),
            'source'          => $record,
            'title'           => $record->fullName(),
        ]);
    }

    /**
     * @param Source $record
     *
     * @return Collection
     * @return Fact[]
     */
    private function facts(Source $record): Collection
    {
        return $record->facts()
            ->sort(static function (Fact $x, Fact $y): int {
                $sort_x = array_search($x->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;
                $sort_y = array_search($y->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;

                return $sort_x <=> $sort_y;
            });
    }
}
