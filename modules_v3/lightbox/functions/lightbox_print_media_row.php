<?php
// Lightbox Album module for webtrees
//
// Display media Items using Lightbox
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2009  PGV Development Team.  All rights reserved.
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
function lightbox_print_media_row($rtype, $rowm, $pid) {

	global $TEXT_DIRECTION;
	global $item, $sort_i, $notes;

	$reorder=safe_get('reorder', '1', '0');

	$mainMedia = check_media_depth($rowm['m_file'], 'NOTRUNC');
	// If media file is missing from "media" directory, but is referenced in Gedcom
	if (!media_exists($mainMedia)) {
		if (!file_exists($rowm['m_file']) && !isset($rowm['m_file'])) {
			echo '<tr>';
			echo '<td valign="top" rowspan="2" >';
			echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/transp80px.gif" height="82px" alt=""></img>';
			echo '</td>';
			echo '<td class="description_box nowrap" valign="top" colspan="3">';
			echo '<center><br><img src="', WT_THEME_URL, 'images/media.gif" height="30">';
			echo '<p class="ui-state-error">', WT_I18N::translate('The file “%s” does not exist.', $rowm['m_file']), '</p>';
			echo '</td>';
			echo '</tr>';
		} else if (!file_exists($rowm['m_file'])) {
			echo '<li class="li_norm" >';
			echo '<table class="pic" width="50px" border="0">';
			echo '<tr>';
			echo '<td valign="top" rowspan="2" >';
			echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/transp80px.gif" height="100px" alt=""></img>';
			echo '</td>';
			echo '<td class="description_box nowrap" valign="top" colspan="3">';
			echo '<center><br><img src="', WT_THEME_URL, 'images/media.gif" height="30">';
			echo '<p class="ui-state-error">', WT_I18N::translate('The file “%s” does not exist.', $rowm['m_file']), '</p>';
			echo '</td>';
			echo '</tr>';
		} else {
			echo '<li class="li_norm" >';
			echo '<table class="pic" width="50px" border="0" >';
		}
	// Else Media files are present in "media" directory
	} else {
		//If media is linked to a 'private' person
		if (!WT_Media::getInstance($rowm['m_media'])->canDisplayDetails() || !canDisplayFact($rowm['m_media'], $rowm['m_gedfile'], $rowm['mm_gedrec'])) {
			return false;
		} else {
			// Media is NOT linked to private person
			// If reorder media has been clicked
			if (isset($reorder) && $reorder==1) {
				echo '<li class="facts_value" style="border:0px;" id="li_', $rowm['m_media'], '" >';

			// Else If reorder media has NOT been clicked
			// Highlight Album Thumbnails - Changed=new (blue), Changed=old (red), Changed=no (none)
			} else if ($rtype=='new') {
				echo '<li class="li_new">';
			} else if ($rtype=='old') {
				echo '<li class="li_old">';
			} else {
				echo '<li class="li_norm">';
			}
		}
	}

	// Add blue or red borders
	$styleadd='';
	if ($rtype=='new') $styleadd = 'change_new';
	if ($rtype=='old') $styleadd = 'change_old';

	// NOTE Start printing the media details
	if (!media_exists($mainMedia)) {
		if (!media_exists($rowm['m_file'])) {
			$thumbnail = '';
			$isExternal = ''; // isFileExternal($thumbnail);
		} else {
			$thumbnail = thumbnail_file($rowm['m_file'], true, false, $pid);
			$isExternal = isFileExternal($thumbnail);
		}
	} else {
		$thumbnail = thumbnail_file($mainMedia, true, false, $pid);
		$isExternal = isFileExternal($thumbnail);
		// echo $thumbnail;
	}
	$linenum = 0;

	// If Fact details can be shown --------------------------------------------------------------------------------------------
	if (canDisplayFact($pid, $rowm['m_file'], $rowm['mm_gedrec'])) {

		//  Get the title of the media
		$media=WT_Media::getInstance($rowm['m_media']);
		$mediaTitle = $media->getFullName();

		$mainMedia = check_media_depth($rowm['m_file'], 'NOTRUNC');
		$mainFileExists = true;
		$imgsize = findImageSize($mainMedia);
		$imgwidth = $imgsize[0]+40;
		$imgheight = $imgsize[1]+150;

		// Get the tooltip link for source
		$sour = WT_Source::getInstance(get_gedcom_value('SOUR', 1, $rowm['m_gedrec']));

		//Get media item Notes
		$haystack = $rowm['m_gedrec'];
		$needle   = '1 NOTE';
		$before   = substr($haystack, 0, strpos($haystack, $needle));
		$after    = substr(strstr($haystack, $needle), strlen($needle));
		$final    = $before.$needle.$after;
		$notes    = htmlspecialchars(print_fact_notes($final, 1, true, true), ENT_QUOTES);

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($mainMedia, $thumbnail, $rowm['m_media'], $mediaTitle, $notes);

		//text alignment for Tooltips
		if ($TEXT_DIRECTION=='rtl') {
			$alignm = 'right';
			$left = 'true';
		} else {
			$alignm = 'left';
			$left = 'false';
		}

		// Tooltip Options
		$tt_opts = ", BALLOON," . true ; // true=balloon, false=normal
		$tt_opts .= ", LEFT," . $left;
		$tt_opts .= ", ABOVE, true";
		$tt_opts .= ", TEXTALIGN, '" . $alignm . "'";
		$tt_opts .= ", WIDTH, -480 ";
		$tt_opts .= ", BORDERCOLOR, ''";
		$tt_opts .= ", TITLEBGCOLOR, ''";
		$tt_opts .= ", CLOSEBTNTEXT, 'X'";
		$tt_opts .= ", CLOSEBTN, false";
		$tt_opts .= ", CLOSEBTNCOLORS, ['#ff0000', '#ffffff', '#ffffff', '#ff0000']";
		$tt_opts .= ", OFFSETX, -30";
		$tt_opts .= ", OFFSETY, 110";
		$tt_opts .= ", STICKY, true";
		$tt_opts .= ", PADDING, 6";
		$tt_opts .= ", CLICKCLOSE, true";
		$tt_opts .= ", DURATION, 8000";
		$tt_opts .= ", BGCOLOR, '#f3f3f3'";
		$tt_opts .= ", JUMPHORZ, 'true' ";
		$tt_opts .= ", JUMPVERT, 'false' ";
		$tt_opts .= ", DELAY, 0";

		// Prepare Below Thumbnail  menu ----------------------------------------------------
		if ($TEXT_DIRECTION== 'rtl') {
			$submenu_class = 'submenuitem_rtl';
			$submenu_hoverclass = 'submenuitem_hover_rtl';
		} else {
			$submenu_class = 'submenuitem';
			$submenu_hoverclass = 'submenuitem_hover';
		}
		$menu = new WT_Menu();
		// Truncate media title to 13 chars (45 chars if Streetview) and add ellipsis
		$mtitle = $mediaTitle;
		if (strpos($rowm['m_file'], 'http://maps.google.')===0) {
			if (utf8_strlen($mtitle)>16) {
				$mtitle = utf8_substr($rowm['m_file'], 0, 45).WT_I18N::translate('…');
			}
		} else {
			if (utf8_strlen($mtitle)>16) {
				$mtitle = utf8_substr($mtitle, 0, 13).WT_I18N::translate('…');
			}
		}

		// Continue menu construction
		// If media file is missing from 'media' directory, but is referenced in Gedcom
		if (!media_exists($rowm['m_file']) && !media_exists($mainMedia)) {
			$menu->addLabel("<img src=\"{$thumbnail}\" style=\"display:none;\" alt=\"\" title=\"\">" . WT_I18N::translate('Edit')." (". $rowm['m_media'].")", 'right');
		} else {
			$menu->addLabel("<img src=\"{$thumbnail}\" style=\"display:none;\" alt=\"\" title=\"\">" . $mtitle, 'right');
		}
		// Next line removed to avoid gallery thumbnail duplication
		// $menu['link'] = mediaInfo['url'];

		if ($rtype=='old') {
			// Do not print menu if item has changed and this is the old item
		} else {
			// Continue printing menu
			$menu->addClass('', '', 'submenu');

			// View Notes
			if (strpos($rowm['m_gedrec'], "\n1 NOTE")) {
				$submenu = new WT_Menu('&nbsp;&nbsp;' . WT_I18N::translate('View Notes') . '&nbsp;&nbsp;', '#', 'right');
				// Notes Tooltip ----------------------------------------------------
				$sonclick  = 'TipTog(';
				// Contents of Notes
				$sonclick .= "'";
				$sonclick .= "&lt;font color=#008800>&lt;b>" . WT_I18N::translate('Notes') . ":&lt;/b>&lt;/font>&lt;br>";
				$sonclick .= $notes;
				$sonclick .= "'";
				// Notes Tooltip Parameters
				$sonclick .= $tt_opts;
				$sonclick .= ");";
				$sonclick .= "return false;";
				$submenu->addOnclick($sonclick);
				$submenu->addClass($submenu_class, $submenu_hoverclass);
				$menu->addSubMenu($submenu);
			}
			//View Details
			$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('View Details') . "&nbsp;&nbsp;", WT_SERVER_NAME.WT_SCRIPT_PATH . "mediaviewer.php?mid=".$rowm['m_media'].'&amp;ged='.WT_GEDURL, 'right');
			$submenu->addClass($submenu_class, $submenu_hoverclass);
			$menu->addSubMenu($submenu);
			//View Source
			if ($sour && $sour->canDisplayDetails()) {
				$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('View Source') . "&nbsp;&nbsp;", $sour->getHtmlUrl(), "right");
				$submenu->addClass($submenu_class, $submenu_hoverclass);
				$menu->addSubMenu($submenu);
			}
			if (WT_USER_CAN_EDIT) {
				// Edit Media
				$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Edit media') . "&nbsp;&nbsp;", "#", "right");
				$submenu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid={$rowm['m_media']}&amp;linktoid={$rowm['mm_gid']}', '_blank', edit_window_specs);");
				$submenu->addClass($submenu_class, $submenu_hoverclass);
				$menu->addSubMenu($submenu);
				if (WT_USER_IS_ADMIN) {
					// Manage Links
					if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
						$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Manage links') . "&nbsp;&nbsp;", "#", "right");
						$submenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=manage', '_blank', find_window_specs);");
						$submenu->addClass($submenu_class, $submenu_hoverclass);
						$menu->addSubMenu($submenu);
					} else {
						$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Set link') . "&nbsp;&nbsp;", "#", "right", "right");

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Person'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=person', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Family'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=family', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Source'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=source', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenuitem_hover', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$menu->addSubMenu($submenu);
					}
					// Unlink Media
					$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Unlink Media') . "&nbsp;&nbsp;", "#", "right");
					$submenu->addOnclick("return delete_record('$pid', 'OBJE', '".$rowm['m_media']."');");
					$submenu->addClass($submenu_class, $submenu_hoverclass);
					$menu->addSubMenu($submenu);
				}
			}
		}

		// Check if allowed to View media
		if ($isExternal || media_exists($thumbnail) && canDisplayFact($rowm['m_media'], $rowm['m_gedfile'], $rowm['m_gedrec'])) {
			$mainFileExists = false;

			// Get Media info
			if ($isExternal || media_exists($rowm['m_file']) || media_exists($mainMedia)) {
				$mainFileExists = true;
				$imgsize = findImageSize($rowm['m_file']);
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;

				// Start Thumbnail Enclosure table ---------------------------------------------
				// Pull table up 90px if media object is a "streetview"
				if (strpos($rowm['m_file'], 'http://maps.google.')===0) {
					echo "<table width=\"10px\" style=\"margin-top:-90px;\" class=\"pic\" border=\"0\"><tr>";
				} else {
					echo "<table width=\"10px\" class=\"pic\" border=\"0\"><tr>";
				}
				echo "<td align=\"center\" rowspan=\"2\" >";
				echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/transp80px.gif" height="100px" alt=""></img>';
				echo "</td>";

				// Check for Notes associated media item
				if ($reorder) {
					// If reorder media has been clicked
					echo "<td width=\"90% align=\"center\"><b><font size=\"2\" style=\"cursor:move;margin-bottom:2px;\">" . $rowm['m_media'] . "</font></b></td>";
					echo "</tr>";
				}
				$item++;

				echo "<td colspan=\"3\" valign=\"middle\" align=\"center\" >";
				// If not reordering, enable Lightbox or popup and show thumbnail tooltip ------
				if (!$reorder) {
					echo '<a href="', $mediaInfo['url'], '">';
				}
			}

			// Now finally print the thumbnail -----------------------------------------------------
			$height = 78;
			$size = findImageSize($mediaInfo['thumb']);
			if ($size[1]<$height) $height = $size[1];
			echo "<img src=\"{$mediaInfo['thumb']}\" height=\"{$height}\"" ;

			// print browser tooltips associated with image ----------------------------------------
			echo " alt=\"\" title=\"" . Printready(strip_tags($mediaTitle)) . "\">";

			// Close anchor --------------------------------------------------------------
			if ($mainFileExists) {
				echo "</a>";
			}
			echo "</td></tr>";

			//View Edit Menu ----------------------------------
			if (!$reorder) {
				// If not reordering media print View or View-Edit Menu
				echo "<tr>";
				echo "<td width=\"5px\"></td>";
				echo "<td valign=\"bottom\" align=\"center\" class=\"nowrap\">";
				echo $menu->getMenu();
				echo "</td>";
				echo "<td width=\"5px\"></td>";
				echo "</tr>";
			}
			// echo "</table>";
		}
	} // NOTE End If Show fact details

	// If media file is missing but details are in Gedcom then add the menu as well
	//if (!media_exists($rowm['m_file'])) {
	if (!media_exists($mainMedia) && !media_exists($rowm['m_file'])) {
		echo '<tr>';
		echo '<td ></td>';
		echo '<td valign="bottom" align="center" class="nowrap">';
		echo $menu->getMenu();
		echo '</td>';
		echo '<td ></td>';
		echo '</tr>';
	}
	//close off the table
	echo '</table>';
	$media_data = $rowm['m_media'];
	echo '<input type="hidden" name="order1[', $media_data, ']" value="', $sort_i, '">';
	$sort_i++;
	echo '</li>';
	return true;
}
