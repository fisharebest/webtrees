<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsImport;

define('WT_SCRIPT_NAME', 'action.php');
require './includes/session.php';

header('Content-type: text/html; charset=UTF-8');

if (!Filter::checkCsrf()) {
	http_response_code(406);

	return;
}

switch (Filter::post('action')) {
case 'accept-changes':
	// Accept all the pending changes for a record
	$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF), $WT_TREE);
	if ($record && Auth::isModerator($record->getTree()) && $record->canShow() && $record->canEdit()) {
		FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ I18N::translate('The changes to “%s” have been accepted.', $record->getFullName()));
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
			if ($fact->getfactId() == $fact_id) {
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
					$clipboard = array();
				}
				$clipboard[$fact_id] = array(
					'type'    => $type,
					'factrec' => $fact->getGedcom(),
					'fact'    => $fact->getTag(),
					);
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
			if ($fact->getfactId() == $fact_id && $fact->canShow() && $fact->canEdit()) {
				$record->deleteFact($fact_id, true);
				break 2;
			}
		}
	}

	// Can’t find the record/fact, or don’t have permission to delete it.
	http_response_code(406);
	break;

case 'delete-family':
case 'delete-individual':
case 'delete-media':
case 'delete-note':
case 'delete-repository':
case 'delete-source':
	$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF), $WT_TREE);
	if ($record && Auth::isEditor($record->getTree()) && $record->canShow() && $record->canEdit()) {
		// Delete links to this record
		foreach (FunctionsDb::fetchAllLinks($record->getXref(), $record->getTree()->getTreeId()) as $xref) {
			$linker     = GedcomRecord::getInstance($xref, $WT_TREE);
			$old_gedcom = $linker->getGedcom();
			$new_gedcom = FunctionsEdit::removeLinks($old_gedcom, $record->getXref());
			// FunctionsDb::fetch_all_links() does not take account of pending changes.  The links (or even the
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
						$source->updateFact($fact->getFactId(), preg_replace(array('/\n2 OBJE @' . $target . '@(\n[3-9].*)*/', '/\n3 OBJE @' . $target . '@(\n[4-9].*)*/'), '', $fact->getGedcom()), true);
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

case 'theme':
	// Change the current theme
	$theme = Filter::post('theme');
	if (Site::getPreference('ALLOW_USER_THEMES') && array_key_exists($theme, Theme::themeNames())) {
		Session::put('theme_id', $theme);
		// Remember our selection
		Auth::user()->setPreference('theme', $theme);
	} else {
		// Request for a non-existant theme.
		http_response_code(406);
	}
	break;
}
