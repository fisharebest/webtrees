<?php
// Media View Page
//
// This page displays all information about media that is selected in PHPGedView.
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

define('WT_SCRIPT_NAME', 'mediaviewer.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller = new WT_Controller_Media();

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
					'This media object has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This media object has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
					'This media object has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\''.$controller->record->getXref().'\');">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This media object has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This media object does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

$controller->addInlineJavascript('
	jQuery("#media-tabs")
		.tabs({
			create: function(e, ui){
				jQuery(e.target).css("visibility", "visible");  // prevent FOUC
			}
		});
');

$linked_indi = $controller->record->linkedIndividuals('OBJE');
$linked_fam  = $controller->record->linkedFamilies('OBJE');
$linked_sour = $controller->record->linkedSources('OBJE');
$linked_repo = $controller->record->linkedRepositories('OBJE'); // Invalid GEDCOM - you cannot link a REPO to an OBJE
$linked_note = $controller->record->linkedNotes('OBJE');        // Invalid GEDCOM - you cannot link a NOTE to an OBJE

echo '<div id="media-details">';
echo '<h2>', $controller->record->getFullName(), ' ', $controller->record->getAddName(), '</h2>';
echo '<div id="media-tabs">';
	echo '<div id="media-edit">';
		echo '<table class="facts_table">
			<tr>
				<td align="center" width="150">';
					// When we have a pending edit, $controller->record shows the *old* data.
					// As a temporary kludge, fetch a "normal" version of the record - which includes pending changes
					// TODO - check both, and use RED/BLUE boxes.
					$tmp = WT_Media::getInstance($controller->record->getXref());
					echo $tmp->displayImage();
					if (!$tmp->isExternal()) {
						if ($tmp->fileExists('main')) {
							if ($SHOW_MEDIA_DOWNLOAD) {
								echo '<p><a href="' . $tmp->getHtmlUrlDirect('main', true).'">' . WT_I18N::translate('Download file') . '</a></p>';
							}
						} else {
							echo '<p class="ui-state-error">' . WT_I18N::translate('The file “%s” does not exist.', $tmp->getFilename()) . '</p>';
						}
					}
				echo '</td>
				<td valign="top">
					<table width="100%">
						<tr>
							<td>
								<table class="facts_table">';
										$facts = $controller->getFacts();
										foreach ($facts as $f=>$fact) {
											print_fact($fact, $controller->record);
										}
								echo '</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>';
	echo '<ul>';
		if ($linked_indi) {
			echo '<li><a href="#indi-media"><span id="indimedia">', WT_I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($linked_fam) {
			echo '<li><a href="#fam-media"><span id="fammedia">', WT_I18N::translate('Families'), '</span></a></li>';
		}
		if ($linked_sour) {
			echo '<li><a href="#sources-media"><span id="sourcemedia">', WT_I18N::translate('Sources'), '</span></a></li>';
		}
		if ($linked_repo) {
			echo '<li><a href="#repo-media"><span id="repomedia">', WT_I18N::translate('Repositories'), '</span></a></li>';
		}
		if ($linked_note) {
			echo '<li><a href="#notes-media"><span id="notemedia">', WT_I18N::translate('Notes'), '</span></a></li>';
		}
	echo '</ul>';

	// Individuals linked to this media object
	if ($linked_indi) {
		echo '<div id="indi-media">', format_indi_table($linked_indi), '</div>';
	}

	// Families linked to this media object
	if ($linked_fam) {
		echo '<div id="fam-media">', format_fam_table($linked_fam), '</div>';
	}

	// Sources linked to this media object
	if ($linked_sour) {
		echo '<div id="sources-media">', format_sour_table($linked_sour), '</div>';
	}

	// Repositories linked to this media object
	if ($linked_repo) {
		echo '<div id="repo-media">', format_repo_table($linked_repo), '</div>';
	}

	// medias linked to this media object
	if ($linked_note) {
		echo '<div id="notes-media">', format_note_table($linked_note), '</div>';
	}
echo '</div>';
echo '</div>';
