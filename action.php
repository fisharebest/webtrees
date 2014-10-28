<?php
// Perform an action, as an AJAX request.
//
// It is bad design to put actions in GET parameters (because
// reloading the page will execute the action again) or POST
// parameters (because it effectively disables the "back" button).
//
// It also means we must hide such links from search engines,
// which frequently penalize sites that generate different
// content for browsers/robots.
//
// Instead, use an AJAX request, such as
//
// <a href="#" onclick="jQuery.post('action.php',{action='foo',p1='bar'}, function(){location.reload()});">click-me!</a>
// <a href="#" onclick="jQuery.post('action.php',{action='foo',p1='bar'}).success(location.reload()).error(alert('failed'));">click-me!</a>
//
// Most actions will not need separate success() and error().
// Typically this may occur if an action has already been submitted, or
// the login session has expired.  In these cases, reloading the page is
// the correct response for both success/error.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\Log;
use WT\User;

define('WT_SCRIPT_NAME', 'action.php');
require './includes/session.php';

header('Content-type: text/html; charset=UTF-8');


if (!WT_Filter::checkCsrf()) {
	Zend_Session::writeClose();
	header('HTTP/1.0 406 Not Acceptable');
	exit;
}

switch (WT_Filter::post('action')) {
case 'accept-changes':
	// Accept all the pending changes for a record
	$record = WT_GedcomRecord::getInstance(WT_Filter::post('xref', WT_REGEX_XREF));
	if ($record && WT_USER_CAN_ACCEPT && $record->canShow() && $record->canEdit()) {
		WT_FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ WT_I18N::translate('The changes to “%s” have been accepted.', $record->getFullName()));
		accept_all_changes($record->getXref(), $record->getGedcomId());
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'copy-fact':
	// Copy a fact to the clipboard
	require WT_ROOT.'includes/functions/functions_edit.php';
	$xref    = WT_Filter::post('xref', WT_REGEX_XREF);
	$fact_id = WT_Filter::post('fact_id');

	$record = WT_GedcomRecord::getInstance($xref);

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
				if (!is_array($WT_SESSION->clipboard)) {
					$WT_SESSION->clipboard=array();
				}
				$WT_SESSION->clipboard[$fact_id]=array(
					'type'   =>$type,
					'factrec'=>$fact->getGedcom(),
					'fact'   =>$fact->getTag()
					);
				// The clipboard only holds 10 facts
				while (count($WT_SESSION->clipboard)>10) {
					array_shift($WT_SESSION->clipboard);
				}
				WT_FlashMessages::addMessage(WT_I18N::translate('The record was copied to the clipboard.'));
				break 2;
			}
		}
	}
	break;

case 'paste-fact':
	// Paste a fact from the clipboard
	require WT_ROOT.'includes/functions/functions_edit.php';
	$xref    = WT_Filter::post('xref', WT_REGEX_XREF);
	$fact_id = WT_Filter::post('fact_id');

	$record = WT_GedcomRecord::getInstance($xref);

	if ($record && $record->canEdit() && isset($WT_SESSION->clipboard[$fact_id])) {
		$record->createFact($WT_SESSION->clipboard[$fact_id]['factrec'], true);
	}
	break;

case 'delete-fact':
	require WT_ROOT.'includes/functions/functions_edit.php';
	$xref    = WT_Filter::post('xref', WT_REGEX_XREF);
	$fact_id = WT_Filter::post('fact_id');

	$record = WT_GedcomRecord::getInstance($xref);
	if ($record && $record->canShow() && $record->canEdit()) {
		foreach ($record->getFacts() as $fact) {
			if ($fact->getfactId() == $fact_id && $fact->canShow() && $fact->canEdit()) {
				$record->deleteFact($fact_id, true);
				break 2;
			}
		}
	}

	// Can’t find the record/fact, or don’t have permission to delete it.
	header('HTTP/1.0 406 Not Acceptable');
	break;

case 'delete-family':
case 'delete-individual':
case 'delete-media':
case 'delete-note':
case 'delete-repository':
case 'delete-source':
	require WT_ROOT.'includes/functions/functions_edit.php';
	$record=WT_GedcomRecord::getInstance(WT_Filter::post('xref', WT_REGEX_XREF));
	if ($record && WT_USER_CAN_EDIT && $record->canShow() && $record->canEdit()) {
		// Delete links to this record
		foreach (fetch_all_links($record->getXref(), $record->getGedcomId()) as $xref) {
			$linker = WT_GedcomRecord::getInstance($xref);
			$old_gedcom =$linker->getGedcom();
			$new_gedcom = remove_links($old_gedcom, $record->getXref());
			// fetch_all_links() does not take account of pending changes.  The links (or even the
			// record itself) may have already been deleted.
			if ($old_gedcom !== $new_gedcom) {
				// If we have removed a link from a family to an individual, and it has only one member
				if (preg_match('/^0 @'.WT_REGEX_XREF.'@ FAM/', $new_gedcom) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . WT_REGEX_XREF . ')@/', $new_gedcom, $match)==1) {
					// Delete the family
					$family = WT_GedcomRecord::getInstance($xref);
					WT_FlashMessages::addMessage(/* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */ WT_I18N::translate('The family “%s” was deleted because it only has one member.', $family->getFullName()));
					$family->deleteRecord();
					// Delete any remaining link to this family
					if ($match) {
						$relict = WT_GedcomRecord::getInstance($match[2][0]);
						$new_gedcom = $relict->getGedcom();
						$new_gedcom = remove_links($new_gedcom, $linker->getXref());
						$relict->updateRecord($new_gedcom, false);
						WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $relict->getFullName(), $family->getFullName()));
					}
				} else {
					// Remove links from $linker to $record
					WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $linker->getFullName(), $record->getFullName()));
					$linker->updateRecord($new_gedcom, false);
				}
			}
		}
		// Delete the record itself
		$record->deleteRecord();
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'delete-user':
	$user = User::find(WT_Filter::postInteger('user_id'));

	if ($user && Auth::isAdmin() && Auth::user() !== $user) {
		Log::addAuthenticationLog('Deleted user: ' . $user->getUserName());
		$user->delete();
	}
	break;

case 'masquerade':
	$user = User::find(WT_Filter::postInteger('user_id'));

	if ($user && Auth::isAdmin() && Auth::user() !== $user) {
		Log::addAuthenticationLog('Masquerade as user: ' . $user->getUserName());
		Auth::login($user);
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'unlink-media':
	// Remove links from an individual and their spouse-family records to a media object.
	// Used by the "unlink" option on the album (lightbox) tab.
	require WT_ROOT.'includes/functions/functions_edit.php';
	$source = WT_Individual::getInstance( WT_Filter::post('source', WT_REGEX_XREF));
	$target = WT_Filter::post('target', WT_REGEX_XREF);
	if ($source && $source->canShow() && $source->canEdit() && $target) {
		// Consider the individual and their spouse-family records
		$sources = $source->getSpouseFamilies();
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
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'reject-changes':
	// Reject all the pending changes for a record
	$record=WT_GedcomRecord::getInstance(WT_Filter::post('xref', WT_REGEX_XREF));
	if ($record && WT_USER_CAN_ACCEPT && $record->canShow() && $record->canEdit()) {
		WT_FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ WT_I18N::translate('The changes to “%s” have been rejected.', $record->getFullName()));
		reject_all_changes($record->getXref(), $record->getGedcomId());
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'theme':
	// Change the current theme
	$theme_dir=WT_Filter::post('theme');
	if (WT_Site::getPreference('ALLOW_USER_THEMES') && in_array($theme_dir, get_theme_names())) {
		$WT_SESSION->theme_dir=$theme_dir;
		// Remember our selection
		Auth::user()->setPreference('theme', $theme_dir);
	} else {
		// Request for a non-existant theme.
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;
}
Zend_Session::writeClose();
