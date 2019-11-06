<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Controller for edit forms and responses.
 */
class EditMediaController extends AbstractEditController
{
    /** @var MediaFileService */
    private $media_file_service;

    /** @var PendingChangesService */
    private $pending_changes_service;

    /**
     * EditMediaController constructor.
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
    public function addMediaFile(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $xref  = $request->getQueryParams()['xref'];
        $media = Media::getInstance($xref, $tree);

        try {
            $media = Auth::checkMediaAccess($media);
        } catch (Exception $ex) {
            return response(view('modals/error', [
                'title' => I18N::translate('Add a media file'),
                'error' => $ex->getMessage(),
            ]));
        }

        return response(view('modals/add-media-file', [
            'max_upload_size' => $this->media_file_service->maxUploadFilesize(),
            'media'           => $media,
            'media_types'     => $this->media_file_service->mediaTypes(),
            'unused_files'    => $this->media_file_service->unusedFiles($tree, $data_filesystem),
        ]));
    }

    /**
     * Add a media file to an existing media object.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addMediaFileAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref  = $request->getQueryParams()['xref'];
        $media = Media::getInstance($xref, $tree);
        $title = $request->getParsedBody()['title'];
        $type  = $request->getParsedBody()['type'];

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route('tree-page', ['tree' => $tree->name()]));
        }

        $file = $this->media_file_service->uploadFile($request);

        if ($file === '') {
            FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

            return redirect($media->url());
        }

        $gedcom = '1 FILE ' . $file;
        if ($type !== '') {
            $gedcom .= "\n2 FORM\n3 TYPE " . $type;
        }
        if ($title !== '') {
            $gedcom .= "\n2 TITL " . $title;
        }

        $media->createFact($gedcom, true);

        // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
        $this->pending_changes_service->acceptRecord($media);

        return redirect($media->url());
    }

    /**
     * Edit an existing media file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editMediaFile(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $params  = $request->getQueryParams();
        $xref    = $params['xref'];
        $fact_id = $params['fact_id'];
        $media   = Media::getInstance($xref, $tree);

        try {
            $media = Auth::checkMediaAccess($media);
        } catch (Exception $ex) {
            return response(view('modals/error', [
                'title' => I18N::translate('Edit a media file'),
                'error' => $ex->getMessage(),
            ]), StatusCodeInterface::STATUS_FORBIDDEN);
        }

        foreach ($media->mediaFiles() as $media_file) {
            if ($media_file->factId() === $fact_id) {
                return response(view('modals/edit-media-file', [
                    'media_file'      => $media_file,
                    'max_upload_size' => $this->media_file_service->maxUploadFilesize(),
                    'media'           => $media,
                    'media_types'     => $this->media_file_service->mediaTypes(),
                    'unused_files'    => $this->media_file_service->unusedFiles($tree, $data_filesystem),
                    'tree'            => $tree,
                ]));
            }
        }

        return response('', StatusCodeInterface::STATUS_NOT_FOUND);
    }

    /**
     * Save an edited media file.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editMediaFileAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $xref     = $request->getQueryParams()['xref'];
        $fact_id  = $request->getQueryParams()['fact_id'];
        $folder   = $request->getParsedBody()['folder'];
        $new_file = $request->getParsedBody()['new_file'];
        $remote   = $request->getParsedBody()['remote'];
        $title    = $request->getParsedBody()['title'];
        $type     = $request->getParsedBody()['type'];
        $media    = Media::getInstance($xref, $tree);

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        // Media object oes not exist?  Media object is read-only?
        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route('tree-page', ['tree' => $tree->name()]));
        }

        // Find the fact we are editing.
        $media_file = null;
        foreach ($media->mediaFiles() as $tmp) {
            if ($tmp->factId() === $fact_id) {
                $media_file = $tmp;
            }
        }

        // Media file does not exist?
        if ($media_file === null) {
            return redirect(route('tree-page', ['tree' => $tree->name()]));
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
        if ($old !== $new && !$media_file->isExternal()) {
            try {
                $new = Util::normalizePath($new);
                $filesystem->rename($old, $new);
                FlashMessages::addMessage(I18N::translate('The media file %1$s has been renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
            } catch (FileNotFoundException $ex) {
                // The "old" file may not exist.  For example, if the file was renamed on disk,
                // and we are now renaming the GEDCOM data to match.
            } catch (FileExistsException $ex) {
                // Don't overwrite existing file
                FlashMessages::addMessage(I18N::translate('The media file %1$s could not be renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                $file = $old;
            }
        }

        $gedcom = $this->media_file_service->createMediaFileGedcom($file, $type, $title);

        $media->updateFact($fact_id, $gedcom, true);

        // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
        if ($old !== $new && !$media_file->isExternal()) {
            $this->pending_changes_service->acceptRecord($media);
        }

        return redirect($media->url());
    }

    /**
     * Show a form to create a new media object.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createMediaObject(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        return response(view('modals/create-media-object', [
            'max_upload_size' => $this->media_file_service->maxUploadFilesize(),
            'media_types'     => $this->media_file_service->mediaTypes(),
            'unused_files'    => $this->media_file_service->unusedFiles($tree, $data_filesystem),
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createMediaObjectFromFileAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = $request->getParsedBody();
        $file   = $params['file'];
        $type   = $params['type'];
        $title  = $params['title'];
        $note   = $params['note'];

        if (preg_match('/\.([a-zA-Z0-9]+)$/', $file, $match)) {
            $format = ' ' . $match[1];
        } else {
            $format = '';
        }

        $gedcom = "0 @@ OBJE\n1 FILE " . $file . "\n2 FORM " . $format;

        if ($type !== '') {
            $gedcom .= "\n3 TYPE " . $type;
        }

        if ($title !== '') {
            $gedcom .= "\n2 TITL " . $title;
        }

        if ($note !== '') {
            $gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
        }

        $media_object = $tree->createRecord($gedcom);
        // Accept the new record.  Rejecting it would leave the filesystem out-of-sync with the genealogy
        $this->pending_changes_service->acceptRecord($media_object);

        return redirect($media_object->url());
    }

    /**
     * Process a form to create a new media object.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createMediaObjectAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params              = $request->getParsedBody();
        $note                = $params['media-note'];
        $title               = $params['title'];
        $type                = $params['type'];
        $privacy_restriction = $params['privacy-restriction'];
        $edit_restriction    = $params['edit-restriction'];

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        // Convert line endings to GEDDCOM continuations
        $note = str_replace([
            "\r\n",
            "\r",
            "\n",
        ], "\n1 CONT ", $note);

        $file = $this->media_file_service->uploadFile($request);

        if ($file === '') {
            return response(['error_message' => I18N::translate('There was an error uploading your file.')], 406);
        }

        $gedcom = "0 @@ OBJE\n" . $this->media_file_service->createMediaFileGedcom($file, $type, $title);

        if ($note !== '') {
            $gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
        }

        if (in_array($privacy_restriction, $this->media_file_service::PRIVACY_RESTRICTIONS, true)) {
            $gedcom .= "\n1 RESN " . $privacy_restriction;
        }

        if (in_array($edit_restriction, $this->media_file_service::EDIT_RESTRICTIONS, true)) {
            $gedcom .= "\n1 RESN " . $edit_restriction;
        }

        $record = $tree->createMediaObject($gedcom);

        // Accept the new record to keep the filesystem synchronized with the genealogy.
        $this->pending_changes_service->acceptRecord($record);

        return response([
            'id'   => $record->xref(),
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function linkMediaToIndividual(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-individual', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function linkMediaToFamily(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-family', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function linkMediaToSource(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getQueryParams()['xref'];

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-source', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function linkMediaToRecordAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = $request->getParsedBody();
        $xref   = $params['xref'];
        $link   = $params['link'];

        $media  = Media::getInstance($xref, $tree);
        $record = GedcomRecord::getInstance($link, $tree);

        $record->createFact('1 OBJE @' . $xref . '@', true);

        return redirect($media->url());
    }
}
