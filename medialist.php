<?php
/**
 * Displays a list of the multimedia objects
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Lists
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'medialist.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';

// $LB_SS_SPEED = "5";
$level = safe_GET("level", "", "0");
$action = safe_GET('action');
$search = safe_GET('search');
$sortby = safe_GET('sortby', 'file', 'title');
$max = safe_GET('max', array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200'), '20');
$folder = safe_GET('folder');
$show = safe_GET('show');
$build = safe_GET('build');
$reset = safe_GET('reset');
$filtered_medialist = safe_GET('filtered_medialist');
$apply_filter = safe_GET('apply_filter');
$filter1 = safe_GET('filter1');
$filter1 = stripLRMRLM($filter1);
$filter2 = safe_GET('filter2');
$filter2 = stripLRMRLM($filter2);
$or = i18n::translate('or');
$and = i18n::translate('and');
$filter_type = safe_GET('filter_type', array($or, $and), $or);
$columns = safe_GET('columns', array('1', '2'), '2');
$currentdironly = (isset($_REQUEST['subdirs']) && $_REQUEST['subdirs']=="on") ? false : true;
$show_thumbnail = (isset($_REQUEST['thumbnails']) && $_REQUEST['thumbnails']=="on") ? true : false;
 
if ($reset == "Reset") {
	$sortby = "title";
	$max = "20";
	$folder = "";
	$filter_type = $or;
	$columns = "2";
	$currentdironly = true;
	$show_thumbnail = true;
	$filter1 = "";
	$filter2 = "";
	$action = "";
	unset($_SESSION['medialist']);
	unset($_SESSION['filtered_medialist']);
}
  
if (empty($_SESSION['medialist_ged'])) $_SESSION['medialist_ged'] = WT_GEDCOM;
if ($_SESSION['medialist_ged'] != WT_GEDCOM) {
	$_SESSION['medialist_ged'] = WT_GEDCOM;
	unset($_SESSION['medialist']);
}

// If the $folder is empty this is a new visit, a return, or a reset
if (empty($folder)) {
	$folder = $MEDIA_DIRECTORY; // default setting
	$show_thumbnail = true; // default setting
}

// If SESSION_medialist then it's a return
if (isset($_SESSION['medialist'])) {
	$show = "yes";
	$search = "yes";
		
	// Build a new array?
	// Not if $action <> filter (ie It's either a layout/page change or a return visit)
	// Load up the session variables	
	if ($action != "filter") {
		$medialist = ($_SESSION['filtered_medialist']);
		$folder=($_SESSION['medialist_folder']);		
		$filter1=($_SESSION['medialist_filter1']);
		$filter2=($_SESSION['medialist_filter2']);
		$filter_type=($_SESSION['filter_type']);
		$sortby=($_SESSION['medialist_sortby']);
		$max=($_SESSION['medialist_max']);
		$columns=($_SESSION['medialist_columns']);
		$currentdironly=($_SESSION['medialist_currentdironly']);
		$show_thumbnail=($_SESSION['medialist_thumbnail']);
	
	} else {		
		// This is a return visit and the FILTER button was used
			// Check if the subdirectory and folder have changed
			if ($MEDIA_DIRECTORY_LEVELS > 0) {
				if ($folder != $_SESSION['medialist_folder']) $build = "yes";
				if ($currentdironly != $_SESSION['medialist_currentdironly']) $build ="yes";
			}			
			// if same subdirectory and folder then use an existing medialist
			if ($build != "yes") {
				if (($filter1 == $_SESSION['medialist_filter1']) && ($filter2 == $_SESSION['medialist_filter2'])) {
					$medialist = $_SESSION['filtered_medialist'];
				} else $medialist = $_SESSION['medialist'];
			}			
		}
} else {
	// This is the first visit to the medialist page
	if ($action == "filter") {
		$build = "yes";
		$show = "yes";
	}
}

// Disable autocomplete
// if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

print_header(i18n::translate('MultiMedia Objects'));
echo "\n\t<div class=\"center\"><h2>", i18n::translate('MultiMedia Objects'), "</h2></div>\n\t";

// Get Javascript variables from lb_config.php ---------------------------
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';

	if ($theme_name=="Minimal") {
		// Force icon options to "text" when we're dealing with the Minimal theme
		if ($LB_AL_HEAD_LINKS!="none") $LB_AL_HEAD_LINKS = "text";
		if ($LB_AL_THUMB_LINKS!="none") $LB_AL_THUMB_LINKS = "text";
		if ($LB_ML_THUMB_LINKS!="none") $LB_ML_THUMB_LINKS = "text";
	}
}

//-- automatically generate an image (NOT CURRENTLY USED)
if (WT_USER_IS_ADMIN && $action=="generate" && !empty($file) && !empty($thumb)) {
	generate_thumbnail($file, $thumb);
}

// ************************  BEGIN = 'Build the medialist array' ************************

if ($build == "yes") {
	if ($folder == "ALL") {
		$folder = $MEDIA_DIRECTORY;
		$currentdironly = false;
	}
	// show external links only if looking at top level directory
	$showExternal = ($folder == $MEDIA_DIRECTORY) ? true : false;
	$medialist=get_medialist($currentdironly, $folder, true, false, $showExternal);

	//-- remove all private media objects
	foreach($medialist as $key => $media) {
			echo " ";

			// Display when user has Edit rights or when object belongs to current GEDCOM
			$disp = WT_USER_CAN_EDIT || $media["GEDFILE"]==WT_GED_ID;
			// Display when Media objects aren't restricted by global privacy
			$disp &= displayDetailsById($media["XREF"], "OBJE");
			// Display when this Media object isn't restricted
			$disp &= !FactViewRestricted($media["XREF"], $media["GEDCOM"]);
			/** -- already included in the displayDetailsById() function
		if ($disp) {
				$links = $media["LINKS"];
				//-- make sure that only media with links are shown
			if (count($links) != 0) {
						foreach($links as $id=>$type) {
							$disp &= displayDetailsById($id, $type);
						}
				}
		}
		*/
		if (!$disp) unset($medialist[$key]);

	}
	
	usort($medialist, "mediasort"); // Reset numbering of medialist array
// save the array
$_SESSION['medialist'] = $medialist;
}

// ************************  END = 'Build the medialist array' ************************

// ************************  BEGIN = 'Build the input form' ************************

// A form for filtering the media items

?>

<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter" />
	<input type="hidden" name="search" value="yes" />	
	<table class="list-table center width75 <?php echo $TEXT_DIRECTION; ?>">
	<?php
	if ($TEXT_DIRECTION=='ltr') {
		$legendAlign = 'align="right"';
		$left = "float: left;";
		$right = "float: right;";
	} else {
		$legendAlign = 'align="left"';
		$left = "float: right;";
		$right = "float: left;";
	}
	?>
	<!-- Build the form cells -->
	<tr>
<!-- // NOTE: Row 1, left: -->
	<!-- // begin select media folders -->
		<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
			<?php echo i18n::translate('Media directory'), help_link('media_dir'); ?></td>
		<td class="optionbox wrap width25">
			<?php
				//if ($MEDIA_DIRECTORY_LEVELS > 0) {
				if (empty($folder)) {
					if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
					else $folder = "ALL";
				}
					$folders = array_merge(array("ALL"), get_media_folders());
					echo "<span dir=\"ltr\"><select name=\"folder\">\n";
				foreach($folders as $f) {
					echo "<option value=\"", $f, "\"";
					if ($folder==$f) echo " selected=\"selected\"";
					echo ">";
					if ($f=="ALL") echo i18n::translate('ALL');
					else echo $f;
					echo "</option>\n";
				}
				echo "</select></span><br />";
		//} else echo $MEDIA_DIRECTORY, "<input name=\"folder\" type=\"hidden\" value=\"ALL\" />";
					?>
			</td>
	<!-- // end select media folders -->
<!-- // NOTE: Row 1 right: -->
	<!-- begin sort files -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
					<?php echo i18n::translate('Sort by title or file name'), help_link('sortby'); ?>
			</td>
			<td class="optionbox wrap width25"><select name="sortby">
				<option value="title" <?php if ($sortby=='title') echo "selected=\"selected\"";?>>
					<?php echo i18n::translate('Sort by title'); ?></option>
				<option value="file" <?php if ($sortby=='file') echo "selected=\"selected\"";?>>
					<?php echo i18n::translate('Sort by file name'); ?></option>
				</select>
			</td>
	<!-- //end sort files -->
	</tr><tr>
<!-- // NOTE: Row 2 left:-->
	<!-- // begin sub directories -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
				<?php echo i18n::translate('Include subdirectories'), help_link('medialist_recursive'); ?>
			<td class="optionbox wrap width25">
				<?php //if ($MEDIA_DIRECTORY_LEVELS > 0) { ?>
				<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?> />
			</td>
				<?php
				//} else echo i18n::translate('none');{ ?>
			</td>
				<?php // } ?>
	<!-- // end subdirectories -->
<!-- // NOTE: Row 2 right:-->
	<!-- // begin media objects per page -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
				<?php echo i18n::translate('Media objects per page'), help_link('media_objects_pp');; ?>
			</td>
			<td class="optionbox wrap width25">
				<select name="max">
					<?php
					foreach (array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200') as $selectEntry) {
						echo "<option value=\"$selectEntry\"";
						if ($selectEntry==$max) echo " selected=\"selected\"";
						echo ">", $selectEntry, "</option>";
					}
					?>
				</select>
			</td>
	<!-- // end media objects per page -->
	</tr><tr>
<!-- // NOTE: Row 3 left:-->
	<!-- // begin search filter -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
				<?php echo i18n::translate('Search filters'), help_link('medialist_filters'); ?>
			</td>
			<td class="optionbox wrap width25">
		<!-- // begin Text field for filter and "submit" button -->
				<input id="filter1" name="filter1" value="<?php echo PrintReady($filter1); ?>" size="14" />
				<select name="filter_type">
					<?php
					foreach (array($or, $and) as $selectEntry) {
						echo "<option value=\"$selectEntry\"";
						if ($selectEntry==$filter_type) echo " selected=\"selected\"";
						echo ">", $selectEntry, "</option>";
					}
					?>
				</select><br />
				<input id="filter2" name="filter2" value="<?php echo PrintReady($filter2); ?>" size="14" />
			</td>
	<!-- // end search filter -->
<!-- // NOTE: Row 3 right:-->
	<!-- // begin columns per page -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
				<?php echo i18n::translate('Columns per page'), help_link('media_columns_pp'); ?>
			</td>
			<td class="optionbox wrap width25">
				<select name="columns">
					<?php
					foreach (array('1', '2') as $selectEntry) {
						echo "<option value=\"$selectEntry\"";
						if ($selectEntry==$columns) echo " selected=\"selected\"";
						echo ">", $selectEntry, "</option>";
					}
					?>
				</select>
			</td>

	<!-- // end columns per page -->
	</tr><tr>
<!-- // NOTE: Row 4 left:-->
	<!-- // begin search buttons  -->
			<td class="descriptionbox wrap width25">
			</td>
			<td class="optionbox wrap width25">
				<input type="submit" name="apply_filter" value="<?php echo i18n::translate('Search');?>" />
				<input type="submit" name="reset" value="<?php echo i18n::translate('Reset');?>" />
			</td>
	<!-- // end search buttons  -->
<!-- // NOTE: Row 4 right:-->
	<!-- // thumbnail option  -->
			<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>
				<?php echo i18n::translate('Show thumbnails'), help_link('media_thumbs'); ?>
			</td>
			<td class="optionbox wrap width25">	
				<input type="checkbox" id="thumbnails" name="thumbnails"<?php if ($show_thumbnail) { ?>checked="checked"<?php } ?> />
			</td>	
	<!-- // end thumbnail option -->
	</tr></table>
</form>
<!-- // end form for filtering the media items -->
<?php
// ************************  END = 'Build the input form' ************************

// ************************  BEGIN = 'Filter the medialist array' ************************

// preserve the original medialist
if (!empty($medialist)) $filtered_medialist = $medialist;

if ($action=="filter" && (!empty($filtered_medialist))) {
	$temp_filter = $filter_type;
	if ($filter_type == $or) {
		if ((strlen($filter1) > 1) && (strlen($filter2)) > 1) {
			foreach($filtered_medialist as $key => $media) {
				if (!filterMedia($media, $filter1, "http") && !filterMedia($media, $filter2, "http"))
				unset($filtered_medialist[$key]);
			}
			usort($filtered_medialist, "mediasort"); // Reset numbering of medialist array
		// If either of the filters is empty use the "and" filter
		} else $filter_type = $and;
	} 

	if ($filter_type == $and) {
		if ((strlen($filter1) > 1) || (strlen($filter2)) > 1) {
			foreach($filtered_medialist as $key => $media) {
				if (!filterMedia($media, $filter1, "http")) unset($filtered_medialist[$key]);
				if (!filterMedia($media, $filter2, "http")) unset($filtered_medialist[$key]);
			}
			usort($filtered_medialist, "mediasort"); // Reset numbering of medialist array
		}
	}
// Restore filter type
$filter_type = $temp_filter;
}

// ************************  END = 'Filter the medialist array' ************************

// *****************************  BEGIN Set SESSION variables ********************************************

if ($search=="yes") {
	if ($filtered_medialist) $_SESSION["filtered_medialist"] = $filtered_medialist;
	$_SESSION['filter_type']=$filter_type;
	$_SESSION['medialist_filter1']=$filter1;
	$_SESSION['medialist_filter2']=$filter2;
	$_SESSION['medialist_folder']=$folder;
	$_SESSION['medialist_sortby']=$sortby;
	$_SESSION['medialist_max']=$max;
	$_SESSION['medialist_columns']=$columns;
	$_SESSION['medialist_currentdironly']=$currentdironly;
	$_SESSION['medialist_thumbnail']=$show_thumbnail;	
}

// *****************************  End Set SESSION variables ********************************************

// ************************  BEGIN = 'Print the medialist array' ************************

if ($show == "yes") {
	if (!empty($filtered_medialist)) {
		$sortedMediaList = $filtered_medialist;	// Default sort (by title) has already been done
		if ($sortby=='file') usort($sortedMediaList, 'filesort');

		// Count the number of items in the medialist
		$ct=count($sortedMediaList);
		$start = 0;
		//$max = 20;
		if (isset($_GET["start"])) $start = $_GET["start"];
		$count = $max;
		if ($start+$count > $ct) $count = $ct-$start;
	} else $ct = "0";

	echo "\n\t<div align=\"center\">", i18n::translate('Media Objects found'), " ", $ct, " <br /><br />";
	if ($ct>0) {

	$currentPage = ((int) ($start / $max)) + 1;
	$lastPage = (int) (($ct + $max - 1) / $max);
	$IconRarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["rarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["larrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconRDarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["rdarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";
	$IconLDarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["ldarrow"]["other"]."\" width=\"20\" height=\"20\" border=\"0\" alt=\"\" />";

	print"\n\t<table class=\"list_table\">\n";

	// echo page back, page number, page forward controls
	
	echo "\n<tr><td colspan=\"2\">\n";
	echo "\n\t<table class=\"list_table width100\">\n";

	echo "\n<tr>\n";

	echo "<td class=\"width30\" align=\"", $TEXT_DIRECTION == "ltr"?"left":"right", "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
		}
	}

	echo "</td>";
	
	echo "<td align=\"center\">", i18n::translate('Page %s of %s', $currentPage, $lastPage), "</td>";
	
	echo "<td class=\"width30\" align=\"", $TEXT_DIRECTION == "ltr"?"right":"left", "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
		}
	}
	echo "</td>";
	
	echo "</tr>\n</table></td></tr>";
	
	// -- echo the array
	echo "\n<tr>\n";

	for ($i=0; $i<$count; $i++) {

		$media = $sortedMediaList[$start+$i];
		$isExternal = isFileExternal($media["FILE"]);
		$imgsize = findImageSize($media["FILE"]);
		$imgwidth = $imgsize[0]+40;
		$imgheight = $imgsize[1]+150;
		$name = basename($media["TITL"]);
//		$name1 = addslashes($media["TITL"]);
		$showFile = WT_USER_CAN_EDIT;
		if ($name=="") {
			//$showFile = false;
			if ($isExternal) $name = "URL";
			else $name = basename($media["FILE"]);
		}

		if ($columns == "1") echo "\n\t\t\t<td class=\"list_value_wrap\" width=\"80%\">";
		if ($columns == "2") echo "\n\t\t\t<td class=\"list_value_wrap\" width=\"50%\">";

		echo "<table class=\"$TEXT_DIRECTION\">\n\t<tr>\n\t\t<td valign=\"top\" style=\"white-space: normal;\">";

		//Get media item Notes
		$haystack = $media["GEDCOM"];
		$needle   = "1 NOTE";
		$before   = substr($haystack, 0, strpos($haystack, $needle));
		$after    = substr(strstr($haystack, $needle), strlen($needle));
		$worked   = str_replace("1 NOTE", "1 NOTE<br />", $after);
		$final    = $before.$needle.$worked;
		$notes    = PrintReady(htmlspecialchars(addslashes(print_fact_notes($final, 1, true, true)), ENT_COMPAT, 'UTF-8'));

		// Get info on how to handle this media file
		$mediaInfo = mediaFileInfo($media["FILE"], $media["THUMB"], $media["XREF"], $name, $notes);

		//-- Thumbnail field
		if ($show_thumbnail) {
		echo '<a href="', $mediaInfo['url'], '">';
		echo '<img src="', $mediaInfo['thumb'], '" align="center" class="thumbnail" border="none"', $mediaInfo['width'];
		echo ' alt="', PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')), '" title="', PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')), '" /></a>';

		echo "</td>\n\t\t", '<td class="list_value_wrap" style="border: none;" width="100%">';

		if (WT_USE_LIGHTBOX) {
			if (WT_USER_CAN_EDIT) {

				if ($LB_ML_THUMB_LINKS != "none") {
					echo "<table border=0><tr>";

					// ---------- Edit Media --------------------
					echo "<td class=\"width33 wrap center font9\" valign=\"top\">";
					echo "<a href=\"javascript:;\" title=\"" . i18n::translate('Edit this Media Item\'s Details') . "\" onclick=\" return window.open('addmedia.php?action=editmedia&pid={$media['XREF']}&linktoid=', '_blank', 'top=50, left=50, width=600, height=600, resizable=1, scrollbars=1');\">";
					if ($LB_ML_THUMB_LINKS == "icon" || $LB_ML_THUMB_LINKS == "both") {
						echo "<img src=\"modules/lightbox/images/image_edit.gif\" alt=\"\" class=\"icon\" title=\"" . i18n::translate('Edit this Media Item\'s Details') . "\" />&nbsp;&nbsp;&nbsp;" ;
					}
					if ($LB_ML_THUMB_LINKS == "both") {
						echo "<br />";
					}
					if ($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") {
						echo i18n::translate('Edit Details') ;
					}
					echo "</a>" . "\n";
					echo "</td>";

					// ---------- Link Media to person, family or source  ---------------
					echo "<td class=\"width33 wrap center font9\" valign=\"top\">";
					require  WT_ROOT.'modules/lightbox/functions/lb_link.php';
					echo "</td>";

					// ---------- View Media Details (mediaviewer) --------------------
					echo "<td class=\"width33 wrap center font9\" valign=\"top\">";
					echo "<a href=\"mediaviewer.php?mid=" . $media["XREF"] . "\" title=\"" . i18n::translate('View this Media Item\'s Details 
Plus other Media Options - MediaViewer page') . "\">";
					if ($LB_ML_THUMB_LINKS == "icon" || $LB_ML_THUMB_LINKS == "both") {
						echo "&nbsp;&nbsp;&nbsp;<img src=\"modules/lightbox/images/image_view.gif\" alt=\"\" class=\"icon\" title=\"" . i18n::translate('View this Media Item\'s Details 
Plus other Media Options - MediaViewer page') . "\" />";
					}
					if ($LB_ML_THUMB_LINKS == "both") {
						echo "<br />";
					}
					if ($LB_ML_THUMB_LINKS == "both" || $LB_ML_THUMB_LINKS == "text") {
						echo i18n::translate('View Details') ;
					}
					echo "</a>" . "\n" ;
					echo "</td>";

					echo "</tr></table>";
				}
				// ------------ Linespace ---------------------
				echo "<br />";
			}
		}
		
	}
		// -- new naming structure ---------
		if ($sortby == 'title'){
			$name_disp1 = $name;
			$name_disp2 = basename($media['FILE']);
			if ($isExternal) $name_disp2 = "URL";
			$name_disp3 = $media['FILE'];
			$name_disp4 = i18n::translate('Filename');
			}else {
			$name_disp1 = basename($media['FILE']);
			if ($isExternal) $name_disp1 = "URL";
			$name_disp2 = $name;
			$name_disp3 = $media['FILE'];
			$name_disp4 = i18n::translate('Title');
			}

			echo "<a href=\"mediaviewer.php?mid=".$media["XREF"]."\">";

			if (begRTLText($name_disp1) && $TEXT_DIRECTION=="ltr") {
			echo "(".$media["XREF"].")&nbsp;&nbsp;&nbsp;";
			echo "<b>".PrintReady($name_disp1)."</b>";
			} else {
			echo "<b>".PrintReady($name_disp1)."</b>&nbsp;&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") echo getRLM();
				echo "(", $media["XREF"], ")";
				if ($TEXT_DIRECTION=="rtl") echo getRLM();
			}
			
			echo "</a>";
			
		if ($showFile) {
			//if ($isExternal) echo "<br /><sub>URL</sub>";
		//	else {
				echo "<br /><br /><sub><span dir=\"ltr\"><b>", PrintReady($name_disp4), ": </b>", PrintReady($name_disp2), "</span></sub>";
				echo "<br /><sub><span dir=\"ltr\"><b>", i18n::translate('Location'), ": </b>", PrintReady($name_disp3), "</span></sub>";
//}
		}
		echo "<br />";

		if (!$isExternal && !$media["EXISTS"]) {
			echo "<br /><span class=\"error\">", i18n::translate('File not found.'), " <span dir=\"ltr\">", PrintReady($media["FILE"]), "</span></span>";
		}
		if (!$isExternal && $media["EXISTS"]) {
			$imageTypes = array("", "GIF", "JPG", "PNG", "SWF", "PSD", "BMP", "TIFF", "TIFF", "JPC", "JP2", "JPX", "JB2", "SWC", "IFF", "WBMP", "XBM");
			if (!empty($imgsize[2])) {
				echo "\n\t\t\t<span class=\"label\"><br />", i18n::translate('Media Format'), ": </span> <span class=\"field\" style=\"direction: ltr;\">", $imageTypes[$imgsize[2]], "</span>";
			} else if (empty($imgsize[2])) {
				$path_end=substr($media["FILE"], strlen($media["FILE"])-5);
				$imageType = strtoupper(substr($path_end, strpos($path_end, ".")+1));
				echo "\n\t\t\t<span class=\"label\"><br />", i18n::translate('Media Format'), ": </span> <span class=\"field\" style=\"direction: ltr;\">", $imageType, "</span>";
			}

			$fileSize = media_filesize($media["FILE"]);
			$sizeString = getfilesize($fileSize);
			echo "&nbsp;&nbsp;&nbsp;<span class=\"field\" style=\"direction: ltr;\">", $sizeString, "</span>";

			if ($imgsize[2]!==false) {
				echo "\n\t\t\t<span class=\"label\"><br />", i18n::translate('Image Dimensions'), ": </span> <span class=\"field\" style=\"direction: ltr;\">", $imgsize[0], $TEXT_DIRECTION =="rtl"?(" " . getRLM() . "x" . getRLM() . " ") : " x ", $imgsize[1], "</span>";
			}
		}
			echo "<div style=\"white-space: normal; width: 95%;\">";
			print_fact_sources($media["GEDCOM"], $media["LEVEL"]+1);
			print_fact_notes($media["GEDCOM"], $media["LEVEL"]+1);
			echo "</div>";

		PrintMediaLinks($media["LINKS"], "small");

			echo "</td></tr></table>\n";
			echo "</td>";
			if ($columns == "1") echo "\n\t\t</tr>\n\t\t<tr>";
			if (($columns == "2") && ($i%2 == 1 && $i < ($count-1)))
			echo "\n\t\t</tr>\n\t\t<tr>";
	}
	echo "\n\t\t</tr>";

	// echo page back, page number, page forward controls
	echo "\n<tr><td colspan=\"2\">\n";
	
	print"\n\t<table class=\"list_table width100\">\n";	
	echo "\n<tr>\n";
	echo "<td class=\"width30\" align=\"", $TEXT_DIRECTION == "ltr"?"left":"right", "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
		}
	}
	echo "</td>";
	echo "<td align=\"center\">", i18n::translate('Page %s of %s', $currentPage, $lastPage), "</td>";
	echo "<td class=\"width30\" align=\"", $TEXT_DIRECTION == "ltr"?"right":"left", "\">";
	if ($TEXT_DIRECTION=="ltr") {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter1={$filter1}&filter2={$filter2}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
		}
	}
	echo "</td>";
	echo "</tr>\n</table></td></tr>";
	echo "</table><br />";
}
echo "\n</div>\n";
// -- load up the slideshow code
if (!WT_USE_LIGHTBOX) {
	if (file_exists(WT_ROOT.'modules/slideshow/slideshow.php')) {
		require_once WT_ROOT.'modules/slideshow/slideshow.php';
		}
	}	
}
// ************************  END = 'Print the medialist array' ************************
print_footer();
?>
