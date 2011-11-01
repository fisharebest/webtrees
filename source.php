<?php
// Displays the details about a source record.  Also shows how many people and families
// reference this source.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'source.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Source();
$controller->init();

if ($controller->source && $controller->source->canDisplayDetails()) {
	print_header($controller->getPageTitle());
	if ($controller->source->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This source has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->source->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->source->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
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
	} elseif (find_updated_record($controller->source->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This source has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->source->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onClick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->source->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
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
	header('HTTP/1.0 403 Forbidden');
	print_header(WT_I18N::translate('Source'));
	echo '<p class="ui-state-error">', WT_I18N::translate('This source does not exist or you do not have permission to view it.'), '</p>';
	print_footer();
	exit;
}

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

$linkToID=$controller->sid; // Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->source->getXref(), '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() { window.location="', $controller->source->getRawUrl(), '"; }';
?>	jQuery(document).ready(function() {
		jQuery("#source-tabs").tabs();
		jQuery("#source-tabs").css('visibility', 'visible');
	});
<?php
echo WT_JS_END;
echo '<div id="source-details">';
echo '<h2>', $controller->source->getFullName(), '</h2>';
echo '<div id="source-tabs">
	<ul>
		<li><a href="#source-edit"><span>', WT_I18N::translate('Details'), '</span></a></li>';
		if ($controller->source->countLinkedIndividuals()) {
			echo '<li><a href="#indi-sources"><span id="indisource">', WT_I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($controller->source->countLinkedFamilies()) {
			echo '<li><a href="#fam-sources"><span id="famsource">', WT_I18N::translate('Families'), '</span></a></li>';
		}
		if ($controller->source->countLinkedMedia()) {
			echo '<li><a href="#media-sources"><span id="mediasource">', WT_I18N::translate('Media objects'), '</span></a></li>';
		}
		if ($controller->source->countLinkedNotes()) {
			echo '<li><a href="#note-sources"><span id="notesource">', WT_I18N::translate('Notes'), '</span></a></li>';
		}
		echo '</ul>';
	// Edit this source
	echo '<div id="source-edit">';
		echo '<table class="facts_table">';

		$sourcefacts=$controller->source->getFacts();
		foreach ($sourcefacts as $fact) {
			print_fact($fact, $controller->source);
		}

		// Print media
		print_main_media($controller->sid);

		// new fact link
		if ($controller->source->canEdit()) {
			print_add_new_fact($controller->sid, $sourcefacts, 'SOUR');
			// new media
			if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				echo '<tr><td class="descriptionbox">';
				echo WT_I18N::translate('Add media'), help_link('add_media');
				echo '</td><td class="optionbox">';
				echo '<a href="javascript:;" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->sid, '\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Add a new media object'), '</a>';		
				echo '<br />';
				echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid=', $controller->sid, '&linkto=source\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Link to an existing media object'), '</a>';
				echo '</td></tr>';
			}
		}
		echo '</table>
	</div>'; // close "details"
	// Individuals linked to this source
	if ($controller->source->countLinkedIndividuals()) {
		echo '<div id="indi-sources">';
		print_indi_table($controller->source->fetchLinkedIndividuals(), $controller->source->getFullName());
		echo '</div>'; //close "indi-sources"
	}
	// Families linked to this source
	if ($controller->source->countLinkedFamilies()) {
		echo '<div id="fam-sources">';
		print_fam_table($controller->source->fetchLinkedFamilies(), $controller->source->getFullName());
		echo '</div>'; //close "fam-sources"
	}
	// Media Items linked to this source
	if ($controller->source->countLinkedMedia()) {
		echo '<div id="media-sources">';
		print_media_table($controller->source->fetchLinkedMedia(), $controller->source->getFullName());
		echo '</div>'; //close "media-sources"
	}
	// Shared Notes linked to this source
	if ($controller->source->countLinkedNotes()) {
		echo '<div id="note-sources">';
		print_note_table($controller->source->fetchLinkedNotes(), $controller->source->getFullName());
		echo '</div>'; //close "note-sources"
	}
echo '</div>'; //close div "source-tabs"
echo '</div>'; //close div "source-details"
print_footer();
