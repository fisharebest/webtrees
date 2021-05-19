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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;
use function route;

/**
 * Add a new media file to a media object.
 */
class AddMediaFileAction implements RequestHandlerInterface
{
    private MediaFileService $media_file_service;

    private PendingChangesService $pending_changes_service;

    /**
     * AddMediaFileAction constructor.
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
     * Add a media file to an existing media object.
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

        $media = Registry::mediaFactory()->make($xref, $tree);

        $params = (array) $request->getParsedBody();
        $title  = $params['title'] ?? '';
        $type   = $params['type'] ?? '';

        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route(TreePage::class, ['tree' => $tree->name()]));
        }

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
