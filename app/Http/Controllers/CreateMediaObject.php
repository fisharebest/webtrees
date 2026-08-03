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

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Exceptions\FileUploadException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function response;
use function view;

final class CreateMediaObject
{
    public function __construct(
        private readonly MediaFileService $media_file_service,
        private readonly PendingChangesService $pending_changes_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree            = Validator::attributes($request)->tree();
        $max_upload_size = $this->media_file_service->maxUploadFilesize();
        $media_types     = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();
        $unused_files    = $this->media_file_service->unusedFiles($tree);

        return response(view('modals/create-media-object', [
            'max_upload_size' => $max_upload_size,
            'media_types'     => $media_types,
            'tree'            => $tree,
            'unused_files'    => $unused_files,
        ]));
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree        = Validator::attributes($request)->tree();
        $note        = Validator::parsedBody($request)->string('media-note');
        $title       = Validator::parsedBody($request)->string('title');
        $type        = Validator::parsedBody($request)->string('type');
        $restriction = Validator::parsedBody($request)->string('restriction');

        $note        = Registry::elementFactory()->make('OBJE:NOTE')->canonical($note);
        $type        = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->canonical($type);
        $title       = Registry::elementFactory()->make('OBJE:FILE:TITL')->canonical($title);
        $restriction = Registry::elementFactory()->make('OBJE:RESN')->canonical($restriction);

        try {
            $file = $this->media_file_service->uploadFile($request);
        } catch (FileUploadException $exception) {
            return response([
                'value' => '',
                'text'  => '',
                'html'  => view('components/alert-danger', [
                    'alert' => $exception->getMessage(),
                ]),
            ]);
        }

        if ($file === '') {
            return response(['error_message' => I18N::translate('There was an error uploading your file.')], HttpStatusCode::NotAcceptable);
        }

        $gedcom = "0 @@ OBJE\n" . $this->media_file_service->createMediaFileGedcom($file, $type, $title, $note);

        if ($restriction !== '') {
            $gedcom .= "\n1 RESN " . strtr($restriction, ["\n" => "\n2 CONT "]);
        }

        $record = $tree->createMediaObject($gedcom);

        // Accept the new record to keep the filesystem synchronized with the genealogy.
        $this->pending_changes_service->acceptRecord($record);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/media', ['media' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The media object has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}
