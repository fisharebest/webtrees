<?php
/**
* Displays the details about a source record.  Also shows how many people and families
* reference this source.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'source.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/source_ctrl.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

// We have finished writing to $_SESSION, so release the lock
session_write_close();

$controller=new SourceController();
$controller->init();

// Tell addmedia.php what to link to
$linkToID=$controller->sid;

print_header($controller->getPageTitle());

// LightBox
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}

if (!$controller->source){
	echo "<b>", i18n::translate('Unable to find record with ID'), "</b><br /><br />";
	print_footer();
	exit;
}
else if ($controller->source->isMarkedDeleted()) {
	echo '<span class="error">', i18n::translate('This record has been marked for deletion upon admin approval.'), '</span>';
}

echo WT_JS_START;
echo 'function show_gedcom_record() {';
echo ' var recwin=window.open("gedrecord.php?pid=', $controller->sid, '", "_blank", "top=0, left=0, width=600, height=400, scrollbars=1, scrollable=1, resizable=1");';
echo '}';
echo 'function showchanges() {';
echo ' window.location="source.php?sid=', $controller->sid, '&show_changes=yes"';
echo '}';
echo WT_JS_END;

echo '<table class="list_table"><tr><td>';
if ($controller->accept_success) {
	echo '<b>', i18n::translate('Changes successfully accepted into database'), '</b><br />';
}
echo '<span class="name_head">', PrintReady(htmlspecialchars($controller->source->getFullName()));
if ($SHOW_ID_NUMBERS) {
	echo ' ', getLRM(), '(', $controller->sid, ')', getLRM();
}
echo '</span><br />';
echo '<table class="facts_table">';

$sourcefacts=$controller->source->getFacts();
foreach ($sourcefacts as $fact) {
	if ($fact) {
		if ($fact->getTag()=='NOTE') {
			print_main_notes($fact->getGedcomRecord(), 1, $controller->sid, $fact->getLineNumber());
		} else {
			print_fact($fact);
		}
	}
}

// Print media
print_main_media($controller->sid);

// new fact link
if (!$controller->isPrintPreview() && $controller->userCanEdit()) {
	print_add_new_fact($controller->sid, $sourcefacts, 'SOUR');
	// new media
	echo '<tr><td class="descriptionbox ', $TEXT_DIRECTION, '">';
	echo i18n::translate('Add media'), help_link('add_media');
	echo '</td><td class="optionbox ', $TEXT_DIRECTION, '">';
	echo '<a href="javascript: ', i18n::translate('Add media'), '" onclick="window.open(\'addmedia.php?action=showmediaform&linktoid=', $controller->sid, '\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', i18n::translate('Add a new media item'), '</a>';
	echo '<br />';
	echo '<a href="javascript:;" onclick="window.open(\'inverselink.php?linktoid=', $controller->sid, '&linkto=source\', \'_blank\', \'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1\'); return false;">', i18n::translate('Link to an existing Media item'), '</a>';
	echo '</td></tr>';
}
echo '</table><br /><br /></td></tr><tr class="center"><td colspan="2">';

// Individuals linked to this source
if ($controller->source->countLinkedIndividuals()) {
	print_indi_table($controller->source->fetchLinkedIndividuals(), $controller->source->getFullName());
}

// Families linked to this source
if ($controller->source->countLinkedFamilies()) {
	print_fam_table($controller->source->fetchLinkedFamilies(), $controller->source->getFullName());
}

// Media Items linked to this source
if ($controller->source->countLinkedMedia()) {
	print_media_table($controller->source->fetchLinkedMedia(), $controller->source->getFullName());
}

// Shared Notes linked to this source
if ($controller->source->countLinkedNotes()) {
	print_note_table($controller->source->fetchLinkedNotes(), $controller->source->getFullName());
}

echo '</td></tr></table>';

print_footer();
?>
