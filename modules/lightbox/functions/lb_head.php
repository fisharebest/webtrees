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

/*
if (!file_exists("modules/googlemap/defaultconfig.php")) {
	$tabno = "7";
}else{
	$tabno = "8";
}
*/
$tabno=safe_get('tab');

// The following is temporary, until the handling of the Lightbox Help system
// is adjusted to match the usual webtrees practice
$lbHelpFile = "modules/lightbox/languages/help.".WT_LOCALE.".php";
if (!file_exists($lbHelpFile)) $lbHelpFile = "modules/lightbox/languages/help_text.en.php";

?>

<script language="javascript" type="text/javascript">
<!--
	function album_help(OPTS) {
		var win01 = window.open("<?php print $lbHelpFile;?>?"+OPTS, "win01", "resizable=1, scrollbars=1, HEIGHT=780, WIDTH=500 ");
		win01.focus()
	}

	function reorder_media() {
	var win02 = window.open(
	"edit_interface.php?action=reorder_media&pid=<?php print $pid; ?>", "win02", "resizable=1, menubar=0, scrollbars=1, top=20, HEIGHT=840, WIDTH=450 ");
	if (window.focus) {win02.focus();}
	}

	function album_add() {
		win03 = window.open(
		"addmedia.php?action=showmediaform&linktoid=<?php print $pid; ?>", "win03", "resizable=1, scrollbars=1, top=50, HEIGHT=780, WIDTH=600 ");
		if (window.focus) {win03.focus();}
	}

	function album_link() {
		win04 = window.open(
		"inverselink.php?linktoid=<?php print $pid; ?>&linkto=person", "win04", "resizable=1, scrollbars=1, top=50, HEIGHT=300, WIDTH=450 ");
		win04.focus()
	}
	
	function goto_config_lightbox() {
		window.location = "module.php?mod=lightbox&pgvaction=lb_editconfig&pid=<?php print $pid; ?>&gedcom=<?php print $GEDCOM; ?>&tab="+selectedTab;
	}
-->
</script>

<?php

// Load Lightbox javascript and css files
// require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';

// Find if indi and family associated media exists and then count them ( $tot_med_ct)
require_once WT_ROOT.'includes/media_reorder_count.php';

	// If in re-order mode do not show header links, but instead, show drag and drop title.
	if (isset($reorder) && $reorder==1){
		echo "<center><b>", i18n::translate('Drag-and-drop thumbnails to re-order media items'), "</b></center>" ;
		echo "<br />";

	}else{
		//Show Lightbox-Album header Links
		//print "<br />";
		echo '<table border="0" width="75%"><tr>';
		// print "<td class=\"width10 center wrap\" valign=\"top\"></td>";

		if ($LB_AL_HEAD_LINKS == "icon" || (!WT_USER_IS_ADMIN && !WT_USER_CAN_EDIT)) {
		print "<td>";
		}

		// Configuration
        if (WT_USER_IS_ADMIN) {
			if ($LB_AL_HEAD_LINKS == "both") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				// print "<a href=\"".encode_url("module.php?mod=lightbox&pgvaction=lb_editconfig&pid={$pid}&gedcom={$GEDCOM}&tab=4")."\">";
				print "<a href=\"javascript:goto_config_lightbox()\">";
				print "<img src=\"modules/lightbox/images/image_edit.gif\" class=\"icon\" title=\"".i18n::translate('Lightbox-Album Configuration')."\" alt=\"".i18n::translate('Lightbox-Album Configuration')."\" /><br />" ;
				print "" . i18n::translate('Lightbox-Album Configuration') . "&nbsp;";
				print "</a>";
				print "</td>";
				// print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "text") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				print "<a href=\"javascript:goto_config_lightbox()\">";
				print "" . i18n::translate('Lightbox-Album Configuration') . "&nbsp;";
				print "</a>";
				print "</td>";
	        //    print "<td width=\"5%\">&nbsp;</td>";
	            print "\n";
			}else if ($LB_AL_HEAD_LINKS == "icon") {
				print "&nbsp;&nbsp;&nbsp;";
	            print "<a href=\"javascript:goto_config_lightbox()\">";
				print "<img src=\"modules/lightbox/images/image_edit.gif\" class=\"icon\" title=\"".i18n::translate('Lightbox-Album Configuration')."\" alt=\"".i18n::translate('Lightbox-Album Configuration')."\" />" ;
				print "</a>";
				print "\n";
			}
        }

		//Add a new multimedia object
        if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
	            print "<a href=\"javascript: album_add()\"> ";
				print "<img src=\"modules/lightbox/images/image_add.gif\" class=\"icon\" title=\"".i18n::translate('Add a new Multimedia Object to this Individual')."\" alt=\"".i18n::translate('Add a new Multimedia Object to this Individual')."\" /><br />" ;
				print "" . i18n::translate('Add a new Media Object') . "&nbsp;";
	            print " </a> ";
	            print "</td>";
	            //print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "text") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
	            print "<a href=\"javascript: album_add()\"> ";
				print "" . i18n::translate('Add a new Media Object') . "&nbsp;";
	            print " </a> ";
	            print "</td>";
	            //print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "icon") {
				print "&nbsp;&nbsp;&nbsp;";
	            print "<a href=\"javascript: album_add()\"> ";
				print "<img src=\"modules/lightbox/images/image_add.gif\" class=\"icon\" title=\"".i18n::translate('Add a new Multimedia Object to this Individual')."\" alt=\"".i18n::translate('Add a new Multimedia Object to this Individual')."\" />" ;
	            print "</a>";
				print "\n";
			}
        }

		//Link to an existing item
        if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
	            print "<a href=\"javascript: album_link()\"> ";
				print "<img src=\"modules/lightbox/images/image_link.gif\" class=\"icon\" title=\"".i18n::translate('Link this Individual to an existing Multimedia Object')."\" alt=\"".i18n::translate('Link this Individual to an existing Multimedia Object')."\" /><br />" ;
				print "" . i18n::translate('Link to an existing Media Object') . "&nbsp;";
	            print " </a> ";
	            print "</td>";
				//    print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "text") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
	            print "<a href=\"javascript: album_link()\"> ";
				print "" . i18n::translate('Link to an existing Media Object') . "&nbsp;";
	            print " </a> ";
	            print "</td>";
				//    print "<td width=\"5%\">&nbsp;</td>";
			}else if ($LB_AL_HEAD_LINKS == "icon") {
				print "&nbsp;&nbsp;&nbsp;";
	            print "<a href=\"javascript: album_link()\">";
				print "<img src=\"modules/lightbox/images/image_link.gif\" class=\"icon\" title=\"".i18n::translate('Link this Individual to an existing Multimedia Object')."\" alt=\"".i18n::translate('Link this Individual to an existing Multimedia Object')."\" />" ;
	            print "</a> ";
				print "\n";
			}else{
			}
        }
/*
		// Album Reorder Media -----
		if (WT_USER_CAN_EDIT) {
			if ($LB_AL_HEAD_LINKS == "both") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				print "<a href=\"".encode_url(WT_SCRIPT_NAME."?pid={$pid}&tab={$tabno}&reorder=1")."\">" ;
				print "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".i18n::translate('Re-order media')."\" alt=\"".i18n::translate('Re-order media')."\" /><br />" ;
				print "" . i18n::translate('Re-order media') . "&nbsp;";
				print '</a>';
				print "</td>";
				// print "<input type=\"hidden\" name=\"reorder\" value=\"1\" />";
				//print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "text") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				print "<a href=\"".encode_url(WT_SCRIPT_NAME."?pid={$pid}&tab={$tabno}&reorder=1")."\">" ;
				print "" . i18n::translate('Re-order media') . "&nbsp;";
				print '</a>';
				print "</td>";
				//print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "icon") {
				print "&nbsp;&nbsp;&nbsp;";
				print "<a href=\"".encode_url(WT_SCRIPT_NAME."?pid={$pid}&tab={$tabno}&reorder=1")."\">" ;
				print "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".i18n::translate('Re-order media')."\" alt=\"".i18n::translate('Re-order media')."\" />" ;
				print '</a>';
				//print "<td width=\"5%\">&nbsp;</td>";
			}
		}
*/

		// Popup Reorder Media -----
		if (WT_USER_CAN_EDIT ) {
			if ($LB_AL_HEAD_LINKS == "both") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				print "<a href=\"javascript: reorder_media()\">" ;
				print "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".i18n::translate('Re-order media (window)')."\" alt=\"".i18n::translate('Re-order media (window)')."\" /><br />" ;
				//print "" . i18n::translate('Re-order media (window)') . "&nbsp;";
				print "" . i18n::translate('Re-order media (window)') . "&nbsp;";
				print '</a>';
				print "</td>";
				//print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "text") {
				print "<td class=\"width15 center wrap\" valign=\"top\">";
				print "<a href=\"javascript: reorder_media()\">" ;
				// print "" . i18n::translate('Re-order media (window)') . "&nbsp;";
				print "" . i18n::translate('Re-order media') . "&nbsp;";
				print '</a>';
				print "</td>";
				//print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}else if ($LB_AL_HEAD_LINKS == "icon") {
				print "&nbsp;&nbsp;&nbsp;&nbsp;";
				print "<a href=\"javascript: reorder_media()\">" ;
				print "<img src=\"modules/lightbox/images/images.gif\" class=\"icon\" title=\"".i18n::translate('Re-order media (window)')."\" alt=\"".i18n::translate('Re-order media (window)')."\" /><br />" ;
				print '</a>';
				//print "<td width=\"5%\">&nbsp;</td>";
				print "\n";
			}
		}


		if ($LB_AL_HEAD_LINKS == "icon" || (!WT_USER_IS_ADMIN && !WT_USER_CAN_EDIT)) {
		print "</td>";
		}

		print "</tr></table>";
	}
?>
