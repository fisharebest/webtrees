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
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for the note page.
 */
class NoteController extends AbstractBaseController
{
    /**
     * Show a note's page.
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
        $record = Note::getInstance($xref, $tree);

        Auth::checkNoteAccess($record, false);

        return $this->viewResponse('note-page', [
            'clipboard_facts' => $clipboard_service->pastableFacts($record, new Collection()),
            'facts'         => $this->facts($record),
            'families'      => $record->linkedFamilies('NOTE'),
            'individuals'   => $record->linkedIndividuals('NOTE'),
            'note'          => $record,
            'notes'         => [],
            'media_objects' => $record->linkedMedia('NOTE'),
            'meta_robots'   => 'index,follow',
            'sources'       => $record->linkedSources('NOTE'),
            'text'          => Filter::formatText($record->getNote(), $tree),
            'title'         => $record->fullName(),
        ]);
    }

    /**
     * @param Note $record
     *
     * @return Collection
     * @return Fact[]
     */
    private function facts(Note $record): Collection
    {
        return $record->facts()
            ->filter(static function (Fact $fact): bool {
                return $fact->getTag() !== 'CONT';
            });
    }
}
