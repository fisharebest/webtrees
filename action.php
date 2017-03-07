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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsImport;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

if (!Filter::checkCsrf()) {
	http_response_code(406);

	return;
}

switch (Filter::post('action', null, Filter::get('action'))) {
case 'accept-changes':
	// Accept all the pending changes for a record
	$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF), $WT_TREE);
	if ($record && Auth::isModerator($record->getTree()) && $record->canShow() && $record->canEdit()) {
		if ($record->isPendingDeletion()) {
			FlashMessages::addMessage(/* I18N: %s is the name of a genealogy record */
				I18N::translate('“%s” has been deleted.', $record->getFullName()));
		} else {
			FlashMessages::addMessage(/* I18N: %s is the name of a genealogy record */
				I18N::translate('The changes to “%s” have been accepted.', $record->getFullName()));
		}
		FunctionsImport::acceptAllChanges($record->getXref(), $record->getTree()->getTreeId());
	} else {
		http_response_code(406);
	}
	break;

case 'copy-fact':
	// Copy a fact to the clipboard
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$fact_id = Filter::post('fact_id');

	$record = GedcomRecord::getInstance($xref, $WT_TREE);

	if ($record && $record->canEdit()) {
		foreach ($record->getFacts() as $fact) {
			if ($fact->getFactId() == $fact_id) {
				switch ($fact->getTag()) {
				case 'NOTE':
				case 'SOUR':
				case 'OBJE':
					$type = 'all'; // paste this anywhere
					break;
				default:
					$type = $record::RECORD_TYPE; // paste only to the same record type
					break;
				}
				$clipboard = Session::get('clipboard');
				if (!is_array($clipboard)) {
					$clipboard = [];
				}
				$clipboard[$fact_id] = [
					'type'    => $type,
					'factrec' => $fact->getGedcom(),
					'fact'    => $fact->getTag(),
					];
				// The clipboard only holds 10 facts
				while (count($clipboard) > 10) {
					array_shift($clipboard);
				}
				Session::put('clipboard', $clipboard);
				FlashMessages::addMessage(I18N::translate('The record has been copied to the clipboard.'));
				break 2;
			}
		}
	}
	break;

case 'create-media-object':
	$MEDIA_DIRECTORY = $WT_TREE->getPreference('MEDIA_DIRECTORY');

	// Create a media object, and return parameters needed by Select2
	header('Content-type: application/json');
	$filename   = 'eek.jpg';
	$auto   = Filter::post('auto');
	$folder = Filter::post('folder');
	$note   = Filter::post('note');
	$type   = Filter::post('type');
	$title  = Filter::post('title');

	// No file uploaded?
	if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
		http_response_code(406);
		break;
	}

	// The filename
	$file = $_FILES['file']['name'];
	$format = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	if ($format === 'jpg') {
		$format = 'jpeg';
	}

	// The folder
	$folder = trim($folder, '/');

	// If the folder is invalid, or doesn't exist and can't be created, then use the root folder
	if (strpos($folder, '..') !== false || !File::mkdir(WT_DATA_DIR . $MEDIA_DIRECTORY . $folder)) {
		$folder = '';
	}

	// Generate a unique name for the file?
	if ($auto === '1' || file_exists(WT_DATA_DIR . $MEDIA_DIRECTORY . $folder . $file)) {
		$folder = '';
		$file   = sha1($_FILES['file']['tmp_name']) . $format;
	}

	if ($folder !== '') {
		$folder .= '/';
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], WT_DATA_DIR . $MEDIA_DIRECTORY . $folder . $file)) {
		Log::addMediaLog('Media file ' . $_FILES['file']['name'] . ' uploaded');
	} else {
		FlashMessages::addMessage(
			I18N::translate('There was an error uploading your file.') .
			'<br>' .
			Functions::fileUploadErrorText($_FILES['mediafile']['error'])
		);
		break;
	}


	$gedcom = "0 @new@ OBJE\n1 FILE " . $folder . $file . "\n2 FORM " .  $format;
	if ($type !== '') {
		$gedcom .= "\n3 TYPE " . $type;
	}
	if ($title !== '') {
		$gedcom .= "\n2 TITL " . $title;
	}
	if ($note !== '') {
		$gedcom .= "\n1 NOTE " . preg_replace('/\r?\n/', "\n2 CONT ", $note);
	}
	$media_object = $WT_TREE->createRecord($gedcom);
	// Accept the new record.  Rejecting it would leave the filesystem out-of-sync with the genealogy
	FunctionsImport::acceptAllChanges($media_object->getXref(), $media_object->getTree()->getTreeId());
	echo json_encode(['id' => $media_object->getXref(), 'text' => Select2::mediaObjectValue($media_object)]);
	break;

case 'create-note-object':
	// Create a note, and return parameters needed by Select2
	header('Content-type: application/json');
	$note        = Filter::post('note');
	$gedcom      = "0 @new@ NOTE " . str_replace("\n", "\n1 CONT ", $note);
	$note_object = $WT_TREE->createRecord($gedcom);
	echo json_encode(['id' => $note_object->getXref(), 'text' => Select2::noteValue($note_object)]);
	break;

case 'create-repository':
	// Create a repository, and return parameters needed by Select2
	header('Content-type: application/json');
	$repository_name = Filter::post('repository_name');
	$gedcom = "0 @new@ REPO\n1 NAME " . $repository_name;
	$repository = $WT_TREE->createRecord($gedcom);
	echo json_encode(['id' => $repository->getXref(), 'text' => Select2::repositoryValue($repository)]);
	break;

case 'create-source':
	// Create a source, and return parameters needed by Select2
	header('Content-type: application/json');
	$source_title = Filter::post('source_title');
	$gedcom       = "0 @new@ SOUR\n1 TITL " . $source_title;
	$source       = $WT_TREE->createRecord($gedcom);
	echo json_encode(['id' => $source->getXref(), 'text' => Select2::sourceValue($source)]);
	break;

case 'create-submitter':
	// Create a submitter, and return parameters needed by Select2
	header('Content-type: application/json');
	$gedcom = '0 @new@ SUBM';
	$submitter_name = Filter::post('submitter_name', null, '');
	if ($submitter_name !== '') {
		$gedcom .= "\n1 NAME " . $submitter_name;
	}
	$submitter_address = Filter::post('submitter_address', null, '');
	if ($submitter_address !== '') {
		$gedcom .= "\n1 ADDR " . $submitter_address;
	}
	$submitter = $WT_TREE->createRecord($gedcom);
	echo json_encode(['id' => $submitter->getXref(), 'text' => Select2::submitterValue($submitter)]);
	break;

case 'paste-fact':
	// Paste a fact from the clipboard
	$xref      = Filter::post('xref', WT_REGEX_XREF);
	$fact_id   = Filter::post('fact_id');
	$record    = GedcomRecord::getInstance($xref, $WT_TREE);
	$clipboard = Session::get('clipboard');

	if ($record && $record->canEdit() && isset($clipboard[$fact_id])) {
		$record->createFact($clipboard[$fact_id]['factrec'], true);
	}
	break;

case 'delete-fact':
	$xref    = Filter::post('xref', WT_REGEX_XREF);
	$fact_id = Filter::post('fact_id');

	$record = GedcomRecord::getInstance($xref, $WT_TREE);
	if ($record && $record->canShow() && $record->canEdit()) {
		foreach ($record->getFacts() as $fact) {
			if ($fact->getFactId() == $fact_id && $fact->canShow() && $fact->canEdit()) {
				$record->deleteFact($fact_id, true);
				break 2;
			}
		}
	}

	// Can’t find the record/fact, or don’t have permission to delete it.
	http_response_code(406);
	break;

case 'delete-record':
	$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF), $WT_TREE);
	if ($record && Auth::isEditor($record->getTree()) && $record->canShow() && $record->canEdit()) {
		// Delete links to this record
		foreach (FunctionsDb::fetchAllLinks($record->getXref(), $record->getTree()->getTreeId()) as $xref) {
			$linker     = GedcomRecord::getInstance($xref, $WT_TREE);
			$old_gedcom = $linker->getGedcom();
			$new_gedcom = FunctionsEdit::removeLinks($old_gedcom, $record->getXref());
			// FunctionsDb::fetch_all_links() does not take account of pending changes. The links (or even the
			// record itself) may have already been deleted.
			if ($old_gedcom !== $new_gedcom) {
				// If we have removed a link from a family to an individual, and it has only one member
				if (preg_match('/^0 @' . WT_REGEX_XREF . '@ FAM/', $new_gedcom) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . WT_REGEX_XREF . ')@/', $new_gedcom, $match) == 1) {
					// Delete the family
					$family = GedcomRecord::getInstance($xref, $WT_TREE);
					FlashMessages::addMessage(/* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */ I18N::translate('The family “%s” has been deleted because it only has one member.', $family->getFullName()));
					$family->deleteRecord();
					// Delete any remaining link to this family
					if ($match) {
						$relict     = GedcomRecord::getInstance($match[2][0], $WT_TREE);
						$new_gedcom = $relict->getGedcom();
						$new_gedcom = FunctionsEdit::removeLinks($new_gedcom, $linker->getXref());
						$relict->updateRecord($new_gedcom, false);
						FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $relict->getFullName(), $family->getFullName()));
					}
				} else {
					// Remove links from $linker to $record
					FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $linker->getFullName(), $record->getFullName()));
					$linker->updateRecord($new_gedcom, false);
				}
			}
		}
		// Delete the record itself
		$record->deleteRecord();
	} else {
		http_response_code(406);
	}
	break;

case 'delete-user':
	$user = User::find(Filter::postInteger('user_id'));

	if ($user && Auth::isAdmin() && Auth::user() !== $user) {
		Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
		$user->delete();
	}
	break;

case 'language':
	// Change the current language
	$language = Filter::post('language');
	try {
		I18N::init($language);
		Session::put('locale', $language);
		// Remember our selection
		Auth::user()->setPreference('language', $language);
	} catch (\Exception $ex) {
		// Request for a non-existant language.
		http_response_code(406);
	}
	break;

case 'masquerade':
	$user = User::find(Filter::postInteger('user_id'));

	if ($user && Auth::isAdmin() && Auth::user() !== $user) {
		Log::addAuthenticationLog('Masquerade as user: ' . $user->getUserName());
		Auth::login($user);
		Session::put('masquerade', '1');
	} else {
		http_response_code(406);
	}
	break;

case 'unlink-media':
	// Remove links from an individual and their spouse-family records to a media object.
	// Used by the "unlink" option on the album (lightbox) tab.
	$source = Individual::getInstance(Filter::post('source', WT_REGEX_XREF), $WT_TREE);
	$target = Filter::post('target', WT_REGEX_XREF);
	if ($source && $source->canShow() && $source->canEdit() && $target) {
		// Consider the individual and their spouse-family records
		$sources   = $source->getSpouseFamilies();
		$sources[] = $source;
		foreach ($sources as $source) {
			foreach ($source->getFacts() as $fact) {
				if (!$fact->isPendingDeletion()) {
					if ($fact->getValue() == '@' . $target . '@') {
						// Level 1 links
						$source->deleteFact($fact->getFactId(), true);
					} elseif (strpos($fact->getGedcom(), ' @' . $target . '@')) {
						// Level 2-3 links
						$source->updateFact($fact->getFactId(), preg_replace(['/\n2 OBJE @' . $target . '@(\n[3-9].*)*/', '/\n3 OBJE @' . $target . '@(\n[4-9].*)*/'], '', $fact->getGedcom()), true);
					}
				}
			}
		}
	} else {
		http_response_code(406);
	}
	break;

case 'reject-changes':
	// Reject all the pending changes for a record
	$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF), $WT_TREE);
	if ($record && $record->canEdit() && Auth::isModerator($record->getTree())) {
		FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ I18N::translate('The changes to “%s” have been rejected.', $record->getFullName()));
		FunctionsImport::rejectAllChanges($record);
	} else {
		http_response_code(406);
	}
	break;

case 'select2-family':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::familySearch($WT_TREE, $page, $query));
	break;

case 'select2-individual':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::individualSearch($WT_TREE, $page, $query));
	break;

case 'select2-media':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::mediaObjectSearch($WT_TREE, $page, $query));
	break;

case 'select2-note':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::noteSearch($WT_TREE, $page, $query));
	break;

case 'select2-place':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::placeSearch($WT_TREE, $page, $query, true));
	break;

case 'select2-repository':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::repositorySearch($WT_TREE, $page, $query));
	break;

case 'select2-source':
	$page  = Filter::postInteger('page');
	$query = Filter::post('q', null, '');
	header('Content-Type: application/json');
	echo json_encode(Select2::sourceSearch($WT_TREE, $page, $query));
	break;

case 'theme':
	// Change the current theme
	$theme = Filter::post('theme');
	if (Site::getPreference('ALLOW_USER_THEMES') === '1' && array_key_exists($theme, Theme::themeNames())) {
		Session::put('theme_id', $theme);
		// Remember our selection
		Auth::user()->setPreference('theme', $theme);
	} else {
		// Request for a non-existant theme.
		http_response_code(406);
	}
	break;
}
