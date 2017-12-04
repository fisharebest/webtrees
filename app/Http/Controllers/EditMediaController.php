<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\File;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditMediaController extends BaseController {
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

		if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
			return new Response(View::make('modals/error', [
				'title' => I18N::translate('Add a media file to this media object'),
				'error' => I18N::translate('This media object does not exist or you do not have permission to view it.'),
			]));
		}

		return new Response(View::make('modals/add-media-file', [
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

		if ($media === null || $media->isPendingDeletion() || !$media->canEdit()) {
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
		}

		$file = $this->uploadFile($request);

		if ($file === '') {
			FlashMessages::addMessage(I18N::translate('There was an error uploading your file.'));
			return new RedirectResponse($media->getRawUrl());
		}

		$title = $request->get('title');
		$type  = $request->get('type');

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

		return new RedirectResponse($media->getRawUrl());
	}

	/**
	 * Show a form to create a new media object.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function createMediaObject(Request $request): Response {
		$tree = $request->attributes->get('tree');

		return new Response(View::make('modals/create-media-object', [
			'tree'            => $tree,
			'max_upload_size' => $this->maxUploadFilesize(),
			'media_types'     => $this->mediaTypes(),
			'unused_files'    => $this->unusedFiles($tree),
		]));
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
		$tree  = $request->attributes->get('tree');
		$note  = $request->get('note');
		$title = $request->get('title');
		$type  = $request->get('type');

		$privacy_restriction = $request->get('privacy-restriction', '');
		$edit_restriction    = $request->get('edit-restriction', '');

		// Convert line endings to GEDDCOM continuations
		$note = str_replace(["\r\n", "\r", "\n"], "\n1 CONT ", $note);

		$file = $this->uploadFile($request);

		if ($file === '') {
			return new JsonResponse(['error_message' => I18N::translate('There was an error uploading your file.')], 406);
		}

		$gedcom = "0 @XREF@ OBJE\n1 FILE " . $file;
		if ($type !== '') {
			$gedcom .= "\n2 FORM\n3 TYPE " . $type;
		}
		if ($title !== '') {
			$gedcom .= "\n2 TITL " . $title;
		}
		if ($note !== '') {
			$gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
		}

		if (in_array($privacy_restriction, ['none', 'privacy', 'confidential'])) {
			$gedcom .= "\n1 RESN " . $privacy_restriction;
		}

		if (in_array($edit_restriction, ['locked'])) {
			$gedcom .= "\n1 RESN " . $edit_restriction;
		}

		$record = $tree->createRecord($gedcom);

		// Accept the new record to keep the filesystem synchronized with the genealogy.
		FunctionsImport::acceptAllChanges($record->getXref(), $record->getTree()->getTreeId());

		return new JsonResponse([
			'id' => $record->getXref(),
			'text' => View::make('selects/media', [
				'media' => $record,
			]),
			'html' => view('modals/record-created', [
				'title' => I18N::translate('The media object has been created'),
				'name'  => $record->getFullName(),
				'url'   => $record->getRawUrl(),
			])
		]);
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
		$iter       = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($media_dir));

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
