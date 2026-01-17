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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;
use function view;

final class EditMediaFileModal implements RequestHandlerInterface
{
    public function __construct(
        private readonly MediaFileService $media_file_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $xref    = Validator::attributes($request)->isXref()->string('xref');
        $fact_id = Validator::attributes($request)->string('fact_id');
        $media   = Registry::mediaFactory()->make($xref, $tree);

        try {
            $media = Auth::checkMediaAccess($media);
        } catch (HttpNotFoundException | HttpAccessDeniedException $ex) {
            return response(view('modals/error', [
                'title' => I18N::translate('Edit a media file'),
                'error' => $ex->getMessage(),
            ]), StatusCodeInterface::STATUS_FORBIDDEN);
        }

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                $media_types = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->values();

                return response(view('modals/edit-media-file', [
                    'media_file'      => $media_file,
                    'max_upload_size' => $this->media_file_service->maxUploadFilesize(),
                    'media'           => $media,
                    'media_types'     => $media_types,
                    'unused_files'    => $this->media_file_service->unusedFiles($tree),
                    'tree'            => $tree,
                ]));
            }
        }

        return response('', StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
