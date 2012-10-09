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
 * -----------------------------------------------------------------------------
 * Print the links to media objects
 * @param string $pid        The the xref id of the object to find media records related to
 * @param int $level        The level of media object to find
 * @param boolean $related        Whether or not to grab media from related records
 */
function lightbox_print_media($pid, $level=1, $related=false, $kind=1, $noedit=false) {
	global $GEDCOM, $MEDIATYPE;
	global $res, $rowm;
	global $rownum, $rownum1, $rownum2, $rownum3, $rownum4;

	$ged_id=get_id_from_gedcom($GEDCOM);
	$gedrec = find_gedcom_record($pid, $ged_id, WT_USER_CAN_EDIT);
	$ids = array($pid);

	//-- find all of the related ids
	if ($related) {
		$ct = preg_match_all("/1 FAMS @(.*)@/", $gedrec, $match, PREG_SET_ORDER);
		for ($i=0; $i<$ct; $i++) {
			$ids[] = trim($match[$i][1]);
		}
	}

	//LBox -- if  exists, get a list of the sorted current objects in the indi gedcom record  -  (1 _WT_OBJE_SORT @xxx@ .... etc) ----------
	$sort_current_objes = array();
	if ($level>0) $sort_regexp = "/".$level." _WT_OBJE_SORT @(.*)@/";
	else $sort_regexp = "/_WT_OBJE_SORT @(.*)@/";
	$sort_ct = preg_match_all($sort_regexp, $gedrec, $sort_match, PREG_SET_ORDER);
	for ($i=0; $i<$sort_ct; $i++) {
		if (!isset($sort_current_objes[$sort_match[$i][1]])) $sort_current_objes[$sort_match[$i][1]] = 1;
		else $sort_current_objes[$sort_match[$i][1]]++;
		$sort_obje_links[$sort_match[$i][1]][] = $sort_match[$i][0];
	}

	// create ORDER BY list from Gedcom sorted records list  ---------------------------
	$orderbylist = 'ORDER BY '; // initialize
	foreach ($sort_match as $id) {
		$orderbylist .= "m_media='$id[1]' DESC, ";
	}
	$orderbylist = rtrim($orderbylist, ', ');
	// ---------------------------------------------------------------------------------------------------------------------------------------------------

	//-- get a list of the current objects in the record
	$current_objes = array();
	if ($level>0) $regexp = "/".$level." OBJE @(.*)@/";
	else $regexp = "/OBJE @(.*)@/";
	$ct = preg_match_all($regexp, $gedrec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$ct; $i++) {
		if (!isset($current_objes[$match[$i][1]])) {
			$current_objes[$match[$i][1]] = 1;
		} else {
			$current_objes[$match[$i][1]]++;
		}
		$obje_links[$match[$i][1]][] = $match[$i][0];
	}

	$media_found = false;

	// Get the related media items
	$sqlmm = "SELECT DISTINCT ";
	$sqlmm .= "m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec, mm_gid, mm_gedrec FROM `##media`, `##media_mapping` where ";
	$sqlmm .= "mm_gid IN (";
	$vars=array();
	foreach ($ids as $id) {
		$sqlmm .= "?, ";
		$vars[]=$id;
	}
	$sqlmm = rtrim($sqlmm, ', ');
	$sqlmm .= ") AND mm_gedfile=? AND mm_media=m_media AND mm_gedfile=m_gedfile ";
	$vars[]=WT_GED_ID;
	//-- for family and source page only show level 1 obje references
	if ($level>0) {
		$sqlmm .= "AND mm_gedrec LIKE ?";
		$vars[]="$level OBJE%";
	}

	// Set type of media from call in album
	switch ($kind) {
	case 1:
		$tt=WT_I18N::translate('Photo');
		$sqlmm.="AND (m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ?)";
		$vars[]='%TYPE photo%';
		$vars[]='%TYPE map%';
		$vars[]='%TYPE painting%';
		$vars[]='%TYPE tombstone%';
		break;
	case 2:
		$tt=WT_I18N::translate('Document');
		$sqlmm.="AND (m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ?)";
		$vars[]='%TYPE card%';
		$vars[]='%TYPE certificate%';
		$vars[]='%TYPE document%';
		$vars[]='%TYPE magazine%';
		$vars[]='%TYPE manuscript%';
		$vars[]='%TYPE newspaper%';
		break;
	case 3:
		$tt=WT_I18N::translate('Census');
		$sqlmm.="AND (m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ?)";
		$vars[]='%TYPE electronic%';
		$vars[]='%TYPE fiche%';
		$vars[]='%TYPE film%';
		break;
	case 4:
		$tt=WT_I18N::translate('Other');
		$sqlmm.="AND (m_gedrec NOT LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ? OR m_gedrec LIKE ?)";
		$vars[]='%TYPE %';
		$vars[]='%TYPE coat%';
		$vars[]='%TYPE book%';
		$vars[]='%TYPE audio%';
		$vars[]='%TYPE video%';
		$vars[]='%TYPE other%';
		break;
	case 5:
	default:
		$tt = WT_I18N::translate('Not in DB');
		break;
	}

	if ($sort_ct>0) {
		$sqlmm .= $orderbylist;
	} else {
		$sqlmm .= " ORDER BY mm_gid DESC ";
	}

	$rows=WT_DB::prepare($sqlmm)->execute($vars)->fetchAll(PDO::FETCH_ASSOC);
	$foundObjs = array();
	$numm = count($rows);

	// Begin to Layout the Album Media Rows
	if ($numm>0 || $kind==5) {
		if ($kind!=5) {
			echo '<table cellpadding="0" border="0" width="100%" class="facts_table"><tr>';
			echo '<td width="100" align="center" class="descriptionbox" style="vertical-align:middle;">';
			echo '<b>', $tt, '</b>';
			echo '</td>';
			echo '<td class="facts_value" >';
			echo '<table class="facts_table" width="100%" cellpadding="0"><tr><td >';
			echo '<div id="thumbcontainer', $kind, '">';
			echo '<ul class="section" id="thumblist_', $kind, '">';
		}
		// ==================================================
		// Start pulling media items into thumbcontainer div ==============================
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
			if (isFileExternal($rowm['m_file'])) {
				if (in_array($rowm['m_ext'], $MEDIATYPE)) {
					$imgwidth = 400+40;
					$imgheight = 500+150;
				} else {
					$imgwidth = 800+40;
					$imgheight = 400+150;
				}
			} else if (media_exists(check_media_depth($rowm['m_file'], 'NOTRUNC'))) {
				$imgsize = findImageSize(check_media_depth($rowm['m_file'], 'NOTRUNC'));
				$imgwidth = $imgsize[0]+40;
				$imgheight = $imgsize[1]+150;
			}
			$rows=array();


			//-- if there is a change to this media item then get the
			//-- updated media item and show it
			if (($newrec=find_updated_record($rowm['m_media'], $ged_id)) && $kind!=5  ) {
				$row = array();
				$row['m_media'] = $rowm['m_media'];
				$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
				$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
				if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
				$row['m_gedrec'] = $newrec;
				$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
				$ext = "";
				if ($et>0) $ext = substr(trim($ematch[1]), 1);
				$row['m_ext'] = $ext;
				$row['mm_gid'] = $pid;
				$row['mm_gedrec'] = $rowm['mm_gedrec'];
				$row['m_gedfile'] = $rowm['m_gedfile'];
				$rows['new'] = $row;
				$rows['old'] = $rowm;
			} else {
				if (!isset($current_objes[$rowm['m_media']]) && ($rowm['mm_gid']==$pid)) {
					$rows['old'] = $rowm;
				} else {
					$rows['normal'] = $rowm;
					if (isset($current_objes[$rowm['m_media']])) {
						$current_objes[$rowm['m_media']]--;
					}
				}
			}
			foreach ($rows as $rtype => $rowm) {
				if ($kind!=5) {
					$res = lightbox_print_media_row($rtype, $rowm, $pid);
				}
				$media_found = $media_found || $res;
				$foundObjs[$rowm['m_media']]=true;
			}
		}
		// =====================================================================================
		//-- Objects are removed from the $current_objes list as they are printed.
		//-- Any "Extra" objects left in the list are new objects recently added to the gedcom
		//-- but not yet accepted into the database.
		//-- We will print them too, and put any "Extra Items not in DB" into a new Row.
		// Firstly, get count of Items in Database for this Individual
		$indiobjs = "SELECT ";
		$indiobjs .= "m_media, m_ext, m_file, m_titl, m_gedfile, m_gedrec, mm_gid, mm_gedrec FROM `##media`, `##media_mapping` where ";
		$indiobjs .= "mm_gid=? ";
		$indiobjs .= "AND mm_gedfile=? AND mm_media=m_media AND mm_gedfile=m_gedfile ";
		$vars2=array($pid, WT_GED_ID);
		$rows=WT_DB::prepare($indiobjs)->execute($vars2)->fetchAll(PDO::FETCH_ASSOC);
		$foundObjs = array();
		$numindiobjs = count($rows);

		// Compare Items count in Database versus Item count in GEDCOM
		if ($kind==5 && $ct!=$numindiobjs) {
			// If any items are left in $current_objes list for this individual, put them into $kind 5 ("Not in DB") row
			echo '<table cellpadding="0" border="0" width="100%" class="facts_table"><tr>';
			echo '<td width="100" align="center" class="descriptionbox" style="vertical-align:middle;">';
			echo '<b>', $tt, '</b>';
			echo '</td>';
			echo '<td class="facts_value" >';
			echo '<table class="facts_table" width="100%" cellpadding="0"><tr><td >';
			echo '<div id="thumbcontainer', $kind, '">';
			echo '<ul class="section" id="thumblist_', $kind, '">';
			foreach ($current_objes as $media_id=>$value) {
				while ($value>0) {
					$objSubrec = array_pop($obje_links[$media_id]);
					$row = array();
					$newrec = find_gedcom_record($media_id, $ged_id, true);
					$row['m_media'] = $media_id;
					$row['m_gedfile']=$ged_id;
					$row['m_file'] = get_gedcom_value("FILE", 1, $newrec);
					$row['m_titl'] = get_gedcom_value("TITL", 1, $newrec);
					if (empty($row['m_titl'])) $row['m_titl'] = get_gedcom_value("FILE:TITL", 1, $newrec);
					$row['m_gedrec'] = $newrec;
					$et = preg_match("/(\.\w+)$/", $row['m_file'], $ematch);
					$ext = "";
					if ($et>0) $ext = substr(trim($ematch[1]), 1);
					$row['m_ext'] = $ext;
					$row['mm_gid'] = $pid;
					$row['mm_gedrec'] = get_sub_record($objSubrec{0}, $objSubrec, $gedrec);
					$res = lightbox_print_media_row('new', $row, $pid);
					$media_found = $media_found || $res;
					$value--;
				}
			}
		}
		// No "Extra" Media Items ============================
		if ($kind==5 && $ct==$numindiobjs) {
		// "Extra" Media Item in GEDCOM but NOT in DB ========
		} else if ($kind==5 && $ct!=$numindiobjs) {
			echo '</ul>';
			echo '</div>';
			echo '<div class="clearlist">';
			echo '</div>';
			echo '</td></tr></table>';
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		// Media Item in GEDCOM & in DB ======================
		} else {
			echo '</ul>';
			echo '</div>';
			echo '<div class="clearlist">';
			echo '</div>';
			echo '</td></tr></table>';
			if ($kind==3 && $numm > 0) {
				echo '<font size="1">';
				echo WT_I18N::translate('"UK census images have been obtained from "The National Archives", the custodian of the original records, and appear here with their approval on the condition that no commercial use is made of them without permission.
Requests for commercial publication of these or other UK census images appearing on this website should be directed to: Image Library, The National Archives, Kew, Surrey, TW9 4DU, United Kingdom."
');
				echo '</font>';
			}
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		}
	}
	if ($media_found) return $is_media='YES';
	else return $is_media='NO';
}

/**
 * print a media row in a table
 * @param string $rtype whether this is a 'new', 'old', or 'normal' media row... this is used to determine if the rows should be printed with an outline color
 * @param array $rowm        An array with the details about this media item
 * @param string $pid        The record id this media item was attached to
 */
function lightbox_print_media_row($rtype, $rowm, $pid) {

	global $TEXT_DIRECTION, $sort_i, $notes;

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
			// Highlight Album Thumbnails - Changed=new (blue), Changed=old (red), Changed=no (none)
			 if ($rtype=='new') {
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
		$notes    = htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)), ENT_QUOTES);

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($mainMedia, $thumbnail, $rowm['m_media'], $mediaTitle, $notes);

		// Prepare Below Thumbnail  menu ----------------------------------------------------
		$menu = new WT_Menu();
		// Truncate media title to 13 chars (45 chars if Streetview) and add ellipsis
		$mtitle = strip_tags($mediaTitle);
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

		if ($rtype=='old') {
			// Do not print menu if item has changed and this is the old item
		} else {
			// Continue printing menu
			$menu->addClass('', 'submenu');

			// View Notes
			if (strpos($rowm['m_gedrec'], "\n1 NOTE")) {
				$submenu = new WT_Menu('&nbsp;&nbsp;' . WT_I18N::translate('View Notes') . '&nbsp;&nbsp;', '#');
				// Notes Tooltip ----------------------------------------------------
				$submenu->addOnclick("modalNotes('". $notes ."','". WT_I18N::translate('View Notes') ."'); return false;");
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);
			}
			//View Details
			$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('View Details') . "&nbsp;&nbsp;", WT_SERVER_NAME.WT_SCRIPT_PATH . "mediaviewer.php?mid=".$rowm['m_media'].'&amp;ged='.WT_GEDURL, 'right');
			$submenu->addClass("submenuitem");
			$menu->addSubMenu($submenu);
			//View Source
			if ($sour && $sour->canDisplayDetails()) {
				$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('View Source') . "&nbsp;&nbsp;", $sour->getHtmlUrl());
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);
			}
			if (WT_USER_CAN_EDIT) {
				// Edit Media
				$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Edit media') . "&nbsp;&nbsp;");
				$submenu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid={$rowm['m_media']}&amp;linktoid={$rowm['mm_gid']}', '_blank', edit_window_specs);");
				$submenu->addClass("submenuitem");
				$menu->addSubMenu($submenu);
				if (WT_USER_IS_ADMIN) {
					// Manage Links
					if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
						$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Manage links') . "&nbsp;&nbsp;");
						$submenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=manage', '_blank', find_window_specs);");
						$submenu->addClass("submenuitem");
						$menu->addSubMenu($submenu);
					} else {
						$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Set link') . "&nbsp;&nbsp;", '#', null, 'right', 'right');
						$submenu->addClass('submenuitem', 'submenu');

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Person'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=person', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Family'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=family', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$ssubmenu = new WT_Menu(WT_I18N::translate('To Source'));
						$ssubmenu->addOnclick("return window.open('inverselink.php?mediaid={$rowm['m_media']}&amp;linkto=source', '_blank', find_window_specs);");
						$ssubmenu->addClass('submenuitem', 'submenu');
						$submenu->addSubMenu($ssubmenu);

						$menu->addSubMenu($submenu);
					}
					// Unlink Media
					$submenu = new WT_Menu("&nbsp;&nbsp;" . WT_I18N::translate('Unlink Media') . "&nbsp;&nbsp;");
					$submenu->addOnclick("return delete_fact('$pid', 'OBJE', '".$rowm['m_media']."', '".WT_I18N::translate('Are you sure you want to delete this fact?')."');");
					$submenu->addClass("submenuitem");
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
					echo '<table width="10px" style="margin-top:-90px;" class="pic" border="0"><tr>';
				} else {
					echo '<table width="10px" class="pic" border="0"><tr>';
				}
				echo '<td align="center" rowspan="2">';
				echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'lightbox/images/transp80px.gif" height="100px" alt=""></img>';
				echo '</td>';
				echo '<td colspan="3" valign="middle" align="center">';
				echo '<a href="', $mediaInfo['url'], '">';
			}

			// Now finally print the thumbnail -----------------------------------------------------
			echo '<img src="', $mediaInfo['thumb'], '"';

			// print browser tooltips associated with image ----------------------------------------
			echo ' alt="" title="' . strip_tags($mediaTitle) . '">';

			// Close anchor --------------------------------------------------------------
			if ($mainFileExists) {
				echo '</a>';
			}
			echo '</td></tr>';

			//View Edit Menu ----------------------------------
			echo '<tr>';
			echo '<td width="5px"></td>';
			echo '<td valign="bottom" align="center" class="nowrap">';
			echo $menu->getMenu();
			echo '</td>';
			echo '<td width="5px"></td>';
			echo '</tr>';
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
