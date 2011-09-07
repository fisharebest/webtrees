<?php
// Displays the details about a repository record.  Also shows how many sources
// reference this repository.
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

define('WT_SCRIPT_NAME', 'repo.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new WT_Controller_Repository();
$controller->init();

if ($controller->repository && $controller->repository->canDisplayDetails()) {
	print_header($controller->getPageTitle());
	if ($controller->repository->isMarkedDeleted()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This repository has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="' . $controller->repository->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="' . $controller->repository->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
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
	} elseif (find_updated_record($controller->repository->getXref(), WT_GED_ID)!==null) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is "accept", %2$s is "reject".  These are links. */ WT_I18N::translate(
					'This repository has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="' . $controller->repository->getHtmlUrl() . '&amp;action=accept">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="' . $controller->repository->getHtmlUrl() . '&amp;action=undo">' . WT_I18N::translate_c('You should review the changes and then accept or reject them.', 'reject') . '</a>'
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
	} elseif ($controller->accept_success) {
		echo '<p class="ui-state-highlight">', WT_I18N::translate('The changes have been accepted.'), '</p>';
	} elseif ($controller->reject_success) {
		echo '<p class="ui-state-highlight">', WT_I18N::translate('The changes have been rejected.'), '</p>';
	}
} else {
	print_header(WT_I18N::translate('Repository'));
	echo '<p class="ui-state-error">', WT_I18N::translate('This repository does not exist or you do not have permission to view it.'), '</p>';
	print_footer();
	exit;
}

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

$linkToID=$controller->rid; // Tell addmedia.php what to link to

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->repository->getXref(), '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() { window.location="', $controller->repository->getRawUrl(), '"; }';
?>	jQuery(document).ready(function() {
		jQuery("#repo-tabs").tabs();
		jQuery("#repo-tabs").css('visibility', 'visible');
	});
<?php
echo WT_JS_END;

echo '<div id="repo-details">';
echo '<h2>', PrintReady(htmlspecialchars($controller->repository->getFullName())), '</h2>';
echo '<div id="repo-tabs">
	<ul>
		<li><a href="#repo-edit"><span>', WT_I18N::translate('Details'), '</span></a></li>';
		if ($controller->repository->countLinkedSources()) {
			echo '<li><a href="#source-repo"><span id="reposource">', WT_I18N::translate('Sources'), '</span></a></li>';
		}
		echo '<a id="repo-return" href="repolist.php">', WT_I18N::translate('Return to repositories'), '</a>
	</ul>';

	// Shared Note details ---------------------
	echo '<div id="repo-edit">';
		echo '<table class="facts_table">';
			$repositoryfacts=$controller->repository->getFacts();
			foreach ($repositoryfacts as $fact) {
				print_fact($fact, $controller->repository);
			}

			// Print media
			print_main_media($controller->rid);

			// new fact link
			if ($controller->repository->canEdit()) {
				print_add_new_fact($controller->rid, $repositoryfacts, 'REPO');
				// new media
				if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') > WT_USER_ACCESS_LEVEL) {
					echo '<tr><td class="descriptionbox">';
					echo WT_I18N::translate('Add media'), help_link('add_media');
					echo '</td><td class="optionbox">';
					echo '<a href="javascript:;" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->rid, '\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Add a new media object'), '</a>';
					echo '<br />';
					echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid=', $controller->rid, '&linkto=repository\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', WT_I18N::translate('Link to an existing media object'), '</a>';
					echo '</td></tr>';
				}}
		echo '</table>
	</div>'; // close "repo-edit"


	// Sources linked to this repository
	if ($controller->repository->countLinkedSources()) {
		echo '<div id="source-repo">';
		print_sour_table($controller->repository->fetchLinkedSources(), $controller->repository->getFullName());
		echo '</div>'; //close "source-repo"
	}

echo '</div>'; //close div "repo-tabs"
echo '</div>'; //close div "repo-details"

print_footer();
