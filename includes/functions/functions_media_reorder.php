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

function media_reorder_row($rowm) {
	$media = WT_Media::getInstance($rowm['m_media']);

	if (!$media->canDisplayDetails() || !canDisplayFact($rowm['m_media'], $rowm['m_gedfile'], $rowm['mm_gedrec'])) {
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
