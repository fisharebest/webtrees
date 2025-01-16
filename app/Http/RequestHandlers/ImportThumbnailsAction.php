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

use Fisharebest\Webtrees\Mime;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
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
    private PendingChangesService $pending_changes_service;

    private TreeService $tree_service;

    /**
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

        $thumbnail = Validator::parsedBody($request)->string('thumbnail');
        $action    = Validator::parsedBody($request)->string('action');
        $xrefs     = Validator::parsedBody($request)->array('xref');
        $geds      = Validator::parsedBody($request)->array('ged');

        try {
            $file_exists = $data_filesystem->fileExists($thumbnail);
        } catch (FilesystemException | UnableToRetrieveMetadata $ex) {
            $file_exists = false;
        }

        if (!$file_exists) {
            return response([]);
        }

        $media_objects = [];

        foreach ($xrefs as $key => $xref) {
            $tree            = $this->tree_service->all()->get($geds[$key]);
            $media_objects[] = Registry::mediaFactory()->make($xref, $tree);
        }

        switch ($action) {
            case 'delete':
                try {
                    $data_filesystem->delete($thumbnail);
                } catch (FilesystemException | UnableToDeleteFile $ex) {
                    // Cannot delete the file.  Leave it there.
                }
                break;

            case 'add':
                try {
                    $mime_type = $data_filesystem->mimeType($thumbnail) ?: Mime::DEFAULT_TYPE;
                } catch (FilesystemException | UnableToRetrieveMetadata $ex) {
                    $mime_type = Mime::DEFAULT_TYPE;
                }

                try {
                    $directory = dirname($thumbnail, 2);
                    $sha1      = sha1($data_filesystem->read($thumbnail));
                    $extension = explode('/', $mime_type)[1];
                    $move_to   = $directory . '/' . $sha1 . '.' . $extension;

                    $data_filesystem->move($thumbnail, $move_to);

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
                } catch (FilesystemException | UnableToReadFile | UnableToMoveFile $ex) {
                    // Cannot import the file?
                }

                break;
        }

        return response([]);
    }
}
