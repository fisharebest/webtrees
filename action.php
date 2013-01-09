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
		array_pop($WT_SESSION->clipboard);
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
			// Fetch the latest version, including any pending changes
			$gedrec=find_gedcom_record($xref, $record->getGedId(), true);
			// Delete the links, plus any sub-tags of the links
			$gedrec=preg_replace('/\n1 '.WT_REGEX_TAG.' @'.$record->getXref().'@(\n[2-9].*)*/', '', $gedrec);
			$gedrec=preg_replace('/\n2 '.WT_REGEX_TAG.' @'.$record->getXref().'@(\n[3-9].*)*/', '', $gedrec);
			$gedrec=preg_replace('/\n3 '.WT_REGEX_TAG.' @'.$record->getXref().'@(\n[4-9].*)*/', '', $gedrec);
			$gedrec=preg_replace('/\n4 '.WT_REGEX_TAG.' @'.$record->getXref().'@(\n[5-9].*)*/', '', $gedrec);
			$gedrec=preg_replace('/\n5 '.WT_REGEX_TAG.' @'.$record->getXref().'@(\n[6-9].*)*/', '', $gedrec);
			$tmp=WT_GedcomRecord::getInstance($xref);
			if (preg_match('/^0 @'.WT_REGEX_XREF.'@ FAM/', $gedrec) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) /', $gedrec, $dummy)<2) {
				WT_FlashMessages::addMessage(/* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */ WT_I18N::translate('The family “%s” has been deleted, as it only has one member.', $tmp->getFullName()));
				delete_gedrec($xref, $record->getGedId());
			} else {
				// Just remove the links
				WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $tmp->getFullName(), $record->getFullName()));
				replace_gedrec($xref, $record->getGedId(), $gedrec, false);
			}
		}
		// Delete the record itself
		delete_gedrec($record->getXref(), $record->getGedId());
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
