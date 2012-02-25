<?php
// Displays the details about a repository record.  Also shows how many sources
// reference this repository.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

define('WT_SCRIPT_NAME', 'repo.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Repository();

if ($controller->record && $controller->record->canDisplayDetails()) {
	$controller->pageHeader();
	if ($controller->record->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This repository has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This repository has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	} elseif (find_updated_record($controller->record->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This repository has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'accept-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="jQuery.post(\'action.php\',{action:\'reject-changes\',xref:\''.$controller->record->getXref().'\'},function(){location.reload();})">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', help_link('pending_changes'),
				'</p>';
		} elseif (WT_USER_CAN_EDIT) {
			echo
				'<p class="ui-state-highlight">',
				WT_I18N::translate('This repository has been edited.  The changes need to be reviewed by a moderator.'),
				' ', help_link('pending_changes'),
				'</p>';
		}
	}
} else {
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$controller->pageHeader();
	echo '<p class="ui-state-error">', WT_I18N::translate('This repository does not exist or you do not have permission to view it.'), '</p>';
	exit;
}

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

$linkToID=$controller->record->getXref(); // Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo 'alert("eek");';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->record->getXref(), '", "_blank", edit_window_specs);';
echo '}';
echo 'function showchanges() { window.location="', $controller->record->getRawUrl(), '"; }';
?>	jQuery(document).ready(function() {
		jQuery("#repo-tabs").tabs();
		jQuery("#repo-tabs").css('visibility', 'visible');
	});
<?php
echo WT_JS_END;

echo '<div id="repo-details">';
echo '<h2>', $controller->record->getFullName(), '</h2>';
echo '<div id="repo-tabs">
	<ul>
		<li><a href="#repo-edit"><span>', WT_I18N::translate('Details'), '</span></a></li>';
		if ($controller->record->countLinkedSources()) {
			echo '<li><a href="#source-repo"><span id="reposource">', WT_I18N::translate('Sources'), '</span></a></li>';
		}
		echo '</ul>';

	// Shared Note details ---------------------
	echo '<div id="repo-edit">';
		echo '<table class="facts_table">';
			$repositoryfacts=$controller->record->getFacts();
			foreach ($repositoryfacts as $fact) {
				print_fact($fact, $controller->record);
			}

			// Print media
			print_main_media($controller->record->getXref());

			// new fact link
			if ($controller->record->canEdit()) {
				print_add_new_fact($controller->record->getXref(), $repositoryfacts, 'REPO');
				// new media
				if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
					echo '<tr><td class="descriptionbox">';
					echo WT_I18N::translate('Add media'), help_link('OBJE');
					echo '</td><td class="optionbox">';
					echo '<a href="#" onclick="window.open(\'addmedia.php?action=showmediaform&amp;linktoid=', $controller->record->getXref(), '\', \'_blank\', edit_window_specs); return false;">', WT_I18N::translate('Add a new media object'), '</a>';
					echo '<br>';
					echo '<a href="#" onclick="window.open(\'inverselink.php?linktoid=', $controller->record->getXref(), '&amp;linkto=repository\', \'_blank\', find_window_specs); return false;">', WT_I18N::translate('Link to an existing media object'), '</a>';
					echo '</td></tr>';
				}}
		echo '</table>
	</div>'; // close "repo-edit"


	// Sources linked to this repository
	if ($controller->record->countLinkedSources()) {
		echo '<div id="source-repo">';
		echo format_sour_table($controller->record->fetchLinkedSources(), $controller->record->getFullName());
		echo '</div>'; //close "source-repo"
	}

echo '</div>'; //close div "repo-tabs"
echo '</div>'; //close div "repo-details"
