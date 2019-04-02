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
use FilesystemIterator;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use InvalidArgumentException;
use function pathinfo;
use const PATHINFO_EXTENSION;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;
use const UPLOAD_ERR_OK;

/**
 * Controller for edit forms and responses.
 */
class EditMediaController extends AbstractEditController
{
    private const EDIT_RESTRICTIONS = [
        'locked',
    ];

    private const PRIVACY_RESTRICTIONS = [
        'none',
        'privacy',
        'confidential',
    ];

    /**
     * Add a media file to an existing media object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function addMediaFile(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref  = $request->get('xref', '');
        $media = Media::getInstance($xref, $tree);

        try {
            Auth::checkMediaAccess($media);
        } catch (Exception $ex) {
            return response(view('modals/error', [
                'title' => I18N::translate('Add a media file'),
                'error' => $ex->getMessage(),
            ]));
        }

        return response(view('modals/add-media-file', [
            'max_upload_size' => $this->maxUploadFilesize(),
            'media'           => $media,
            'media_types'     => $this->mediaTypes(),
            'unused_files'    => $this->unusedFiles($tree),
        ]));
    }

    /**
     * Add a media file to an existing media object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function addMediaFileAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref  = $request->get('xref', '');
        $media = Media::getInstance($xref, $tree);
        $title = $request->get('title');
        $type  = $request->get('type');

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route('tree-page', ['ged' => $tree->name()]));
        }

        $file = $this->uploadFile($request, $tree);

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
        FunctionsImport::acceptAllChanges($media->xref(), $tree);

        return redirect($media->url());
    }

    /**
     * Edit an existing media file.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function editMediaFile(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref    = $request->get('xref', '');
        $fact_id = $request->get('fact_id', '');
        $media   = Media::getInstance($xref, $tree);

        try {
            Auth::checkMediaAccess($media);
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
                    'max_upload_size' => $this->maxUploadFilesize(),
                    'media'           => $media,
                    'media_types'     => $this->mediaTypes(),
                    'unused_files'    => $this->unusedFiles($tree),
                ]));
            }
        }

        return response('', StatusCodeInterface::STATUS_NOT_FOUND);
    }

    /**
     * Save an edited media file.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function editMediaFileAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref     = $request->get('xref', '');
        $fact_id  = $request->get('fact_id', '');
        $folder   = $request->get('folder', '');
        $new_file = $request->get('new_file', '');
        $remote   = $request->get('remote', '');
        $title    = $request->get('title', '');
        $type     = $request->get('type', '');
        $media    = Media::getInstance($xref, $tree);

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        // Media object oes not exist?  Media object is read-only?
        if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
            return redirect(route('tree-page', ['ged' => $tree->name()]));
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
            return redirect(route('tree-page', ['ged' => $tree->name()]));
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
            if (strpos($file, '../') !== false) {
                $file = '';
            }
        }

        // Invalid filename?  Do not change it.
        if ($file === '') {
            $file = $media_file->filename();
        }

        $MEDIA_DIRECTORY = $media->tree()->getPreference('MEDIA_DIRECTORY');
        $old             = $MEDIA_DIRECTORY . $media_file->filename();
        $new             = $MEDIA_DIRECTORY . $file;

        // Update the filesystem, if we can.
        if ($old !== $new && !$media_file->isExternal()) {
            // Don't overwrite existing file
            if (file_exists(WT_DATA_DIR . $new) && file_exists(WT_DATA_DIR . $old)) {
                FlashMessages::addMessage(I18N::translate('The media file %1$s could not be renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                $file = $media_file->filename();
            } else {
                try {
                    // The "old" file may not exist.  For example, if the file was renamed on disk,
                    // and we are now renaming the GEDCOM data to match.
                    if (file_exists(WT_DATA_DIR . $old)) {
                        File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folder);
                        rename(WT_DATA_DIR . $old, WT_DATA_DIR . $new);
                    }
                    FlashMessages::addMessage(I18N::translate('The media file %1$s has been renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                } catch (Throwable $ex) {
                    FlashMessages::addMessage($ex, 'info');
                    FlashMessages::addMessage(I18N::translate('The media file %1$s could not be renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
                    $file = $media_file->filename();
                }
            }
        }

        $gedcom = $this->createMediaFileGedcom($file, $type, $title);

        $media->updateFact($fact_id, $gedcom, true);

        // Accept the changes, to keep the filesystem in sync with the GEDCOM data.
        if ($old !== $new && !$media_file->isExternal()) {
            FunctionsImport::acceptAllChanges($media->xref(), $tree);
        }

        return redirect($media->url());
    }

    /**
     * Show a form to create a new media object.
     *
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function createMediaObject(Tree $tree): ResponseInterface
    {
        return response(view('modals/create-media-object', [
            'max_upload_size' => $this->maxUploadFilesize(),
            'media_types'     => $this->mediaTypes(),
            'unused_files'    => $this->unusedFiles($tree),
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function createMediaObjectFromFileAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $file  = $request->get('file');
        $type  = $request->get('type');
        $title = $request->get('title');
        $note  = $request->get('note');

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
        FunctionsImport::acceptAllChanges($media_object->xref(), $tree);

        return redirect($media_object->url());
    }

    /**
     * Process a form to create a new media object.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function createMediaObjectAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $note                = $request->get('note');
        $title               = $request->get('title');
        $type                = $request->get('type');
        $privacy_restriction = $request->get('privacy-restriction', '');
        $edit_restriction    = $request->get('edit-restriction', '');

        // Tidy whitespace
        $type  = trim(preg_replace('/\s+/', ' ', $type));
        $title = trim(preg_replace('/\s+/', ' ', $title));

        // Convert line endings to GEDDCOM continuations
        $note = str_replace([
            "\r\n",
            "\r",
            "\n",
        ], "\n1 CONT ", $note);

        $file = $this->uploadFile($request, $tree);

        if ($file === '') {
            return response(['error_message' => I18N::translate('There was an error uploading your file.')], 406);
        }

        $gedcom = "0 @@ OBJE\n" . $this->createMediaFileGedcom($file, $type, $title);

        if ($note !== '') {
            $gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
        }

        if (in_array($privacy_restriction, self::PRIVACY_RESTRICTIONS)) {
            $gedcom .= "\n1 RESN " . $privacy_restriction;
        }

        if (in_array($edit_restriction, self::EDIT_RESTRICTIONS)) {
            $gedcom .= "\n1 RESN " . $edit_restriction;
        }

        $record = $tree->createMediaObject($gedcom);

        // Accept the new record to keep the filesystem synchronized with the genealogy.
        FunctionsImport::acceptAllChanges($record->xref(), $record->tree());

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
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function linkMediaToIndividual(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-individual', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function linkMediaToFamily(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-family', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function linkMediaToSource(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $media = Media::getInstance($xref, $tree);

        return response(view('modals/link-media-to-source', [
            'media' => $media,
            'tree'  => $tree,
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function linkMediaToRecordAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = $request->get('xref', '');
        $link = $request->get('link', '');

        $media  = Media::getInstance($xref, $tree);
        $record = GedcomRecord::getInstance($link, $tree);

        $record->createFact('1 OBJE @' . $xref . '@', true);

        return redirect($media->url());
    }

    /**
     * Convert the media file attributes into GEDCOM format.
     *
     * @param string $file
     * @param string $type
     * @param string $title
     *
     * @return string
     */
    private function createMediaFileGedcom(string $file, string $type, string $title): string
    {
        if (preg_match('/\.([a-z0-9]+)/i', $file, $match)) {
            $extension = strtolower($match[1]);
            $extension = str_replace('jpg', 'jpeg', $extension);
            $extension = ' ' . $extension;
        } else {
            $extension = '';
        }

        $gedcom = '1 FILE ' . $file;
        if ($type !== '') {
            $gedcom .= "\n2 FORM" . $extension . "\n3 TYPE " . $type;
        }
        if ($title !== '') {
            $gedcom .= "\n2 TITL " . $title;
        }

        return $gedcom;
    }

    /**
     * What is the largest file a user may upload?
     */
    private function maxUploadFilesize(): string
    {
        $bytes = UploadedFile::getMaxFilesize();
        $kb    = intdiv($bytes + 1023, 1024);

        return I18N::translate('%s KB', I18N::number($kb));
    }

    /**
     * A list of key/value options for media types.
     *
     * @param string $current
     *
     * @return array
     */
    private function mediaTypes($current = ''): array
    {
        $media_types = GedcomTag::getFileFormTypes();

        $media_types = ['' => ''] + [$current => $current] + $media_types;

        return $media_types;
    }

    /**
     * Store an uploaded file (or URL), either to be added to a media object
     * or to create a media object.
     *
     * @param ServerRequestInterface $request
     * @param Tree    $tree
     *
     * @return string The value to be stored in the 'FILE' field of the media object.
     */
    private function uploadFile(ServerRequestInterface $request, Tree $tree): string
    {
        $file_location = $request->get('file_location');

        switch ($file_location) {
            case 'url':
                $remote = $request->get('remote');

                if (strpos($remote, '://') !== false) {
                    return $remote;
                }

                return '';

            case 'unused':
                $unused = $request->get('unused');
                $unused = str_replace('\\', '/', $unused);

                if (strpos($unused, '../') !== false) {
                    return '';
                }

                return $unused;

            case 'upload':
            default:
                $media_folder = $tree->getPreference('MEDIA_DIRECTORY');
                $folder       = $request->get('folder', '');
                $auto         = $request->get('auto', '0');
                $new_file     = $request->get('new_file', '');

                /** @var UploadedFileInterface|null $uploaded_file */
                $uploaded_file = $request->getUploadedFiles()['file'];
                if ($uploaded_file === null || $uploaded_file->getError() !== UPLOAD_ERR_OK) {
                    return '';
                }

                // The filename
                $new_file = str_replace('\\', '/', $new_file);
                if ($new_file !== '' && strpos($new_file, '/') === false) {
                    $file = $new_file;
                } else {
                    $file = $uploaded_file->getClientFilename();
                }

                // The folder
                $folder = str_replace('\\', '/', $folder);
                $folder = trim($folder, '/');
                if ($folder !== '') {
                    $folder .= '/';
                }

                // Invalid path?
                if (strpos($folder, '../') !== false || !File::mkdir(WT_DATA_DIR . $media_folder . $folder)) {
                    $auto = '1';
                }

                // Generate a unique name for the file?
                if ($auto === '1' || file_exists(WT_DATA_DIR . $media_folder . $folder . $file)) {
                    $folder    = '';
                    $extension = pathinfo($uploaded_file->getClientFilename(), PATHINFO_EXTENSION);
                    $file      = sha1_file((string) $uploaded_file->getStream()) . '.' . $extension;
                }

                try {
                    $uploaded_file->moveTo(WT_DATA_DIR . $media_folder . $folder . $file);

                    return $folder . $file;
                } catch (RuntimeException | InvalidArgumentException $ex) {
                    FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

                    return '';
                }
        }
    }

    /**
     * A list of media files not already linked to a media object.
     *
     * @param Tree $tree
     *
     * @return array
     */
    private function unusedFiles(Tree $tree): array
    {
        $used_files = DB::table('media_file')
            ->where('m_file', '=', $tree->id())
            ->where('multimedia_file_refn', 'NOT LIKE', 'http://%')
            ->where('multimedia_file_refn', 'NOT LIKE', 'https://%')
            ->pluck('multimedia_file_refn')
            ->all();

        $disk_files = [];
        $media_dir  = WT_DATA_DIR . $tree->getPreference('MEDIA_DIRECTORY', 'media/');
        $iter       = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($media_dir, FilesystemIterator::FOLLOW_SYMLINKS));

        foreach ($iter as $file) {
            if ($file->isFile()) {
                $filename = substr($file->getPathname(), strlen($media_dir));
                // Older versions of webtrees used a couple of special folders.
                if (strpos($filename, 'thumbs/') !== 0 && strpos($filename, 'watermarks/') !== 0) {
                    $disk_files[] = $filename;
                }
            }
        }

        $unused_files = array_diff($disk_files, $used_files);

        sort($unused_files);

        return array_combine($unused_files, $unused_files);
    }
}
