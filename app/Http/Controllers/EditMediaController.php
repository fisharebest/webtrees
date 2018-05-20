<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use FilesystemIterator;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Controller for edit forms and responses.
 */
class EditMediaController extends AbstractBaseController {
	const EDIT_RESTRICTIONS    = ['locked'];
	const PRIVACY_RESTRICTIONS = ['none', 'privacy', 'confidential'];

	/**
	 * Add a media file to an existing media object.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function addMediaFile(Request $request): Response {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');
		$xref  = $request->get('xref');
		$media = Media::getInstance($xref, $tree);

		try {
			$this->checkMediaAccess($media);
		} catch (Exception $ex) {
			return new Response(view('modals/error', [
				'title' => I18N::translate('Add a media file'),
				'error' => $ex->getMessage(),
			]));
		}

		return new Response(view('modals/add-media-file', [
			'max_upload_size' => $this->maxUploadFilesize(),
			'media'           => $media,
			'media_types'     => $this->mediaTypes(),
			'unused_files'    => $this->unusedFiles($tree),
		]));
	}

	/**
	 * Add a media file to an existing media object.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function addMediaFileAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');
		$xref  = $request->get('xref');
		$media = Media::getInstance($xref, $tree);
		$title = $request->get('title');
		$type  = $request->get('type');

		// Tidy whitespace
		$type  = trim(preg_replace('/\s+/', ' ', $type));
		$title = trim(preg_replace('/\s+/', ' ', $title));

		if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
		}

		$file = $this->uploadFile($request);

		if ($file === '') {
			FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));

			return new RedirectResponse($media->url());
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
		FunctionsImport::acceptAllChanges($media->getxref(), $tree->getTreeId());

		return new RedirectResponse($media->url());
	}

	/**
	 * Edit an existing media file.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function editMediaFile(Request $request): Response {
		/** @var Tree $tree */
		$tree    = $request->attributes->get('tree');
		$xref    = $request->get('xref', '');
		$fact_id = $request->get('fact_id', '');
		$media   = Media::getInstance($xref, $tree);

		try {
			$this->checkMediaAccess($media);
		} catch (Exception $ex) {
			return new Response(view('modals/error', [
				'title' => I18N::translate('Edit a media file'),
				'error' => $ex->getMessage(),
			]), Response::HTTP_FORBIDDEN);
		}

		foreach ($media->mediaFiles() as $media_file) {
			if ($media_file->factId() === $fact_id) {
				return new Response(view('modals/edit-media-file', [
					'media_file'      => $media_file,
					'max_upload_size' => $this->maxUploadFilesize(),
					'media'           => $media,
					'media_types'     => $this->mediaTypes(),
					'unused_files'    => $this->unusedFiles($tree),
				]));
			}
		}

		return new Response('', Response::HTTP_NOT_FOUND);
	}

	/**
	 * Save an edited media file.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function editMediaFileAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree     = $request->attributes->get('tree');
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
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
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
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
		}

		// We can edit the file as either a URL or a folder/file
		if ($remote !== '') {
			$file = $remote;
		} else {
			$new_file = str_replace('\\', '/', $new_file);
			$folder   = str_replace('\\', '/', $folder);
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

		$MEDIA_DIRECTORY = $media->getTree()->getPreference('MEDIA_DIRECTORY');
		$old             = $MEDIA_DIRECTORY . $media_file->filename();
		$new             = $MEDIA_DIRECTORY . $file;

		// Update the filesystem, if we can.
		if (!$media_file->isExternal()) {
			// Don't overwrite existing file
			if (file_exists(WT_DATA_DIR . $new) && sha1_file(WT_DATA_DIR . $old) !== sha1_file(WT_DATA_DIR . $new)) {
				FlashMessages::addMessage(I18N::translate('The media file %1$s could not be renamed to %2$s.', Html::filename($media_file->filename()), Html::filename($file)), 'info');
				$file = $media_file->filename();
			} else {
				try {
					File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folder);
					rename(WT_DATA_DIR . $MEDIA_DIRECTORY . $media_file->filename(), WT_DATA_DIR . $MEDIA_DIRECTORY . $file);
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
			FunctionsImport::acceptAllChanges($media->getxref(), $tree->getTreeId());
		}

		return new RedirectResponse($media->url());
	}

	/**
	 * Show a form to create a new media object.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function createMediaObject(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		return new Response(view('modals/create-media-object', [
			'max_upload_size' => $this->maxUploadFilesize(),
			'media_types'     => $this->mediaTypes(),
			'unused_files'    => $this->unusedFiles($tree),
		]));
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function createMediaObjectFromFileAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$file  = $request->get('file');
		$type  = $request->get('type');
		$title = $request->get('title');
		$note  = $request->get('note');

		if (preg_match('/\.([a-zA-Z0-9]+)$/', $file, $match)) {
			$format = ' ' . $match[1];
		} else {
			$format = '';
		}

		$gedcom = "0 @new@ OBJE\n1 FILE " . $file . "\n2 FORM " . $format;

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
		FunctionsImport::acceptAllChanges($media_object->getXref(), $tree->getTreeId());

		return new RedirectResponse($media_object->url());
	}

	/**
	 * Process a form to create a new media object.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function createMediaObjectAction(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree                = $request->attributes->get('tree');
		$note                = $request->get('note');
		$title               = $request->get('title');
		$type                = $request->get('type');
		$privacy_restriction = $request->get('privacy-restriction', '');
		$edit_restriction    = $request->get('edit-restriction', '');

		// Tidy whitespace
		$type  = trim(preg_replace('/\s+/', ' ', $type));
		$title = trim(preg_replace('/\s+/', ' ', $title));

		// Convert line endings to GEDDCOM continuations
		$note = str_replace(["\r\n", "\r", "\n"], "\n1 CONT ", $note);

		$file = $this->uploadFile($request);

		if ($file === '') {
			return new JsonResponse(['error_message' => I18N::translate('There was an error uploading your file.')], 406);
		}

		$gedcom = "0 @XREF@ OBJE\n" . $this->createMediaFileGedcom($file, $type, $title);

		if ($note !== '') {
			$gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
		}

		if (in_array($privacy_restriction, self::PRIVACY_RESTRICTIONS)) {
			$gedcom .= "\n1 RESN " . $privacy_restriction;
		}

		if (in_array($edit_restriction, self::EDIT_RESTRICTIONS)) {
			$gedcom .= "\n1 RESN " . $edit_restriction;
		}

		$record = $tree->createRecord($gedcom);

		// Accept the new record to keep the filesystem synchronized with the genealogy.
		FunctionsImport::acceptAllChanges($record->getXref(), $record->getTree()->getTreeId());

		return new JsonResponse([
			'id'   => $record->getXref(),
			'text' => view('selects/media', [
				'media' => $record,
			]),
			'html' => view('modals/record-created', [
				'title' => I18N::translate('The media object has been created'),
				'name'  => $record->getFullName(),
				'url'   => $record->url(),
			]),
		]);
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
	private function createMediaFileGedcom(string $file, string $type, string $title): string {
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
	private function maxUploadFilesize(): string {
		$bytes = UploadedFile::getMaxFilesize();
		$kb    = (int) ($bytes / 1024);

		return I18N::translate('%s KB', I18N::number($kb));
	}

	/**
	 * A list of key/value options for media types.
	 *
	 * @param string $current
	 *
	 * @return array
	 */
	private function mediaTypes($current = ''): array {
		$media_types = GedcomTag::getFileFormTypes();

		$media_types = ['' => ''] + [$current => $current] + $media_types;

		return $media_types;
	}

	/**
	 * Store an uploaded file (or URL), either to be added to a media object
	 * or to create a media object.
	 *
	 * @param Request $request
	 *
	 * @return string The value to be stored in the 'FILE' field of the media object.
	 */
	private function uploadFile(Request $request): string {
		/** @var Tree $tree */
		$tree          = $request->attributes->get('tree');
		$file_location = $request->get('file_location');

		switch ($file_location) {
			case 'url':
				$remote = $request->get('remote');

				if (strpos($remote, '://') !== false) {
					return $remote;
				} else {
					return '';
				}

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

				$uploaded_file = $request->files->get('file');
				if ($uploaded_file === null) {
					return '';
				}

				// The filename
				$new_file = str_replace('\\', '/', $new_file);
				if ($new_file !== '' && strpos($new_file, '/') === false) {
					$file = $new_file;
				} else {
					$file = $uploaded_file->getClientOriginalName();
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
					$extension = $uploaded_file->guessExtension();
					$file      = sha1_file($uploaded_file->getPathname()) . '.' . $extension;
				}

				try {
					//if ($uploaded_file->isValid()) {
					//	$uploaded_file->move(WT_DATA_DIR . $media_folder . $folder, $file);
					if (is_uploaded_file($_FILES['file']['tmp_name'])) {
						move_uploaded_file($_FILES['file']['tmp_name'], WT_DATA_DIR . $media_folder . $folder . $file);

						return $folder . $file;
					}
				} catch (FileException $ex) {
					DebugBar::addThrowable($ex);
				}

				return '';
		}
	}

	/**
	 * A list of media files not already linked to a media object.
	 *
	 * @param Tree $tree
	 *
	 * @return array
	 */
	private function unusedFiles(Tree $tree): array {
		$used_files = Database::prepare(
			"SELECT multimedia_file_refn FROM `##media_file`" .
			" WHERE m_file = :tree_id" .
			" AND multimedia_file_refn NOT LIKE 'http://%' AND multimedia_file_refn NOT LIKE 'https://%'"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchOneColumn();

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

		return array_combine($unused_files, $unused_files);
	}
}
