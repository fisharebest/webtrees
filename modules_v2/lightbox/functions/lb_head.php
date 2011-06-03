<?php
/**
 * Lightbox Album module for webtrees
 *
 * Display media Items using Lightbox
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 * @author Brian Holland
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $GEDCOM;
$reorder=safe_get('reorder', '1', '0');
?>
<script language="javascript" type="text/javascript">
<!--
	function reorder_media() {
		var win02 = window.open(
		'edit_interface.php?action=reorder_media&pid=<?php echo $this->controller->indi->getXref(); ?>&currtab=album', 'win02', 'resizable=1, menubar=0, scrollbars=1, top=20, HEIGHT=840, WIDTH=450 ');
		if (window.focus) {win02.focus();}
	}
	function album_add() {
		win03 = window.open(
		'addmedia.php?action=showmediaform&linktoid=<?php echo $this->controller->indi->getXref(); ?>', 'win03', 'resizable=1, scrollbars=1, top=50, HEIGHT=780, WIDTH=600 ');
		if (window.focus) {win03.focus();}
	}
	function album_link() {
		win04 = window.open(
		'inverselink.php?linktoid=<?php echo $this->controller->indi->getXref(); ?>&linkto=person', 'win04', 'resizable=1, scrollbars=1, top=50, HEIGHT=300, WIDTH=450 ');
		win04.focus()
	}
-->
</script>

<?php
// Find if indi and family associated media exists and then count them ( $tot_med_ct)
require_once WT_ROOT.'includes/media_reorder_count.php';

// If in re-order mode do not show header links, but instead, show drag and drop title.
if (isset($reorder) && $reorder==1) {
	echo '<center><b>', WT_I18N::translate('Drag-and-drop thumbnails to re-order media items'), '</b></center>';
	echo '<br />';
} else {
	//Show Lightbox-Album header Links
	if (WT_USER_IS_ADMIN) {
		echo '<table border="0" width="75%"><tr>';
		// Add a new multimedia object
		echo '<td class="width15 center wrap" valign="top">';
		echo '<a href="javascript: album_add()">';
		echo '<img src="', WT_MODULES_DIR, 'lightbox/images/image_add.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Add a new Multimedia Object to this Individual'), '" alt="', WT_I18N::translate('Add a new Multimedia Object to this Individual'), '" /><br />';
		echo WT_I18N::translate('Add a new Media Object');
		echo '</a>';
		echo '</td>';
		// Link to an existing item
		echo '<td class="width15 center wrap" valign="top">';
		echo '<a href="javascript: album_link()">';
		echo '<img src="', WT_MODULES_DIR, 'lightbox/images/image_link.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Link this Individual to an existing Multimedia Object'), '" alt="', WT_I18N::translate('Link this Individual to an existing Multimedia Object'), '" /><br />';
		echo WT_I18N::translate('Link to an existing Media Object');
		echo '</a>';
		echo '</td>';
		// Popup Reorder Media
		echo '<td class="width15 center wrap" valign="top">';
		echo '<a href="javascript: reorder_media()">';
		echo '<img src="', WT_MODULES_DIR, 'lightbox/images/images.gif" id="head_icon" class="icon" title="', WT_I18N::translate('Re-order media (window)'), '" alt="', WT_I18N::translate('Re-order media (window)'), '" /><br />';
		echo WT_I18N::translate('Re-order media (window)');
		echo '</a>';
		echo '</td>';
		echo '</tr></table>';
	}
}
