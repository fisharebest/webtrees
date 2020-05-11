<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Show a note's page.
 */
class NotePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * NotePage constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $note = Factory::note()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note, false);

        // Redirect to correct xref/slug
        if ($note->xref() !== $xref || $request->getAttribute('slug') !== $note->slug()) {
            return redirect($note->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('note-page', [
            'clipboard_facts'  => $this->clipboard_service->pastableFacts($note, new Collection()),
            'facts'            => $this->facts($note),
            'families'         => $note->linkedFamilies('NOTE'),
            'individuals'      => $note->linkedIndividuals('NOTE'),
            'note'             => $note,
            'notes'            => new Collection(),
            'media_objects'    => $note->linkedMedia('NOTE'),
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'sources'          => $note->linkedSources('NOTE'),
            'text'             => Filter::formatText($note->getNote(), $tree),
            'title'            => $note->fullName(),
            'tree'             => $tree,
        ]);
    }

    /**
     * @param Note $record
     *
     * @return Collection<Fact>
     */
    private function facts(Note $record): Collection
    {
        return $record->facts()
            ->filter(static function (Fact $fact): bool {
                return $fact->getTag() !== 'CONT';
            });
    }
}
