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


/**
 * print a media row in a table
 * @param string $rtype whether this is a 'new', 'old', or 'normal' media row... this is used to determine if the rows should be printed with an outline color
 * @param array $rowm        An array with the details about this media item
 * @param string $pid        The record id this media item was attached to
 */
function media_reorder_row($rtype, $rowm, $pid) {
	global $WT_IMAGES, $MEDIA_DIRECTORY;
	global $GEDCOM, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $SEARCH_SPIDER;
	global $t, $n, $item, $items, $p, $edit, $reorder, $note, $rowm;
	global $order1, $mediaType;

	if (!isset($rowm)) {
		$rowm=$row;
	}
	echo "<li class=\"facts_value\" style=\"list-style:none;cursor:move;margin-bottom:2px;\" id=\"li_" . $rowm['m_media'] . "\" >";

    //echo $rtype." ".$rowm["m_media"]." ".$pid;
    if (!WT_Media::getInstance($rowm['m_media'])->canDisplayDetails() || !canDisplayFact($rowm['m_media'], $rowm['m_gedfile'], $rowm['mm_gedrec'])) {
        //echo $rowm['m_media']." no privacy ";
        return false;
    }

    $styleadd="";
    if ($rtype=='new') $styleadd = "change_new";
    if ($rtype=='old') $styleadd = "change_old";

    // NOTE Start printing the media details

    $thumbnail = thumbnail_file($rowm["m_file"], true, false, $pid);
    // $isExternal = stristr($thumbnail,"://");
	$isExternal = isFileExternal($thumbnail);

    $linenum = 0;



    // NOTE Get the title of the media
    $mediaTitle = $rowm["m_titl"];
    $subtitle = get_gedcom_value("TITL", 2, $rowm["mm_gedrec"]);

    if (!empty($subtitle)) $mediaTitle = $subtitle;
		$mainMedia = check_media_depth($rowm["m_file"], "NOTRUNC");
    if ($mediaTitle=="") $mediaTitle = basename($rowm["m_file"]);

		echo "<table class=\"pic\"><tr>";
		echo "<td width=\"80\" valign=\"top\" align=\"center\" >";

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($mainMedia, $thumbnail, $rowm["m_media"], $mediaTitle, '');

		//-- Thumbnail field
		echo "<img src=\"".$mediaInfo['thumb']."\" height=\"38\"";

		if (strpos($rowm['m_gedrec'], "1 SOUR")!==false) {
			echo " alt=\"" . htmlspecialchars($mediaTitle) . "\" title=\"" . htmlspecialchars($mediaTitle) . " Source info available\">";
		} else {
			echo " alt=\"" . htmlspecialchars($mediaTitle) . "\" title=\"" . htmlspecialchars($mediaTitle) . "\">";
		}

		//print media info
		$ttype2 = preg_match("/\d TYPE (.*)/", $rowm["m_gedrec"], $match);
		if ($ttype2>0) {
			$mediaType = WT_Gedcom_Tag::getFileFormTypeValue($match[1]);
			// echo "<br><span class=\"label\">".WT_I18N::translate('Type').": </span> <span class=\"field\">$mediaType</span>";
		}

		echo "</td><td>&nbsp;</td>";
		echo "<td valign=\"top\" align=\"left\">";
		//echo "<font color=\"blue\">";
		echo $rowm['m_media'];
		//echo "</font>";

		echo "<b>";
		echo "&nbsp;&nbsp;" . $mediaType;
		echo "</b>";

		echo "<br>";
		echo $mediaTitle;

		echo "</td>";
		echo "</tr>";
		echo "</table>";
	if (!isset($j)) {
		$j=0;
	} else {
		$j=$j;
	}
	$media_data = $rowm['m_media'];
	echo "<input type=\"hidden\" name=\"order1[", $media_data, "]\" value=\"", $j, "\">";

	echo "</li>";
	return true;
}
