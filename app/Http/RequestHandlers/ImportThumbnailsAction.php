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

use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function dirname;
use function explode;
use function response;
use function strlen;
use function substr;

/**
 * Import custom thumbnails from webtrees 1.x.
 */
class ImportThumbnailsAction implements RequestHandlerInterface
{
    /** @var PendingChangesService */
    private $pending_changes_service;

    /** @var TreeService */
    private $tree_service;

    /**
     * ImportThumbnailsController constructor.
     *
     * @param PendingChangesService $pending_changes_service
     * @param TreeService           $tree_service
     */
    public function __construct(
        PendingChangesService $pending_changes_service,
        TreeService $tree_service
    ) {
        $this->pending_changes_service = $pending_changes_service;
        $this->tree_service            = $tree_service;
    }

    /**
     * Import custom thumbnails from webtrees 1.x.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data_filesystem = Registry::filesystem()->data();

        $params = (array) $request->getParsedBody();

        $thumbnail = $params['thumbnail'];
        $action    = $params['action'];
        $xrefs     = $params['xref'];
        $geds      = $params['ged'];

        if (!$data_filesystem->has($thumbnail)) {
            return response([]);
        }

        $media_objects = [];

        foreach ($xrefs as $key => $xref) {
            $tree            = $this->tree_service->all()->get($geds[$key]);
            $media_objects[] = Registry::mediaFactory()->make($xref, $tree);
        }

        switch ($action) {
            case 'delete':
                $data_filesystem->delete($thumbnail);
                break;

            case 'add':
                $mime_type = $data_filesystem->getMimetype($thumbnail) ?: Mime::DEFAULT_TYPE;
                $directory = dirname($thumbnail, 2);
                $sha1      = sha1($data_filesystem->read($thumbnail));
                $extension = explode('/', $mime_type)[1];
                $move_to   = $directory . '/' . $sha1 . '.' . $extension;

                $data_filesystem->rename($thumbnail, $move_to);

                foreach ($media_objects as $media_object) {
                    $prefix = $media_object->tree()->getPreference('MEDIA_DIRECTORY');
                    $gedcom = '1 FILE ' . substr($move_to, strlen($prefix)) . "\n2 FORM " . $extension;

                    if ($media_object->firstImageFile() === null) {
                        // The media object doesn't have an image.  Add this as a secondary file.
                        $media_object->createFact($gedcom, true);
                    } else {
                        // The media object already has an image.  Show this custom one in preference.
                        $gedcom = '0 @' . $media_object->xref() . "@ OBJE\n" . $gedcom;
                        foreach ($media_object->facts() as $fact) {
                            $gedcom .= "\n" . $fact->gedcom();
                        }
                        $media_object->updateRecord($gedcom, true);
                    }

                    // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
                    $this->pending_changes_service->acceptRecord($media_object);
                }
                break;
        }

        return response([]);
    }
}
