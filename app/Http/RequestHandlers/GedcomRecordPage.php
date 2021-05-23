<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Display non-standard genealogy records.
 */
class GedcomRecordPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // These standard genealogy record types have their own pages.
    private const STANDARD_RECORDS = [
        Family::class,
        Header::class,
        Individual::class,
        Location::class,
        Media::class,
        Note::class,
        Repository::class,
        Source::class,
        Submission::class,
        Submitter::class,
    ];

    /**
     * Show a gedcom record's page.
     *
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

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record);

        // Standard genealogy records have their own pages.
        if ($record->xref() !== $xref || in_array(get_class($record), self::STANDARD_RECORDS, true)) {
            return redirect($record->url());
        }

        return $this->viewResponse('record-page', [
            'clipboard_facts'      => new Collection(),
            'linked_families'      => $record->linkedFamilies($record->tag()),
            'linked_individuals'   => $record->linkedIndividuals($record->tag()),
            'linked_media_objects' => $record->linkedMedia($record->tag()),
            'linked_notes'         => $record->linkedNotes($record->tag()),
            'linked_sources'       => $record->linkedSources($record->tag()),
            'record'               => $record,
            'title'                => $record->fullName(),
            'tree'                 => $tree,
        ]);
    }
}
