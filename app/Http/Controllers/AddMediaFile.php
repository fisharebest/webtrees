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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpForbiddenException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function response;
use function view;

final class AddMediaFile
{
    public function __construct(
        private readonly MediaFileService $media_file_service,
        private readonly PendingChangesService $pending_changes_service
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree  = Validator::attributes($request)->tree();
        $xref  = Validator::attributes($request)->isXref()->string('xref');
        $media = Registry::mediaFactory()->make($xref, $tree);

        try {
            $media = Auth::checkMediaAccess($media);
        } catch (HttpNotFoundException | HttpForbiddenException $ex) {
            return response(view('modals/error', [
                'title' => I18N::translate('Add a media file'),
                'error' => $ex->getMessage(),
            ]));
        }

        $max_upload_size = $this->media_file_service->maxUploadFilesize();
        $media_types     = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();
        $unused_files    = $this->media_file_service->unusedFiles($tree);

        return response(view('modals/add-media-file', [
            'max_upload_size' => $max_upload_size,
            'media'           => $media,
            'media_types'     => $media_types,
            'tree'            => $tree,
            'unused_files'    => $unused_files,
        ]));
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $media  = Registry::mediaFactory()->make($xref, $tree);
        $media  = Auth::checkMediaAccess($media, true);
        $title  = Validator::parsedBody($request)->string('title');
        $type   = Validator::parsedBody($request)->string('type');

        $type  = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->canonical($type);
        $title = Registry::elementFactory()->make('OBJE:FILE:TITL')->canonical($title);

        $file = $this->media_file_service->uploadFile($request);

        if ($file === '') {
            FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

            return redirect($media->url());
        }

        $gedcom = $this->media_file_service->createMediaFileGedcom($file, $type, $title, '');

        $media->createFact($gedcom, true);

        // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
        $this->pending_changes_service->acceptRecord($media);

        return redirect($media->url());
    }
}
