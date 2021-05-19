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
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
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
     * EditMediaFileAction constructor.
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
     * Save an edited media file.
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

        $fact_id = $request->getAttribute('fact_id');
        assert(is_string($fact_id));

        $data_filesystem = Registry::filesystem()->data();

        $params   = (array) $request->getParsedBody();
        $folder   = $params['folder'] ?? '';
        $new_file = $params['new_file'] ?? '';
        $remote   = $params['remote'] ?? '';
        $title    = $params['title'] ?? '';
        $type     = $params['type'] ?? '';
        $media    = Registry::mediaFactory()->make($xref, $tree);

        // Tidy non-printing characters
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        // Media object oes not exist?  Media object is read-only?
        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route(TreePage::class, ['tree' => $tree->name()]));
        }

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

        $filesystem = $media->tree()->mediaFilesystem($data_filesystem);
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
