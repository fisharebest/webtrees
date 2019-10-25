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

use function assert;
use function redirect;

/**
 * Controller for the note page.
 */
class NoteController extends AbstractBaseController
{
    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * MediaController constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * Show a note's page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        $slug = $request->getAttribute('slug');
        $note = Note::getInstance($xref, $tree);

        Auth::checkNoteAccess($note, false);

        if ($slug !== $note->slug()) {
            return redirect($note->url());
        }

        return $this->viewResponse('note-page', [
            'clipboard_facts' => $this->clipboard_service->pastableFacts($note, new Collection()),
            'facts'           => $this->facts($note),
            'families'        => $note->linkedFamilies('NOTE'),
            'individuals'     => $note->linkedIndividuals('NOTE'),
            'note'            => $note,
            'notes'           => new Collection([]),
            'media_objects'   => $note->linkedMedia('NOTE'),
            'meta_robots'     => 'index,follow',
            'sources'         => $note->linkedSources('NOTE'),
            'text'            => Filter::formatText($note->getNote(), $tree),
            'title'           => $note->fullName(),
        ]);
    }

    /**
     * @param Note $record
     *
     * @return Collection
     */
    private function facts(Note $record): Collection
    {
        return $record->facts()
            ->filter(static function (Fact $fact): bool {
                return $fact->getTag() !== 'CONT';
            });
    }
}
