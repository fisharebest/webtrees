<?php
// Displays the details about a shared note record.  Also shows how many people and families
// reference this shared note.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if ($controller->record && $controller->record->canShow()) {
	$controller->pageHeader();
	if ($controller->record->isOld()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
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
	} elseif ($controller->record->isNew()) {
		if (WT_USER_CAN_ACCEPT) {
			echo
				'<p class="ui-state-highlight">',
				/* I18N: %1$s is “accept”, %2$s is “reject”.  These are links. */ WT_I18N::translate(
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

$linkToID=$controller->record->getXref(); // Tell addmedia.php what to link to

$controller
	->addInlineJavascript('function show_gedcom_record() {var recwin=window.open("gedrecord.php?pid=' . $controller->record->getXref() . '", "_blank", edit_window_specs);}')
	->addInlineJavascript('function edit_note() {var win04 = window.open("edit_interface.php?action=editnote&xref=' . $linkToID . '", "win04", edit_window_specs);if (window.focus) {win04.focus();}}')
	->addInlineJavascript('jQuery("#note-tabs").tabs();')
	->addInlineJavascript('jQuery("#note-tabs").css("visibility", "visible");');

$linked_indi = $controller->record->fetchLinkedIndividuals();
$linked_fam  = $controller->record->fetchLinkedFamilies();
$linked_obje = $controller->record->fetchLinkedMedia();
$linked_sour = $controller->record->fetchLinkedSources();

$facts = array();
foreach ($controller->record->getFacts() as $fact) {
	if ($fact->getTag() != 'CONT') {
		$facts[] = $fact;
	}
}

?>
<div id="note-details">
	<h2><?php echo $controller->record->getFullName(); ?></h2>
	<div id="note-tabs">
	<ul>
	<li><a href="#note-edit"><span><?php echo WT_I18N::translate('Details'); ?></span></a></li>
	<?php if ($linked_indi) { ?>
	<li><a href="#indi-note"><span id="indisource"><?php echo WT_I18N::translate('Individuals'); ?></span></a></li>
	<?php } ?>
	<?php if ($linked_fam) { ?>
	<li><a href="#fam-note"><span id="famsource"><?php echo WT_I18N::translate('Families'); ?>/span></a></li>
	<?php } ?>
	<?php if ($linked_obje) { ?>
			echo '<li><a href="#media-note"><span id="mediasource"><?php echo WT_I18N::translate('Media objects'); ?></span></a></li>
	<?php } ?>
	<?php if ($linked_sour) { ?>
			echo '<li><a href="#source-note"><span id="notesource"><?php echo WT_I18N::translate('Sources'); ?></span></a></li>
	<?php } ?>
	</ul>
	<div id="note-edit">
		<table class="facts_table">
			<tr>
				<td align="left" class="descriptionbox">
					<?php if (WT_USER_CAN_EDIT) { ?>
						<a href="#" onclick="edit_note()" title="<?php echo WT_I18N::translate('Edit'); ?>">
						<i class="icon-note"></i> <?php echo WT_I18N::translate('Shared note'); ?>
						</a>
						<div class="editfacts">
							<div class="editlink">
							<a class="editicon" href="#" onclick="edit_note()" title="<?php echo WT_I18N::translate('Edit'); ?>">
								<span class="link_text">', WT_I18N::translate('Edit'), '</span>
							</a>
						</div>
					<?php } else { ?>
					<i class="icon-note"></i>
						<?php echo WT_I18N::translate('Shared note'); ?>
					<?php } ?>
				</td>
				<td class="optionbox wrap width80" style="white-space: pre-wrap;"><?php echo $controller->record->getNote(); ?></td>
			</tr>
			<?php
				foreach ($facts as $fact) {
					print_fact($fact, $controller->record);
				}
				print_main_media($controller->record->getXref());
				if ($controller->record->canEdit()) {
					print_add_new_fact($controller->record->getXref(), $facts, 'NOTE');
				}
			?>
		</table>
	</div>
<?php
	if ($linked_indi) {
		echo '<div id="indi-note">';
		echo format_indi_table($controller->record->fetchLinkedIndividuals(), $controller->record->getFullName());
		echo '</div>';
	}
	if ($linked_fam) {
		echo '<div id="fam-note">';
		echo format_fam_table($controller->record->fetchLinkedFamilies(), $controller->record->getFullName());
		echo '</div>';
	}
	if ($linked_obje) {
		echo '<div id="media-note">';
		echo format_media_table($controller->record->fetchLinkedMedia(), $controller->record->getFullName());
		echo '</div>';
	}
	if ($linked_sour) {
		echo '<div id="source-note">';
		echo format_sour_table($controller->record->fetchLinkedSources(), $controller->record->getFullName());
		echo '</div>';
	}
?>
	</div>
</div>
