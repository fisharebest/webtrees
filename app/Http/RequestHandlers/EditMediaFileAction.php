<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_replace;
use function redirect;
use function route;
use function str_replace;
use function trim;

/**
 * Edit a media file.
 */
class EditMediaFileAction implements RequestHandlerInterface
{
    private MediaFileService $media_file_service;

    private PendingChangesService $pending_changes_service;

    /**
     * @param MediaFileService      $media_file_service
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(MediaFileService $media_file_service, PendingChangesService $pending_changes_service)
    {
        $this->media_file_service      = $media_file_service;
        $this->pending_changes_service = $pending_changes_service;
    }

    /**
     * Save an edited media file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $xref     = Validator::attributes($request)->isXref()->string('xref');
        $fact_id  = Validator::attributes($request)->string('fact_id');
        $folder   = Validator::parsedBody($request)->string('folder');
        $new_file = Validator::parsedBody($request)->string('new_file');
        $remote   = Validator::parsedBody($request)->string('remote');
        $title    = Validator::parsedBody($request)->string('title');
        $type     = Validator::parsedBody($request)->string('type');
        $media    = Registry::mediaFactory()->make($xref, $tree);
        $media    = Auth::checkMediaAccess($media, true);

        $type  = Registry::elementFactory()->make('OBJE:FILE:FORM:TYPE')->canonical($type);
        $title = Registry::elementFactory()->make('OBJE:FILE:TITL')->canonical($title);

        // Find the fact to edit
        $media_file = $media->mediaFiles()
            ->first(static function (MediaFile $media_file) use ($fact_id): bool {
                return $media_file->factId() === $fact_id;
            });

        // Media file does not exist?
        if ($media_file === null) {
            return redirect(route(TreePage::class, ['tree' => $tree->name()]));
        }

        // We can edit the file as either a URL or a folder/file
        if ($remote !== '') {
            $file = $remote;
        } else {
            $new_file = str_replace('\\', '/', $new_file);
            $folder   = str_replace('\\', '/', $folder);
            $folder   = trim($folder, '/');

            if ($folder === '') {
                $file = $new_file;
            } else {
                $file = $folder . '/' . $new_file;
            }
        }

        // Invalid filename?  Do not change it.
        if ($new_file === '') {
            $file = $media_file->filename();
        }

        $filesystem = $media->tree()->mediaFilesystem();
        $old        = $media_file->filename();
        $new        = $file;

        // Update the filesystem, if we can.
        if ($old !== $new && !$media_file->isExternal() && $filesystem->fileExists($old)) {
            try {
                $file_exists = $filesystem->fileExists($old);

                if ($file_exists) {
                    try {
                        $filesystem->move($old, $new);
                        FlashMessages::addMessage(I18N::translate('The media file %1$s has been renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                    } catch (FilesystemException | UnableToMoveFile $ex) {
                        // Don't overwrite existing file
                        FlashMessages::addMessage(I18N::translate('The media file %1$s could not be renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                        $file = $old;
                    }
                }
            } catch (FilesystemException | UnableToRetrieveMetadata $ex) {
                // File does not exist?
            }
        }

        $gedcom = $this->media_file_service->createMediaFileGedcom($file, $type, $title, '');

        $media->updateFact($fact_id, $gedcom, true);

        // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
        if ($old !== $new && !$media_file->isExternal()) {
            $this->pending_changes_service->acceptRecord($media);
        }

        return redirect($media->url());
    }
}
