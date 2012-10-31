<?php
// Reorder media Items using drag and drop
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_print_facts.php';

function media_reorder_row($rowm) {
	$media = WT_Media::getInstance($rowm['m_media']);

	if (!$media->canDisplayDetails()) {
		return false;
	}

	echo "<li class=\"facts_value\" style=\"list-style:none;cursor:move;margin-bottom:2px;\" id=\"li_" . $media->getXref() . "\" >";
	echo "<table class=\"pic\"><tr>";
	echo "<td width=\"80\" valign=\"top\" align=\"center\" >";
	echo $media->displayMedia();
	echo "</td><td>&nbsp;</td>";
	echo "<td valign=\"top\" align=\"left\">";
	echo $media->getXref();
	echo "<b>";
	echo "&nbsp;&nbsp;", WT_Gedcom_Tag::getFileFormTypeValue($media->getMediaType());
	echo "</b>";
	echo "<br>";
	echo $media->getFullName();
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<input type=\"hidden\" name=\"order1[",$media->getXref(), "]\" value=\"0\">";
	echo "</li>";
	return true;
}

$controller->addInlineJavascript('
	jQuery("#reorder_media_list").sortable({forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.7, cursor: "move", axis: "y"});

	//-- update the order numbers after drag-n-drop sorting is complete
	jQuery("#reorder_media_list").bind("sortupdate", function(event, ui) {
			jQuery("#"+jQuery(this).attr("id")+" input").each(
				function (index, value) {
					value.value = index+1;
				}
			);
		});
	');

echo '<br><b>', WT_I18N::translate('Re-order media'), '</b>';
echo '&nbsp --- &nbsp;' . WT_I18N::translate('Click a row, then drag-and-drop to re-order media ');

?>
<form name="reorder_form" method="post" action="edit_interface.php">
	<input type="hidden" name="action" value="reorder_media_update">
	<input type="hidden" name="pid" value="<?php echo $pid; ?>">

	<ul id="reorder_media_list">
	<?php
	$person = WT_Person::getInstance($pid);

	//-- find all of the related ids
	$ids = array($person->getXref());
	foreach ($person->getSpouseFamilies() as $family) {
		$ids[] = $family->getXref();
	}

	//-- If they exist, get a list of the sorted current objects in the indi gedcom record  -  (1 _WT_OBJE_SORT @xxx@ .... etc) ----------
	$sort_current_objes = array();
	$sort_ct = preg_match_all('/\n1 _WT_OBJE_SORT @(.*)@/', $person->getGedcomRecord(), $sort_match, PREG_SET_ORDER);
	for ($i=0; $i<$sort_ct; $i++) {
		if (!isset($sort_current_objes[$sort_match[$i][1]])) {
			$sort_current_objes[$sort_match[$i][1]] = 1;
		} else {
			$sort_current_objes[$sort_match[$i][1]]++;
		}
		$sort_obje_links[$sort_match[$i][1]][] = $sort_match[$i][0];
	}

	// create ORDER BY list from Gedcom sorted records list  ---------------------------
	$orderbylist = 'ORDER BY '; // initialize
	foreach ($sort_match as $id) {
		$orderbylist .= "m_media='$id[1]' DESC, ";
	}
	$orderbylist = rtrim($orderbylist, ', ');

	//-- get a list of the current objects in the record
	$current_objes = array();
	$regexp = '/\n\d OBJE @(.*)@/';
	$ct = preg_match_all($regexp, $person->getGedcomRecord(), $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		if (!isset($current_objes[$match[$i][1]])) {
			$current_objes[$match[$i][1]] = 1;
		}  else {
			$current_objes[$match[$i][1]]++;
		}
		$obje_links[$match[$i][1]][] = $match[$i][0];
	}

	$media_found = false;

	// Get the related media items
	$sqlmm =
		"SELECT DISTINCT m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec" .
		" FROM `##media`" .
		" JOIN `##link` ON (m_media=l_to AND m_gedfile=l_file AND l_type='OBJE')" .
		" WHERE m_gedfile=? AND l_from IN (";
	$i=0;
	$vars=array(WT_GED_ID);
	foreach ($ids as $media_id) {
		if ($i>0) $sqlmm .= ",";
		$sqlmm .= "?";
		$vars[]=$media_id;
		$i++;
	}
	$sqlmm .= ')';

	if ($sort_ct>0) {
		$sqlmm .= $orderbylist;
	}

	$rows=WT_DB::prepare($sqlmm)->execute($vars)->fetchAll(PDO::FETCH_ASSOC);

	$foundObjs = array();
	foreach ($rows as $rowm) {
		if (isset($foundObjs[$rowm['m_media']])) {
			if (isset($current_objes[$rowm['m_media']])) {
				$current_objes[$rowm['m_media']]--;
			}
			continue;
		}
		// NOTE: Determine the size of the mediafile
		$imgwidth = 300+40;
		$imgheight = 300+150;
		if (preg_match("'://'", $rowm['m_file'])) {
			if (in_array($rowm['m_ext'], $MEDIATYPE)) {
				$imgwidth = 400+40;
				$imgheight = 500+150;
			} else {
				$imgwidth = 800+40;
				$imgheight = 400+150;
			}
		}
		else if (file_exists(filename_decode(check_media_depth($rowm["m_file"], "NOTRUNC")))) {
			$imgsize = findImageSize(check_media_depth($rowm["m_file"], "NOTRUNC"));
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
		}
		$rows = array();
		$rows['normal'] = $rowm;
		if (isset($current_objes[$rowm['m_media']])) $current_objes[$rowm['m_media']]--;
		foreach ($rows as $rowm) {
			$res = media_reorder_row($rowm);
			$media_found = $media_found || $res;
			$foundObjs[$rowm['m_media']] = true;
		}
	}
	?>
	</ul>
	<?php
		if (WT_USER_IS_ADMIN) {
			echo '<table width=97%><tr><td class="descriptionbox wrap width25">';
			echo WT_Gedcom_Tag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
			if ($NO_UPDATE_CHAN) {
				echo '<input type="checkbox" checked="checked" name="preserve_last_changed">';
			} else {
				echo '<input type="checkbox" name="preserve_last_changed">';
			}
			echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'), '<br>';
			$event = $person->getChangeEvent();
			echo format_fact_date($event, new WT_Person(''), false, true);
			echo '</td></tr></table>';
		}
	?>
	<p id="save-cancel">
		<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
		<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
	</p>
</form>
