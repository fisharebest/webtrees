<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Show a media object's page.
 */
class MediaPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ClipboardService $clipboard_service;

    private LinkedRecordService $linked_record_service;

    /**
     * @param ClipboardService $clipboard_service
     * @param LinkedRecordService $linked_record_service
     */
    public function __construct(ClipboardService $clipboard_service, LinkedRecordService $linked_record_service)
    {
        $this->clipboard_service     = $clipboard_service;
        $this->linked_record_service = $linked_record_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $slug   = Validator::attributes($request)->string('slug', '');
        $record = Registry::mediaFactory()->make($xref, $tree);
        $record = Auth::checkMediaAccess($record);

        // Redirect to correct xref/slug
        if ($record->xref() !== $xref || Registry::slugFactory()->make($record) !== $slug) {
            return redirect($record->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        $linked_families    = $this->linked_record_service->linkedFamilies($record);
        $linked_individuals = $this->linked_record_service->linkedIndividuals($record);
        $linked_locations   = $this->linked_record_service->linkedLocations($record);
        $linked_notes       = $this->linked_record_service->linkedNotes($record);
        $linked_sources     = $this->linked_record_service->linkedSources($record);

        return $this->viewResponse('media-page', [
            'clipboard_facts'    => $this->clipboard_service->pastableFacts($record),
            'linked_families'    => $linked_families,
            'linked_individuals' => $linked_individuals,
            'linked_locations'   => $linked_locations->isEmpty() ? null : $linked_locations,
            'linked_notes'       => $linked_notes,
            'linked_sources'     => $linked_sources,
            'meta_description'   => '',
            'meta_robots'        => 'index,follow',
            'record'             => $record,
            'title'              => $record->fullName(),
            'tree'               => $tree,
        ]);
    }
}
