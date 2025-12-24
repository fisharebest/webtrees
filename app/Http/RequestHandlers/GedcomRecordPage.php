<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function get_class;
use function in_array;
use function redirect;

final class GedcomRecordPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // These standard genealogy record types have their own pages.
    private const array STANDARD_RECORDS = [
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

    public function __construct(
        private readonly ClipboardService $clipboard_service,
        private readonly LinkedRecordService $linked_record_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record);

        // Standard genealogy records have their own pages.
        if ($record->xref() !== $xref || in_array(get_class($record), self::STANDARD_RECORDS, true)) {
            return redirect($record->url());
        }

        $linked_families     = $this->linked_record_service->linkedFamilies($record);
        $linked_individuals  = $this->linked_record_service->linkedIndividuals($record);
        $linked_locations    = $this->linked_record_service->linkedLocations($record);
        $linked_media        = $this->linked_record_service->linkedMedia($record);
        $linked_notes        = $this->linked_record_service->linkedNotes($record);
        $linked_repositories = $this->linked_record_service->linkedRepositories($record);
        $linked_sources      = $this->linked_record_service->linkedSources($record);
        $linked_submitters   = $this->linked_record_service->linkedSubmitters($record);

        return $this->viewResponse('record-page', [
            'clipboard_facts'      => $this->clipboard_service->pastableFacts($record),
            'linked_families'      => $linked_families->isEmpty() ? null : $linked_families,
            'linked_individuals'   => $linked_individuals->isEmpty() ? null : $linked_individuals,
            'linked_locations'     => $linked_locations->isEmpty() ? null : $linked_locations,
            'linked_media_objects' => $linked_media->isEmpty() ? null : $linked_media,
            'linked_notes'         => $linked_notes->isEmpty() ? null : $linked_notes,
            'linked_repositories'  => $linked_repositories->isEmpty() ? null : $linked_repositories,
            'linked_sources'       => $linked_sources->isEmpty() ? null : $linked_sources,
            'linked_submitters'    => $linked_submitters->isEmpty() ? null : $linked_submitters,
            'record'               => $record,
            'title'                => $record->fullName(),
            'tree'                 => $tree,
        ])->withHeader('Link', '<' . $record->url() . '>; rel="canonical"');
    }
}
