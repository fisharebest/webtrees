<?php
// Displays the details about a shared note record.  Also shows how many people and families
// reference this shared note.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Note();

if ($controller->record && $controller->record->canDisplayDetails()) {
	$controller->pageHeader();
	if ($controller->record->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This note has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
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
	} elseif (find_updated_record($controller->record->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This note has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
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
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This note does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

if (WT_USE_LIGHTBOX) {
	$album = new lightbox_WT_Module();
	$album->getPreLoadContent();
}

$linkToID=$controller->record->getXref(); // Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->record->getXref(), '", "_blank", edit_window_specs);';
echo '}';
echo 'function showchanges() { window.location="', $controller->record->getRawUrl(), '"; }';
echo 'function edit_note() {';
echo ' var win04 = window.open("edit_interface.php?action=editnote&pid=', $linkToID, '", "win04", edit_window_specs);';
echo ' if (window.focus) {win04.focus();}';
echo '}';
?>	jQuery(document).ready(function() {
		jQuery("#note-tabs").tabs();
		jQuery("#note-tabs").css('visibility', 'visible');
	});
<?php
echo WT_JS_END;

echo '<div id="note-details">';
echo '<h2>', $controller->record->getFullName(), '</h2>';
echo '<div id="note-tabs">
	<ul>
		<li><a href="#note-edit"><span>', WT_I18N::translate('Details'), '</span></a></li>';
		if ($controller->record->countLinkedIndividuals()) {
			echo '<li><a href="#indi-note"><span id="indisource">', WT_I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($controller->record->countLinkedFamilies()) {
			echo '<li><a href="#fam-note"><span id="famsource">', WT_I18N::translate('Families'), '</span></a></li>';
		}
		if ($controller->record->countLinkedMedia()) {
			echo '<li><a href="#media-note"><span id="mediasource">', WT_I18N::translate('Media objects'), '</span></a></li>';
		}
		if ($controller->record->countLinkedSources()) {
			echo '<li><a href="#source-note"><span id="notesource">', WT_I18N::translate('Sources'), '</span></a></li>';
		}
		echo '</ul>';

	// Shared Note details ---------------------
	$noterec=$controller->record->getGedcomRecord();
	preg_match("/0 @".$controller->record->getXref()."@ NOTE(.*)/", $noterec, $n1match);
	$note = print_note_record("<br>".$n1match[1], 1, $noterec, false, true, true);

	echo '<div id="note-edit">';
		echo '<table class="facts_table">';
			echo '<tr><td align="left" class="descriptionbox">';
				if (WT_USER_CAN_EDIT) {
					echo '<a href="#" onclick="edit_note()" title="', WT_I18N::translate('Edit'), '">';
					echo '<i class="icon-note"></i>';
					echo WT_I18N::translate('Shared note'), '</a>';
					echo '<div class="editfacts">';
					echo '<div class="editlink"><a class="editicon" href="#" onclick="edit_note()" title="', WT_I18N::translate('Edit'), '"><span class="link_text">', WT_I18N::translate('Edit'), '</span></div></a>';
					echo '</div>';
				} else { 
					echo '<i class="icon-note"></i>';
					echo WT_I18N::translate('Shared note');
				}
				echo '</td><td class="optionbox wrap width80">';
				echo $note;
				echo "<br>";
			echo "</td></tr>";

			$notefacts=$controller->record->getFacts();
			foreach ($notefacts as $fact) {
				if ($fact->getTag()!='CONT') {
					print_fact($fact, $controller->record);
				}
			}
			// Print media
			print_main_media($controller->record->getXref());
			// new fact link
			if ($controller->record->canEdit()) {
				print_add_new_fact($controller->record->getXref(), $notefacts, 'NOTE');
			}
		echo '</table>
	</div>'; // close "note-edit"

	// Individuals linked to this shared note
	if ($controller->record->countLinkedIndividuals()) {
		echo '<div id="indi-note">';
		echo format_indi_table($controller->record->fetchLinkedIndividuals(), $controller->record->getFullName());
		echo '</div>'; //close "indi-note"
	}
	// Families linked to this shared note
	if ($controller->record->countLinkedFamilies()) {
		echo '<div id="fam-note">';
		echo format_fam_table($controller->record->fetchLinkedFamilies(), $controller->record->getFullName());
		echo '</div>'; //close "fam-note"
	}
	// Media Items linked to this shared note
	if ($controller->record->countLinkedMedia()) {
		echo '<div id="media-note">';
		echo format_media_table($controller->record->fetchLinkedMedia(), $controller->record->getFullName());
		echo '</div>'; //close "media-note"
	}
	// Sources linked to this shared note
	if ($controller->record->countLinkedSources()) {
		echo '<div id="source-note">';
		echo format_sour_table($controller->record->fetchLinkedSources(), $controller->record->getFullName());
		echo '</div>'; //close "source-note"
	}
echo '</div>'; //close div "note-tabs"
echo '</div>'; //close div "note-details"
