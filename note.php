<?php
/**
* Displays the details about a shared note record.  Also shows how many people and families
* reference this shared note.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2009 PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @package webtrees
* @version $Id$
*/

define('WT_SCRIPT_NAME', 'note.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/note_ctrl.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

// We have finished writing to $_SESSION, so release the lock
session_write_close();

$controller=new NoteController();
$controller->init();

// Tell addmedia.php what to link to
$linkToID=$controller->nid;

print_header($controller->getPageTitle());

// LightBox
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}

if (!$controller->note){
	echo "<b>", i18n::translate('Unable to find record with ID'), "</b><br /><br />";
	print_footer();
	exit;
}
else if ($controller->note->isMarkedDeleted()) {
	echo '<span class="error">', i18n::translate('This record has been marked for deletion upon admin approval.'), '</span>';
}

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->nid, '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() {';
echo ' window.location="note.php?nid=', $controller->nid, '&show_changes=yes"';
echo '}';
echo 'function edit_note() {';
echo ' var win04 = window.open("edit_interface.php?action=editnote&pid=', $linkToID, '", "win04", "top=70, left=70, width=620, height=500, resizable=1, scrollbars=1");';
echo ' if (window.focus) {win04.focus();}';
echo '}';
echo WT_JS_END;

echo '<table class="list_table width80"><tr><td>';
if ($controller->accept_success) {
	echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b><br />';
}
echo '<span class="name_head">', PrintReady(htmlspecialchars($controller->note->getFullName()));
if ($SHOW_ID_NUMBERS) {
	echo ' ', getLRM(), '(', $controller->nid, ')', getLRM();
}
echo '</span><br />';
echo '<table class="facts_table">';
echo '<tr class="', $TEXT_DIRECTION, '"><td><table class="width100">';
// Shared Note details ---------------------
$noterec = find_gedcom_record($controller->nid, WT_GED_ID);
$nt = preg_match("/0 @$controller->nid@ NOTE(.*)/", $noterec, $n1match);
if ($nt==1) {
	$note = print_note_record("<br />".$n1match[1], 1, $noterec, false, true);
}else{
	$note = "No Text";
}
echo '<tr><td align="left" class="descriptionbox ', $TEXT_DIRECTION, '">';
	echo '<center>';
	if (!empty($WT_IMAGES["notes"]["small"]) && $SHOW_FACT_ICONS)
		echo '<img src="', $WT_IMAGE_DIR, "/", $WT_IMAGES["notes"]["small"], '" alt="', i18n::translate('Shared Note'), '" title="', i18n::translate('Shared Note'), '" align="middle" /> ';
	echo i18n::translate('Shared Note'), "</center>";
	echo '<br /><br />';
	if (WT_USER_CAN_EDIT) {
		echo "<a href=\"javascript: edit_note()\"> ";
		echo i18n::translate('Edit');
		echo "</a>";
	}
	echo '</td><td class="optionbox wrap width80 ', $TEXT_DIRECTION, '">';
	echo $note;
	echo "<br />";
echo "</td></tr>";

$notefacts=$controller->note->getFacts();
foreach ($notefacts as $fact) {
	if ($fact && $fact->getTag()!='CONT') {
		if ($fact->getTag()=='NOTE' ) {
			print_fact($fact);
		} else {
			print_fact($fact);
		}
	}
}

// Print media
print_main_media($controller->nid);

// new fact link
if (!$controller->isPrintPreview() && $controller->userCanEdit()) {
	print_add_new_fact($controller->nid, $notefacts, 'NOTE');
	// new media
	echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, '">';
	echo i18n::translate('Add media'), help_link('add_media');
	echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
	echo '<a href="javascript: ', i18n::translate('Add media'), '" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->nid, '\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', i18n::translate('Add a new media item'), '</a>';
	echo '<br />';
	echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid=', $controller->nid, '&linkto=note\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', i18n::translate('Link to an existing Media item'), '</a>';
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
?>
