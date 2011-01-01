<?php
/**
 * Lightbox Album module for phpGedView
 *
 * Display media Items using Lightbox 4.1
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @package webtrees
 * @subpackage Module
 * @version $Id$
 * @author Brian Holland
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $reorder, $GEDCOM, $LB_AL_HEAD_LINKS;

$reorder=safe_get('reorder', '1', '0');

?>

<script language="javascript" type="text/javascript">
<!--
	function reorder_media() {
	var win02 = window.open(
	"edit_interface.php?action=reorder_media&pid=<?php echo $pid; ?>&currtab=album", "win02", "resizable=1, menubar=0, scrollbars=1, top=20, HEIGHT=840, WIDTH=450 ");
	if (window.focus) {win02.focus();}
	}

	function album_add() {
		win03 = window.open(
		"addmedia.php?action=showmediaform&linktoid=<?php echo $pid; ?>", "win03", "resizable=1, scrollbars=1, top=50, HEIGHT=780, WIDTH=600 ");
		if (window.focus) {win03.focus();}
	}

	function album_link() {
		win04 = window.open(
		"inverselink.php?linktoid=<?php echo $pid; ?>&linkto=person", "win04", "resizable=1, scrollbars=1, top=50, HEIGHT=300, WIDTH=450 ");
		win04.focus()
	}

	function goto_config_lightbox() {
		window.location = "module.php?mod=lightbox&mod_action=lb_editconfig&pid=<?php echo $pid; ?>&gedcom=<?php echo $GEDCOM; ?>#lightbox";
	}
-->
</script>

<?php

// Load Lightbox javascript and css files
// require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';

// Find if indi and family associated media exists and then count them ( $tot_med_ct)
require_once WT_ROOT.'includes/media_reorder_count.php';

	// If in re-order mode do not show header links, but instead, show drag and drop title.
	if (isset($reorder) && $reorder==1) {
		echo "<center><b>", WT_I18N::translate('Drag-and-drop thumbnails to re-order media items'), "</b></center>" ;
		echo "<br />";

	} else {
		//Show Lightbox-Album header Links
		//echo "<br />";
		echo '<table border="0" width="75%"><tr>';
		// echo "<td class=\"width10 center wrap\" valign=\"top\"></td>";

		if ($LB_AL_HEAD_LINKS == "icon" || (!WT_USER_IS_ADMIN && !WT_USER_CAN_EDIT)) {
		echo "<td>";
		}

		// Configuration
        if (WT_USER_IS_ADMIN) {
			if ($LB_AL_HEAD_LINKS == "both") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"javascript:goto_config_lightbox()\">";
				echo "<img src=\"modules/lightbox/images/image_edit.gif\" class=\"icon\" title=\"".WT_I18N::translate('Lightbox-Album Configuration')."\" alt=\"".WT_I18N::translate('Lightbox-Album Configuration')."\" /><br />" ;
				echo "" . WT_I18N::translate('Lightbox-Album Configuration') . "&nbsp;";
				echo "</a>";
				echo "</td>";
				// echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "text") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"javascript:goto_config_lightbox()\">";
				echo "" . WT_I18N::translate('Lightbox-Album Configuration') . "&nbsp;";
				echo "</a>";
				echo "</td>";
	        //    echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "icon") {
				echo "&nbsp;&nbsp;&nbsp;";
	            echo "<a href=\"javascript:goto_config_lightbox()\">";
				echo "<img src=\"modules/lightbox/images/image_edit.gif\" class=\"icon\" title=\"".WT_I18N::translate('Lightbox-Album Configuration')."\" alt=\"".WT_I18N::translate('Lightbox-Album Configuration')."\" />" ;
				echo "</a>";
			}
        }

		//Add a new multimedia object
        if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
	            echo "<a href=\"javascript: album_add()\"> ";
				echo "<img src=\"modules/lightbox/images/image_add.gif\" class=\"icon\" title=\"".WT_I18N::translate('Add a new Multimedia Object to this Individual')."\" alt=\"".WT_I18N::translate('Add a new Multimedia Object to this Individual')."\" /><br />" ;
				echo "" . WT_I18N::translate('Add a new Media Object') . "&nbsp;";
	            echo " </a> ";
	            echo "</td>";
	            //echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "text") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
	            echo "<a href=\"javascript: album_add()\"> ";
				echo "" . WT_I18N::translate('Add a new Media Object') . "&nbsp;";
	            echo " </a> ";
	            echo "</td>";
	            //echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "icon") {
				echo "&nbsp;&nbsp;&nbsp;";
	            echo "<a href=\"javascript: album_add()\"> ";
				echo "<img src=\"modules/lightbox/images/image_add.gif\" class=\"icon\" title=\"".WT_I18N::translate('Add a new Multimedia Object to this Individual')."\" alt=\"".WT_I18N::translate('Add a new Multimedia Object to this Individual')."\" />" ;
	            echo "</a>";
			}
        }

		//Link to an existing item
        if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
	            echo "<a href=\"javascript: album_link()\"> ";
				echo "<img src=\"modules/lightbox/images/image_link.gif\" class=\"icon\" title=\"".WT_I18N::translate('Link this Individual to an existing Multimedia Object')."\" alt=\"".WT_I18N::translate('Link this Individual to an existing Multimedia Object')."\" /><br />" ;
				echo "" . WT_I18N::translate('Link to an existing Media Object') . "&nbsp;";
	            echo " </a> ";
	            echo "</td>";
				//    echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "text") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
	            echo "<a href=\"javascript: album_link()\"> ";
				echo "" . WT_I18N::translate('Link to an existing Media Object') . "&nbsp;";
	            echo " </a> ";
	            echo "</td>";
				//    echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "icon") {
				echo "&nbsp;&nbsp;&nbsp;";
	            echo "<a href=\"javascript: album_link()\">";
				echo "<img src=\"modules/lightbox/images/image_link.gif\" class=\"icon\" title=\"".WT_I18N::translate('Link this Individual to an existing Multimedia Object')."\" alt=\"".WT_I18N::translate('Link this Individual to an existing Multimedia Object')."\" />" ;
	            echo "</a> ";
			} else {
			}
        }
/*
		// Album Reorder Media -----
		if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"".WT_SCRIPT_NAME."?pid={$pid}&amp;tab={$tabno}&amp;reorder=1\">" ;
				echo "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".WT_I18N::translate('Re-order media')."\" alt=\"".WT_I18N::translate('Re-order media')."\" /><br />" ;
				echo "" . WT_I18N::translate('Re-order media') . "&nbsp;";
				echo '</a>';
				echo "</td>";
				// echo "<input type=\"hidden\" name=\"reorder\" value=\"1\" />";
				//echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "text") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"".WT_SCRIPT_NAME."?pid={$pid}&amp;tab={$tabno}&amp;reorder=1\">" ;
				echo "" . WT_I18N::translate('Re-order media') . "&nbsp;";
				echo '</a>';
				echo "</td>";
				//echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "icon") {
				echo "&nbsp;&nbsp;&nbsp;";
				echo "<a href=\"".WT_SCRIPT_NAME."?pid={$pid}&amp;tab={$tabno}&amp;reorder=1\">" ;
				echo "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".WT_I18N::translate('Re-order media')."\" alt=\"".WT_I18N::translate('Re-order media')."\" />" ;
				echo '</a>';
				//echo "<td width=\"5%\">&nbsp;</td>";
			}
		}
*/

		// Popup Reorder Media -----
		if (WT_USER_CAN_EDIT ) {
			if ($LB_AL_HEAD_LINKS == "both") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"javascript: reorder_media()\">" ;
				echo "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".WT_I18N::translate('Re-order media (window)')."\" alt=\"".WT_I18N::translate('Re-order media (window)')."\" /><br />" ;
				//echo "" . WT_I18N::translate('Re-order media (window)') . "&nbsp;";
				echo "" . WT_I18N::translate('Re-order media (window)') . "&nbsp;";
				echo '</a>';
				echo "</td>";
				//echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "text") {
				echo "<td class=\"width15 center wrap\" valign=\"top\">";
				echo "<a href=\"javascript: reorder_media()\">" ;
				// echo "" . WT_I18N::translate('Re-order media (window)') . "&nbsp;";
				echo "" . WT_I18N::translate('Re-order media') . "&nbsp;";
				echo '</a>';
				echo "</td>";
				//echo "<td width=\"5%\">&nbsp;</td>";
			} else if ($LB_AL_HEAD_LINKS == "icon") {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<a href=\"javascript: reorder_media()\">" ;
				echo "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".WT_I18N::translate('Re-order media (window)')."\" alt=\"".WT_I18N::translate('Re-order media (window)')."\" /><br />" ;
				echo '</a>';
				//echo "<td width=\"5%\">&nbsp;</td>";
			}
		}


		if ($LB_AL_HEAD_LINKS == "icon" || (!WT_USER_IS_ADMIN && !WT_USER_CAN_EDIT)) {
		echo "</td>";
		}

		echo "</tr></table>";
	}
