<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree   $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\MediaController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'mediaviewer.php');
require './includes/session.php';

$controller = new MediaController;

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isPendingDeletion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This media object has been deleted.  You should review the deletion and then %1$s or %2$s it.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the deletion and then accept or reject it.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This media object has been deleted.  The deletion will need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	} elseif ($controller->record->isPendingAddtion()) {
		if (Auth::isModerator($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ I18N::translate(
					'This media object has been edited.  You should review the changes and then %1$s or %2$s them.',
					'<a href="#" onclick="accept_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'accept') . '</a>',
					'<a href="#" onclick="reject_changes(\'' . $controller->record->getXref() . '\');">' . I18N::translateContext('You should review the changes and then accept or reject them.', 'reject') . '</a>'
				),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		} elseif (Auth::isEditor($controller->record->getTree())) {
			echo
				'<p class="ui-state-highlight">',
				I18N::translate('This media object has been edited.  The changes need to be reviewed by a moderator.'),
				' ', FunctionsPrint::helpLink('pending_changes'),
				'</p>';
		}
	}
} else {
	http_response_code(404);
	$controller->pageHeader();
	echo '<p class="ui-state-error">', I18N::translate('This media object does not exist or you do not have permission to view it.'), '</p>';

	return;
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
$linked_note = $controller->record->linkedNotes('OBJE'); // Invalid GEDCOM - you cannot link a NOTE to an OBJE

echo '<div id="media-details">';
echo '<h2>', $controller->record->getFullName(), ' ', $controller->record->getAddName(), '</h2>';
echo '<div id="media-tabs">';
	echo '<div id="media-edit">';
		echo '<table class="facts_table">
			<tr>
				<td align="center" width="150">';
					// When we have a pending edit, $controller->record shows the *old* data.
					// As a temporary kludge, fetch a "normal" version of the record - which includes pending changes
					// Perhaps check both, and use RED/BLUE boxes.
					$tmp = Media::getInstance($controller->record->getXref(), $WT_TREE);
					echo $tmp->displayImage();
					if (!$tmp->isExternal()) {
						if ($tmp->fileExists('main')) {
							if ($controller->record->getTree()->getPreference('SHOW_MEDIA_DOWNLOAD')) {
								echo '<p><a href="' . $tmp->getHtmlUrlDirect('main', true) . '">' . I18N::translate('Download file') . '</a></p>';
							}
						} else {
							echo '<p class="ui-state-error">' . I18N::translate('The file “%s” does not exist.', $tmp->getFilename()) . '</p>';
						}
					}
				echo '</td>
				<td valign="top">
					<table width="100%">
						<tr>
							<td>
								<table class="facts_table">';
										$facts = $controller->getFacts();
										foreach ($facts as $f => $fact) {
											FunctionsPrintFacts::printFact($fact, $controller->record);
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
			echo '<li><a href="#indi-media"><span id="indimedia">', I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($linked_fam) {
			echo '<li><a href="#fam-media"><span id="fammedia">', I18N::translate('Families'), '</span></a></li>';
		}
		if ($linked_sour) {
			echo '<li><a href="#sources-media"><span id="sourcemedia">', I18N::translate('Sources'), '</span></a></li>';
		}
		if ($linked_repo) {
			echo '<li><a href="#repo-media"><span id="repomedia">', I18N::translate('Repositories'), '</span></a></li>';
		}
		if ($linked_note) {
			echo '<li><a href="#notes-media"><span id="notemedia">', I18N::translate('Notes'), '</span></a></li>';
		}
	echo '</ul>';

	// Individuals linked to this media object
	if ($linked_indi) {
		echo '<div id="indi-media">', FunctionsPrintLists::individualTable($linked_indi), '</div>';
	}

	// Families linked to this media object
	if ($linked_fam) {
		echo '<div id="fam-media">', FunctionsPrintLists::familyTable($linked_fam), '</div>';
	}

	// Sources linked to this media object
	if ($linked_sour) {
		echo '<div id="sources-media">', FunctionsPrintLists::sourceTable($linked_sour), '</div>';
	}

	// Repositories linked to this media object
	if ($linked_repo) {
		echo '<div id="repo-media">', FunctionsPrintLists::repositoryTable($linked_repo), '</div>';
	}

	// medias linked to this media object
	if ($linked_note) {
		echo '<div id="notes-media">', FunctionsPrintLists::noteTable($linked_note), '</div>';
	}
echo '</div>';
echo '</div>';
