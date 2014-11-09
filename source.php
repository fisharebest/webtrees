<?php
// Displays the details about a source record.  Also shows how many people and families
// reference this source.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

define('WT_SCRIPT_NAME', 'source.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller = new WT_Controller_Source();

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
					'This source has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This source has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
					'This source has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This source has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This source does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

$controller->addInlineJavascript('
	jQuery("#source-tabs")
		.tabs({
			create: function(e, ui){
				jQuery(e.target).css("visibility", "visible");  // prevent FOUC
			}
		});
');

$linked_indi = $controller->record->linkedIndividuals('SOUR');
$linked_fam  = $controller->record->linkedFamilies('SOUR');
$linked_obje = $controller->record->linkedMedia('SOUR');
$linked_note = $controller->record->linkedNotes('SOUR');

echo '<div id="source-details">';
echo '<h2>', $controller->record->getFullName(), '</h2>';
echo '<div id="source-tabs">
	<ul>
		<li><a href="#source-edit"><span>', WT_I18N::translate('Details'), '</span></a></li>';
		if ($linked_indi) {
			echo '<li><a href="#indi-sources"><span id="indisource">', WT_I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($linked_fam) {
			echo '<li><a href="#fam-sources"><span id="famsource">', WT_I18N::translate('Families'), '</span></a></li>';
		}
		if ($linked_obje) {
			echo '<li><a href="#media-sources"><span id="mediasource">', WT_I18N::translate('Media objects'), '</span></a></li>';
		}
		if ($linked_note) {
			echo '<li><a href="#note-sources"><span id="notesource">', WT_I18N::translate('Notes'), '</span></a></li>';
		}
		echo '</ul>';

	echo '<div id="source-edit">';
		echo '<table class="facts_table">';

		// Fetch the facts
		$facts=$controller->record->getFacts();

		// Sort the facts
		usort(
			$facts,
			function(WT_Fact $x, WT_Fact $y) {
				static $order = array(
					'TITL' => 0,
					'ABBR' => 1,
					'AUTH' => 2,
					'DATA' => 3,
					'PUBL' => 4,
					'TEXT' => 5,
					'NOTE' => 6,
					'OBJE' => 7,
					'REFN' => 8,
					'RIN'  => 9,
					'_UID' => 10,
					'CHAN' => 11,
				);
				return
					(array_key_exists($x->getTag(), $order) ? $order[$x->getTag()] : PHP_INT_MAX)
					-
					(array_key_exists($y->getTag(), $order) ? $order[$y->getTag()] : PHP_INT_MAX);
			}
		);

		// Print the facts
		foreach ($facts as $fact) {
			print_fact($fact, $controller->record);
		}

		// new fact link
		if ($controller->record->canEdit()) {
			print_add_new_fact($controller->record->getXref(), $facts, 'SOUR');
			// new media
			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				echo '<tr><td class="descriptionbox">';
				echo WT_Gedcom_Tag::getLabel('OBJE');
				echo '</td><td class="optionbox">';
				echo '<a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&amp;linktoid=', $controller->record->getXref(), '\', \'_blank\', edit_window_specs); return false;">', WT_I18N::translate('Add a new media object'), '</a>';
				echo help_link('OBJE');
				echo '<br>';
				echo '<a href="#" onclick="window.open(\'inverselink.php?linktoid=', $controller->record->getXref(), '&amp;linkto=source\', \'_blank\', find_window_specs); return false;">', WT_I18N::translate('Link to an existing media object'), '</a>';
				echo '</td></tr>';
			}
		}
		echo '</table>
	</div>';

	// Individuals linked to this source
	if ($linked_indi) {
		echo '<div id="indi-sources">', format_indi_table($linked_indi), '</div>';
	}
	// Families linked to this source
	if ($linked_fam) {
		echo '<div id="fam-sources">', format_fam_table($linked_fam), '</div>';
	}
	// Media Items linked to this source
	if ($linked_obje) {
		echo '<div id="media-sources">', format_media_table($linked_obje), '</div>';
	}
	// Shared Notes linked to this source
	if ($linked_note) {
		echo '<div id="note-sources">', format_note_table($linked_note), '</div>';
	}
echo '</div>'; //close div "source-tabs"
echo '</div>'; //close div "source-details"
