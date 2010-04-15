<?php
/**
 * Media Link Assistant Control module for phpGedView
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage GEDFact_assistant
 * @version $Id$
*/

// GEDFact Media assistant replacement code for inverselink.php: ===========================

//-- extra page parameters and checking
$more_links		= safe_REQUEST($_REQUEST, 'more_links', WT_REGEX_UNSAFE);
$exist_links	= safe_REQUEST($_REQUEST, 'exist_links', WT_REGEX_UNSAFE);
$gid			= safe_GET_xref('gid');
$update_CHAN	= safe_REQUEST($_REQUEST, 'preserve_last_changed', WT_REGEX_UNSAFE);


if (empty($linktoid) || empty($linkto)) {
	$paramok = false;
	$toitems = "";
} else {
	switch ($linkto) {
	case 'person':
		$toitems = i18n::translate('To Person');
		break;
	case 'family':
		$toitems = i18n::translate('To Family');
		break;
	case 'source':
		$toitems = i18n::translate('To Source');
		break;
	}
}
if (WT_USER_IS_ADMIN) {
	print_simple_header(i18n::translate('Link Media')." ".$toitems);
}else{
	print_simple_header(i18n::translate('Admin'));
	echo i18n::translate('Unable to authenticate user.');
}

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';


//-- check for admin
//$paramok =  WT_USER_CAN_EDIT;
$paramok =  WT_USER_GEDCOM_ADMIN;
if (!empty($linktoid)) $paramok = displayDetailsById($linktoid);

if ($action == "choose" && $paramok) {

	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	// Javascript variables
	var id_empty = "<?php echo i18n::translate('When adding a Link, the ID field cannot be empty.'); ?>";
	
	var pastefield;
	var language_filter, magnify;
	language_filter = "";
	magnify = "";

	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}

	function paste_id(value) {
		pastefield.value = value;
	}

	function paste_char(value, lang, mag) {
		pastefield.value += value;
		language_filter = lang;
		magnify = mag;
	}
	
	function blankwin() {
		if (document.getElementById('gid').value == "" || document.getElementById('gid').value.length<=1) {
			alert(id_empty);
		}else{
			var iid = document.getElementById('gid').value;
			var winblank = window.open('module.php?mod=GEDFact_assistant&pgvaction=_MEDIA/media_query_3a&iid='+iid, 'winblank', 'top=100, left=200, width=400, height=20, toolbar=0, directories=0, location=0, status=0, menubar=0, resizable=1, scrollbars=1');
		}
	}
	
	var GEDFact_assist = "installed";
//-->
	</script>
	<script src="webtrees.js" language="JavaScript" type="text/javascript"></script>
	<link href ="modules/GEDFact_assistant/css/media_0_inverselink.css" 	rel="stylesheet" type="text/css" media="screen" />

	<?php
	echo '<form name="link" method="get" action="inverselink.php">';
	// echo '<input type="hidden" name="action" value="choose" />';
	echo '<input type="hidden" name="action" value="update" />';
	if (!empty($mediaid)) {
		echo '<input type="hidden" name="mediaid" value="', $mediaid, '" />';
	}
	if (!empty($linktoid)) {
		echo '<input type="hidden" name="linktoid" value="', $linktoid, '" />';
	}
	echo '<input type="hidden" name="linkto" value="', $linkto, '" />';
	echo '<input type="hidden" name="ged" value="', $GEDCOM, '" />';
	echo '<table class="facts_table center ', $TEXT_DIRECTION, '">';
	echo '<tr><td class="topbottombar" colspan="2">';
	echo i18n::translate('Link Media'), ' ', $toitems, help_link('add_media_linkid');
	echo '</td></tr><tr><td class="descriptionbox width20 wrap">', i18n::translate('Media ID'), '</td>';
	echo '<td class="optionbox wrap">';
	if (!empty($mediaid)) {
		//-- Get the title of this existing Media item
		$title=
			WT_DB::prepare("SELECT m_titl FROM {$TBLPREFIX}media where m_media=? AND m_gedfile=?")
			->execute(array($mediaid, WT_GED_ID))
			->fetchOne();
		if ($title) {
			echo '<b>', PrintReady($title), '</b>&nbsp;&nbsp;&nbsp;';
			if ($TEXT_DIRECTION=="rtl") echo getRLM();
			echo '(', $mediaid, ')';
			if ($TEXT_DIRECTION=="rtl") echo getRLM();
		} else {
			echo '<b>', $mediaid, '</b>';
		}
		echo '<table><tr><td>';
		//-- Get the filename of this existing Media item
		$filename=
			WT_DB::prepare("SELECT m_file FROM {$TBLPREFIX}media where m_media=? AND m_gedfile=?")
			->execute(array($mediaid, WT_GED_ID))
			->fetchOne();
		$filename = str_replace(" ", "%20", $filename);
		// $thumbnail = thumbnail_file($filename, false, false);
		$thumbnail = str_replace("media/", "media/thumbs/", $filename);
		echo '<img src = ', $thumbnail, ' class="thumbheight" />';
		echo '</td></tr></table>';
		echo '</td></tr>';
		echo '<tr><td class="descriptionbox width20 wrap">', i18n::translate('Links'), '</td>';
		echo '<td class="optionbox wrap">';
		require 'modules/GEDFact_assistant/_MEDIA/media_query_1a.php';
		echo '</td></tr>';
	}
	
	if (!isset($linktoid)) { $linktoid = ""; }
	
	echo '<tr><td class="descriptionbox wrap">';
	echo i18n::translate('Add links');
	echo '<td class="optionbox wrap ">';
	if ($linktoid=="") {
		// ----
	} else {
		$record=Person::getInstance($linktoid);
		echo '<b>', PrintReady($record->getFullName()), '</b>&nbsp;&nbsp;&nbsp;';
		if ($TEXT_DIRECTION=="rtl") print getRLM();
		echo '(', $linktoid, ')';
		if ($TEXT_DIRECTION=="rtl") print getRLM();
	}
	echo '<table><tr><td>';
		print "<input type=\"text\" name=\"gid\" id=\"gid\" size=\"6\" value=\"\" />";
		// echo ' Enter Name or ID &nbsp; &nbsp; &nbsp; <b>OR</b> &nbsp; &nbsp; &nbsp;Search for ID ';
	echo '</td><td style=" padding-bottom:2px; vertical-align:middle">';
		echo '&nbsp;';
		if (isset($WT_IMAGES["add"]["other"])) { 
			echo '<a href="#"><img style="border-style:none;" src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["add"]["other"], '" alt="', i18n::translate('Add'), ' "title="', i18n::translate('Add'), '" align="middle" name="addLink" value="" onClick="javascript:blankwin(); return false;" />';
			} else {
			echo '<button name="addLink" value="" type="button" onClick="javascript:blankwin(); return false;">', i18n::translate('Add'), '</button>';
		}
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		print_findindi_link("gid", "");
		echo '&nbsp;';
		print_findfamily_link("gid");
		echo '&nbsp;';
		print_findsource_link("gid");
	echo '</td></tr></table>';
	print "<sub>" . i18n::translate('Enter or search for the ID of the person, family, or source to which this media item should be linked.') . "</sub>";

	
	echo '<br /><br />';
	echo '<input type="hidden" name="idName" id="idName" size="36" value="Name of ID" />';
	require 'modules/GEDFact_assistant/_MEDIA/media_query_2a.php';
	echo '</td></tr>';
	// Admin Option CHAN log update override =======================
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox ", $TEXT_DIRECTION, " wrap width25\">";
		echo i18n::translate('Admin Option'), "</td><td class=\"optionbox wrap\">\n";
		echo "<input type=\"checkbox\" name=\"preserve_last_changed\" value=\"no_change\"/ >\n";
		echo i18n::translate('Do not update the CHAN (Last Change) records'), help_link('no_update_CHAN'), '<br /><br />';
		echo "</td></tr>\n";
	}
	echo '</tr>';
	echo '<input type="hidden" name="more_links" value="No_Values" />';
	echo '<input type="hidden" name="exist_links" value="No_Values" />';
	echo '<tr><td colspan="2">';
	echo '</td></tr>';
	echo '<tr><td class="topbottombar" colspan="2">';
	echo '<center><input type="submit" value="', i18n::translate('Save'), '" onclick="javascript:shiftlinks();" />';
	echo '</center></td></tr>';
	require 'modules/GEDFact_assistant/_MEDIA/media_7_parse_addLinksTbl.php';
	echo '</table>';
	echo '</form>';
	echo '<br/><br/><center><a href="javascript:;" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close(); ">', i18n::translate('Close Window'), '</a><br /></center>';
	// print_simple_footer();
	
} elseif ($action == "update" && $paramok) {

	echo "<b>", $mediaid, "</b><br/><br />";
	
	// Unlink records indicated by radio button =========
	if (isset($exist_links) && $exist_links!="No_Values") {
		$exist_links = substr($exist_links, 0, -1);
		$rem_exist_links = (explode(", ", $exist_links));
		foreach ($rem_exist_links as $remLinkId) {
			global $linkToId;
			$linkToId = PrintReady($remLinkId);
			echo i18n::translate('Link to %s deleted', $remLinkId);
			echo '<br />';
			if ($update_CHAN=='no_change') {
				unlinkMedia($remLinkId, 'OBJE', $mediaid, 1, false);
			} else {
				unlinkMedia($remLinkId, 'OBJE', $mediaid, 1, true);
			}
		}
		echo '<br />';
	}else{
		// echo nothing and do nothing
	}
	
	// Add new Links ====================================
	if (isset($more_links) && $more_links!="No_Values" && $more_links!=",") {
		$more_links = substr($more_links, 0, -1);
		$add_more_links = (explode(", ", $more_links));
		foreach ($add_more_links as $addLinkId) {
			global $unlinkFromId;
			$$unlinkFromId = PrintReady($addLinkId);
			echo i18n::translate('Link to %s added', $addLinkId);
			if ($update_CHAN=='no_change') {
				linkMedia($mediaid, $addLinkId, 1, false);
			} else {
				linkMedia($mediaid, $addLinkId, 1, true);
			}
			echo '<br />';
		}
		echo '<br />';
	}else if ($more_links==",") {
		// echo nothing and do nothing
	}else{
		//	echo $mediaid, $pgv_lang["media_now_linked to"], '(', $gid, ')<br />';
		//	linkMedia($mediaid, $gid);
	}
	
	if ($update_CHAN=='no_change') {
		echo i18n::translate('No CHAN (Last Change) records were updated');
		echo '<br />';
	}
	
	echo '<br/><br/><center><a href="javascript:;" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close(); ">', i18n::translate('Close Window'), '</a><br /></center>';
	print_simple_footer();
		
} else {
	// echo '<center>You must be logged in as an Administrator<center>';
	echo '<br/><br/><center><a href="javascript:;" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close();">', i18n::translate('Close Window'), '</a><br /></center>';
	//print_simple_footer();
}

?>
