<?php
// Lightbox Album module for webtrees
//
// Display media Items using Lightbox
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$reorder=safe_GET_bool('reorder');
?>
<script>
	function album_add() {
		win03 = window.open(
		'addmedia.php?action=showmediaform&linktoid=<?php echo $controller->record->getXref(); ?>', 'win03', 'resizable=1, scrollbars=1, top=50, HEIGHT=780, WIDTH=600 ');
		if (window.focus) {win03.focus();}
	}
	function album_link() {
		win04 = window.open(
		'inverselink.php?linktoid=<?php echo $controller->record->getXref(); ?>&linkto=person', 'win04', 'resizable=1, scrollbars=1, top=50, HEIGHT=300, WIDTH=450 ');
		win04.focus()
	}
</script>

<?php
// If in re-order mode do not show header links, but instead, show drag and drop title.
if (isset($reorder) && $reorder==1) {
	echo '<center><b>', WT_I18N::translate('Drag-and-drop thumbnails to re-order media items'), '</b></center>';
	echo '<br>';
} else {
	//Show Lightbox-Album header Links
	if (WT_USER_CAN_EDIT) {
		echo '<table class="facts_table"><tr>';
		echo '<td class="descriptionbox rela">';
		// Add a new media object
		if (get_gedcom_setting(WT_GED_ID, 'MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
			echo '<span><a href="#" onclick="album_add()">';
			echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/image_add.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Add a new media object'), '" alt="', WT_I18N::translate('Add a new media object'), '">';
			echo WT_I18N::translate('Add a new media object');
			echo '</a></span>';
			// Link to an existing item
			echo '<span><a href="#" onclick="album_link()">';
			echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/image_link.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Link to an existing media object'), '" alt="', WT_I18N::translate('Link to an existing media object'), '">';
			echo WT_I18N::translate('Link to an existing media object');
			echo '</a></span>';
		}
		if (WT_USER_GEDCOM_ADMIN && $this->get_media_count()>1) {
			// Popup Reorder Media
			echo '<span><a href="#" onclick="reorder_media(\''.$controller->record->getXref().'\')">';
			echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/images.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Re-order media'), '" alt="', WT_I18N::translate('Re-order media'), '">';
			echo WT_I18N::translate('Re-order media');
			echo '</a></span>';
			echo '</td>';
		}
		echo '</tr></table>';
	}
}
