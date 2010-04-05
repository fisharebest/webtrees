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
$filter = safe_GET('filter');
$filter = stripLRMRLM($filter);
$search = safe_GET('search');
$sortby = safe_GET('sortby', 'file', 'title');
$max = safe_GET('max', array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200'), '20');
$folder = safe_GET('folder');
if (empty($folder)) $folder = $MEDIA_DIRECTORY;

if (empty($_SESSION['medialist_ged'])) $_SESSION['medialist_ged'] = WT_GEDCOM;
if ($_SESSION['medialist_ged'] != WT_GEDCOM) {
	$_SESSION['medialist_ged'] = WT_GEDCOM;
	unset($_SESSION['medialist']);
}

if (!isset($_SESSION['medialist'])) $search = "yes";

$currentdironly = (isset($_REQUEST['subdirs']) && $_REQUEST['subdirs']=="on") ? false : true;
print_header(i18n::translate('MultiMedia Objects'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

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


//-- automatically generate an image
if (WT_USER_IS_ADMIN && $action=="generate" && !empty($file) && !empty($thumb)) {
	generate_thumbnail($file, $thumb);
}
if ($search == "yes") {
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
}

// A form for filtering the media items
?>
<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter" />
	<input type="hidden" name="search" value="yes" />
	<table class="list-table center width75 <?php echo $TEXT_DIRECTION; ?>">
	<?php
	if ($TEXT_DIRECTION=='ltr') $legendAlign = 'align="right"';
	else $legendAlign = 'align="left"';
	?>

	<!-- // NOTE: Row 1, left: Sort sequence -->
	<tr><td class="descriptionbox wrap width25" <?php echo $legendAlign;?>><?php echo i18n::translate('Sequence'), help_link('sortby'); ?></td>
	<td class="optionbox wrap"><select name="sortby">
		<option value="title" <?php if ($sortby=='title') echo "selected=\"selected\"";?>><?php echo i18n::translate('TITL'); ?></option>
		<option value="file" <?php if ($sortby=='file') echo "selected=\"selected\"";?>><?php echo i18n::translate('FILE'); ?></option>
	</select></td>

	<!-- // NOTE: Row 1, right: Objects per page -->
	<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>><?php echo i18n::translate('Media objects per page'); ?></td>
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
	</td></tr>

	<!-- // NOTE: Row 2 left: Filter options -->
	<tr><td class="descriptionbox wrap width25" <?php echo $legendAlign;?>><?php echo i18n::translate('Filter'), help_link('simple_filter'); ?></td>
	<td class="optionbox wrap width25">
		<?php
		// Directory pick list
		if ($MEDIA_DIRECTORY_LEVELS > 0) {
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
		} else echo "<input name=\"folder\" type=\"hidden\" value=\"ALL\" />";
		// Text field for filter and "submit" button
		?>
		<input id="filter" name="filter" value="<?php echo PrintReady($filter); ?>"/><br />
		<input type="submit" value="<?php echo i18n::translate('Apply filter');?>" />
	</td>

	<!-- // NOTE: Row 2 right: Recursive directory list -->
	<?php if ($MEDIA_DIRECTORY_LEVELS > 0) { ?>
	<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>><?php echo i18n::translate('List files in subdirectories'), help_link('medialist_recursive'); ?></td>
	<td class="optionbox wrap width25">
		<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?> />
	</td></tr>
	<?php } else { ?>
	<td class="descriptionbox wrap width25" <?php echo $legendAlign;?>>&nbsp;</td>
	<td class="optionbox wrap">
		&nbsp;
	</td></tr>
	<?php } ?>

	</table>
</form>
<?php

if ($action=="filter") {
	if (strlen($filter) > 1) {
		foreach($medialist as $key => $media) {
			if (!filterMedia($media, $filter, "http")) unset($medialist[$key]);
		}
	}
	usort($medialist, "mediasort"); // Reset numbering of medialist array
}
if ($search=="yes") {
	$_SESSION["medialist"] = $medialist;
} else {
	$medialist = $_SESSION["medialist"];
}

// Sort the media list according to the user's wishes
$sortedMediaList = $medialist;	// Default sort (by title) has already been done
if ($sortby=='file') usort($sortedMediaList, 'filesort');

// Count the number of items in the medialist
$ct=count($sortedMediaList);
$start = 0;
$max = 20;
if (isset($_GET["start"])) $start = $_GET["start"];
if (isset($_GET["max"])) $max = $_GET["max"];
$count = $max;
if ($start+$count > $ct) $count = $ct-$start;

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
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
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
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
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
		$name = trim($media["TITL"]);
//		$name1 = addslashes($media["TITL"]);
		$showFile = WT_USER_CAN_EDIT;
		if ($name=="") {
			//$showFile = false;
			if ($isExternal) $name = "URL";
			else $name = basename($media["FILE"]);
		}
		echo "\n\t\t\t<td class=\"list_value_wrap\" width=\"50%\">";
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

		echo "<a href=\"mediaviewer.php?mid=", $media["XREF"], "\">";

		if (begRTLText($name) && $TEXT_DIRECTION=="ltr") {
			if ($SHOW_ID_NUMBERS) {
				echo "(", $media["XREF"], ")&nbsp;&nbsp;&nbsp;";
			}
			echo "<b>", PrintReady($name), "</b>";
		} else {
			echo "<b>", PrintReady($name), "</b>";
			if ($SHOW_ID_NUMBERS) {
				echo "&nbsp;&nbsp;&nbsp;";
				if ($TEXT_DIRECTION=="rtl") echo getRLM();
				echo "(", $media["XREF"], ")";
				if ($TEXT_DIRECTION=="rtl") echo getRLM();
			}
		}
		if ($showFile) {
			if ($isExternal) echo "<br /><sub>URL</sub>";
			else echo "<br /><sub><span dir=\"ltr\">", PrintReady($media["FILE"]), "</span></sub>";
		}
		echo "</a><br />";

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
			if ($i%2 == 1 && $i < ($count-1)) echo "\n\t\t</tr>\n\t\t<tr>";
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
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
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
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconRarrow, "</a>\n";
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$lastStart}&max={$max}"), "\">", $IconRDarrow, "</a>\n";
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start={$newstart}&max={$max}"), "\">", $IconLarrow, "</a>\n";
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo "<a href=\"", encode_url("medialist.php?folder={$folder}&filter={$filter}&sortby={$sortby}&search=no&start=0&max={$max}"), "\">", $IconLDarrow, "</a>\n";
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
print_footer();

?>
