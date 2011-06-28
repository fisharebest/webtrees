<?php
// Displays the details about a shared note record.  Also shows how many people and families
// reference this shared note.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2009 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'note.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Note();
$controller->init();

if ($controller->note && $controller->note->canDisplayDetails()) {
	print_header($controller->getPageTitle());
	if ($controller->note->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This note has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="' . $controller->note->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should accept or reject it.', 'accept') . '</a>',
					'<a href="' . $controller->note->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This note has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif (find_updated_record($controller->note->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This note has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="' . $controller->note->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should accept or reject it.', 'accept') . '</a>',
					'<a href="' . $controller->note->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This note has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif ($controller->accept_success) {
		echo '<p class="ui-state-highlight">', WT_I18N::translate('The changes have been accepted.'), '</p>';
	} elseif ($controller->reject_success) {
		echo '<p class="ui-state-highlight">', WT_I18N::translate('The changes have been rejected.'), '</p>';
	}
} else {
	print_header(WT_I18N::translate('Note'));
	echo '<p class="ui-state-error">', WT_I18N::translate('This note does not exist or you do not have permission to view it.'), '</p>';
	print_footer();
	exit;
}

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

$linkToID=$controller->nid; // Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->note->getXref(), '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() { window.location="', $controller->note->getRawUrl(), '"; }';
echo 'function edit_note() {';
echo ' var win04 = window.open("edit_interface.php?action=editnote&pid=', $linkToID, '", "win04", "top=70, left=70, width=620, height=500, resizable=1, scrollbars=1");';
echo ' if (window.focus) {win04.focus();}';
echo '}';
echo WT_JS_END;

echo '<table class="list_table width80"><tr><td>';
echo '<span class="name_head">', PrintReady(htmlspecialchars($controller->note->getFullName()));
echo '</span><br />';
echo '<table class="facts_table">';

// Shared Note details ---------------------
$noterec=$controller->note->getGedcomRecord();
preg_match("/0 @{$controller->nid}@ NOTE(.*)/", $noterec, $n1match);
$note = print_note_record("<br />".$n1match[1], 1, $noterec, false, true, true);

echo '<tr><td align="left" class="descriptionbox ', $TEXT_DIRECTION, '">';
	if (WT_USER_CAN_EDIT) {
		echo '<a href="javascript: edit_note()" title="', WT_I18N::translate('Edit'), '">';
		if (!empty($WT_IMAGES['note']) && $SHOW_FACT_ICONS) echo '<img src="', $WT_IMAGES['note'], '" alt="" align="top" />';
		echo WT_I18N::translate('Shared note'), '</a>';
		echo '<div class="editfacts">';
			echo '<div class="editlink"><a class="editicon" href="javascript: edit_note()" title="', WT_I18N::translate('Edit'), '"><span class="link_text">', WT_I18N::translate('Edit'), '</span></div></a>';
		echo '</div>';
	} else { 
		if (!empty($WT_IMAGES['note']) && $SHOW_FACT_ICONS) echo '<img src="', $WT_IMAGES['note'], '" alt="" align="top" />';
		echo WT_I18N::translate('Shared note');
	}
	echo '</td><td class="optionbox wrap width80 ', $TEXT_DIRECTION, '">';
	echo $note;
	echo "<br />";
echo "</td></tr>";

$notefacts=$controller->note->getFacts();
foreach ($notefacts as $fact) {
	if ($fact->getTag()!='CONT') {
		print_fact($fact, $controller->note);
	}
}

// Print media
print_main_media($controller->nid);

// new fact link
if ($controller->note->canEdit()) {
	print_add_new_fact($controller->nid, $notefacts, 'NOTE');
	// new media
	echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, '">';
	echo WT_I18N::translate('Add media'), help_link('add_media');
	echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
	echo '<a href="javascript:;" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->nid, '\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Add a new media object'), '</a>';
	echo '<br />';
	echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid=', $controller->nid, '&linkto=note\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Link to an existing media object'), '</a>';
	echo '</td></tr>';
}
echo '</table><br /><br /></td></tr><tr class="center"><td colspan="2">';

// Individuals linked to this shared note
if ($controller->note->countLinkedIndividuals()) {
	print_indi_table($controller->note->fetchLinkedIndividuals(), $controller->note->getFullName());
}

// Families linked to this shared note
if ($controller->note->countLinkedFamilies()) {
	print_fam_table($controller->note->fetchLinkedFamilies(), $controller->note->getFullName());
}

// Media Items linked to this shared note
if ($controller->note->countLinkedMedia()) {
	print_media_table($controller->note->fetchLinkedMedia(), $controller->note->getFullName());
}

// Sources linked to this shared note
if ($controller->note->countLinkedSources()) {
	print_sour_table($controller->note->fetchLinkedSources(), $controller->note->getFullName());
}

echo '</td></tr></table>';

print_footer();
