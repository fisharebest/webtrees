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
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'action.php');
require './includes/session.php';

header('Content-type: text/html; charset=UTF-8');

switch (safe_POST('action')) {
case 'accept-changes':
	// Accept all the pending changes for a record
	require WT_ROOT.'includes/functions/functions_edit.php';
	$record=WT_GedcomRecord::getInstance(safe_POST_xref('xref'));
	if ($record && WT_USER_CAN_ACCEPT && $record->canDisplayDetails() && $record->canEdit()) {
		WT_FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ WT_I18N::translate('The changes to “%s” have been accepted.', $record->getFullName()));
		accept_all_changes($record->getXref(), $record->getGedId());
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}	
	break;

case 'copy-fact':
	// Copy a fact to the clipboard
	// The calling page may want to reload, to refresh its "paste" buffer
	require WT_ROOT.'includes/functions/functions_edit.php';
	$fact=new WT_Event(rawurldecode(safe_POST('factgedcom', WT_REGEX_UNSAFE)), null, 0);
	// Where can we paste this?
	if (preg_match('/^(NOTE|SOUR|OBJE)$/', $fact->getTag())) {
		// Some facts can be pasted to any record
		$type='all';
	} else {
		// Other facts can only be pasted records of the same type
		$type=safe_POST('type', array('INDI','FAM','SOUR','REPO','OBJE','NOTE'));
	}
	if (!is_array($WT_SESSION->clipboard)) {
		$WT_SESSION->clipboard=array();
	}
	$WT_SESSION->clipboard[]=array(
		'type'   =>$type,
		'factrec'=>$fact->getGedcomRecord(),
		'fact'   =>$fact->getTag()
		);
	// The clipboard only holds 10 facts
	while (count($WT_SESSION->clipboard)>10) {
		array_shift($WT_SESSION->clipboard);
	}
	WT_FlashMessages::addMessage(WT_I18N::translate('Record copied to clipboard'));
	break;

case 'delete-family':
case 'delete-individual':
case 'delete-media':
case 'delete-note':
case 'delete-repository':
case 'delete-source':
	require WT_ROOT.'includes/functions/functions_edit.php';
	$record=WT_GedcomRecord::getInstance(safe_POST_xref('xref'));
	if ($record && WT_USER_CAN_EDIT && $record->canDisplayDetails() && $record->canEdit()) {
		// Delete links to this record
		foreach (fetch_all_links($record->getXref(), $record->getGedId()) as $xref) {
			$linker = WT_GedcomRecord::getInstance($xref);
			$gedrec = find_gedcom_record($xref, $record->getGedId(), true);
			$gedrec = remove_links($gedrec, $record->getXref());
			// If we have removed a link from a family to an individual, and it has only one member
			if (preg_match('/^0 @'.WT_REGEX_XREF.'@ FAM/', $gedcom) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . WT_REGEX_XREF . ')@/', $gedcom, $match)==1) {
				// Delete the family
				$family = WT_GedcomRecord::getInstance($xref);
				WT_FlashMessages::addMessage(/* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */ WT_I18N::translate('The family “%s” has been deleted, as it only has one member.', $family->getFullName()));
				delete_gedrec($family->getXref(), $family->getGedId());
				// Delete any remaining link to this family
				if ($match) {
					$relict = WT_GedcomRecord::getInstance($match[2][0]);
					$gedrec = find_gedcom_record($relict->getXref(), $relict->getGedId(), true);
					$gedrec = remove_links($gedrec, $linker->getXref());
					replace_gedrec($relict->getXref(), $relict->getGedId(), $gedrec, false);
					WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $relict->getFullName(), $family->getFullName()));
				}
			} else {
				// Remove links from $linker to $record
				WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $linker->getFullName(), $record->getFullName()));
				replace_gedrec($linker->getXref(), $linker->getGedId(), $gedrec, false);
			}
		}
		// Delete the record itself
		delete_gedrec($record->getXref(), $record->getGedId());
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}	
	break;

case 'delete-user':
	$user_id = WT_Filter::postInteger('user_id');

	if (WT_USER_IS_ADMIN && WT_USER_ID != $user_id && WT_Filter::checkCsrf()) {
		AddToLog('deleted user ->' . get_user_name($user_id) . '<-', 'auth');
		delete_user($user_id);
	}
	break;

case 'masquerade':
	$user_id   = WT_Filter::postInteger('user_id');
	$all_users = get_all_users('ASC', 'username');

	if (WT_USER_IS_ADMIN && WT_USER_ID != $user_id && array_key_exists($user_id, $all_users)) {
		AddToLog('masquerade as user ->' . get_user_name($user_id) . '<-', 'auth');
		$WT_SESSION->wt_user = $user_id;
		Zend_Session::regenerateId();
		Zend_Session::writeClose();
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;

case 'reject-changes':
	// Reject all the pending changes for a record
	require WT_ROOT.'includes/functions/functions_edit.php';
	$record=WT_GedcomRecord::getInstance(safe_POST_xref('xref'));
	if ($record && WT_USER_CAN_ACCEPT && $record->canDisplayDetails() && $record->canEdit()) {
		WT_FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ WT_I18N::translate('The changes to “%s” have been rejected.', $record->getFullName()));
		reject_all_changes($record->getXref(), $record->getGedId());
	} else {
		header('HTTP/1.0 406 Not Acceptable');
	}	
	break;

case 'theme':
	// Change the current theme
	$theme_dir=safe_POST('theme');
	if (WT_Site::preference('ALLOW_USER_THEMES') && in_array($theme_dir, get_theme_names())) {
		$WT_SESSION->theme_dir=$theme_dir;
		if (WT_USER_ID) {
			// Remember our selection
			set_user_setting(WT_USER_ID, 'theme', $theme_dir);
		}
	} else {
		// Request for a non-existant theme.
		header('HTTP/1.0 406 Not Acceptable');
	}
	break;
}
Zend_Session::writeClose();
