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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function in_array;
use function response;

/**
 * Process a form to create a new media object.
 */
class CreateMediaObjectAction implements RequestHandlerInterface
{
    private MediaFileService $media_file_service;

    private PendingChangesService $pending_changes_service;

    /**
     * CreateMediaObjectAction constructor.
     *
     * @param MediaFileService      $media_file_service
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(MediaFileService $media_file_service, PendingChangesService $pending_changes_service)
    {
        $this->media_file_service      = $media_file_service;
        $this->pending_changes_service = $pending_changes_service;
    }

    /**
     * Process a form to create a new media object.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params      = (array) $request->getParsedBody();
        $note        = $params['media-note'] ?? '';
        $title       = $params['title'] ?? '';
        $type        = $params['type'] ?? '';
        $restriction = $params['restriction'] ?? '';

        $file = $this->media_file_service->uploadFile($request);

        if ($file === '') {
            return response(['error_message' => I18N::translate('There was an error uploading your file.')], StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
        }

        $gedcom = "0 @@ OBJE\n" . $this->media_file_service->createMediaFileGedcom($file, $type, $title, $note);

        if (in_array($restriction, ['none', 'privacy', 'confidential', 'locked'], true)) {
            $gedcom .= "\n1 RESN " . $restriction;
        }

        $record = $tree->createMediaObject($gedcom);
        $record = Registry::mediaFactory()->new($record->xref(), $record->gedcom(), null, $tree);

        // Accept the new record to keep the filesystem synchronized with the genealogy.
        $this->pending_changes_service->acceptRecord($record);

        // id and text are for select2 / autocomplete
        // html is for interactive modals
        return response([
            'id'   => '@' . $record->xref() . '@',
            'text' => view('selects/media', [
                'media' => $record,
            ]),
            'html' => view('modals/record-created', [
                'title' => I18N::translate('The media object has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
