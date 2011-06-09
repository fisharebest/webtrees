<?php
// Displays a list of the media objects
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'medialist.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';

// $LB_SS_SPEED = '5';
$level = safe_GET('level', '', '0');
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
$or = WT_I18N::translate('or');
$and = WT_I18N::translate('and');
$filter_type = safe_GET('filter_type', array($or, $and), $or);
$columns = safe_GET('columns', array('1', '2'), '2');
$currentdironly = (isset($_REQUEST['subdirs']) && $_REQUEST['subdirs']=='on') ? false : true;
$show_thumbnail = (isset($_REQUEST['thumbnails']) && $_REQUEST['thumbnails']=='on') ? true : false;
$subdirs = safe_GET('subdirs');
$thumbnail = safe_GET('thumbnail');

if ($reset == 'Reset') {
	$sortby = 'title';
	$max = '20';
	$folder = '';
	$filter_type = $or;
	$columns = '2';
	$currentdironly = true;
	$show_thumbnail = true;
	$filter1 = '';
	$filter2 = '';
	$action = '';
	unset($_SESSION['Medialist']);
	unset($_SESSION['Filtered_medialist']);
}

if (empty($_SESSION['Medialist_ged'])) $_SESSION['Medialist_ged'] = WT_GEDCOM;
if ($_SESSION['Medialist_ged'] != WT_GEDCOM) {
	$_SESSION['Medialist_ged'] = WT_GEDCOM;
	unset($_SESSION['Medialist']);
}

// If the $folder is empty this is a new visit, a return, or a reset
if (empty($folder)) {
	$folder = $MEDIA_DIRECTORY; // default setting
	$show_thumbnail = true; // default setting
}

// If SESSION_medialist then it's a return
if (isset($_SESSION['Medialist']) && isset($_SESSION['Filtered_medialist']) ) {
	$show = 'yes';
	$search = 'yes';
	// Build a new array?
	// Not if $action <> filter (ie It's either a layout/page change or a return visit)
	// Load up the session variables
	if ($action != 'filter') {
		$medialist = ($_SESSION['Filtered_medialist']);
		$folder=($_SESSION['Medialist_folder']);
		$filter1=($_SESSION['Medialist_filter1']);
		$filter2=($_SESSION['Medialist_filter2']);
		$filter_type=($_SESSION['Filter_type']);
		$sortby=($_SESSION['Medialist_sortby']);
		$max=($_SESSION['Medialist_max']);
		$columns=($_SESSION['Medialist_columns']);
		$currentdironly=($_SESSION['Medialist_currentdironly']);
		$show_thumbnail=($_SESSION['Medialist_thumbnail']);

	} else {
		// This is a return visit and the FILTER button was used
		// Check if the subdirectory and folder have changed
		if ($MEDIA_DIRECTORY_LEVELS > 0) {
			if ($folder != $_SESSION['Medialist_folder']) $build = 'yes';
			if ($currentdironly != $_SESSION['Medialist_currentdironly']) $build ='yes';
		}
		// if same subdirectory and folder then use an existing medialist
		if ($build != 'yes') {
			if (($filter1 == $_SESSION['Medialist_filter1']) && ($filter2 == $_SESSION['Medialist_filter2']) && ($filter_type == $_SESSION['Filter_type'])) {
				$medialist = $_SESSION['Filtered_medialist'];
				$action = false;
			} else $medialist = $_SESSION['Medialist'];
		}
	}
} else {
	// This is the first visit to the medialist page
	if ($action == 'filter') {
		$build = 'yes';
		$show = 'yes';
	}
}

// Disable autocomplete
// if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

print_header(WT_I18N::translate('Media objects'));
echo '<div class="center"><h2>', WT_I18N::translate('Media objects'), '</h2></div>';
// Get Javascript variables from lb_config.php ---------------------------
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/lb_defaultconfig.php';
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}
//-- automatically generate an image (NOT CURRENTLY USED)
if (WT_USER_IS_ADMIN && $action=='generate' && !empty($file) && !empty($thumb)) {
	generate_thumbnail($file, $thumb);
}
// ************************  BEGIN = 'Build the medialist array' ************************
if ($build == 'yes') {
	if ($folder == 'ALL') {
		$folder = $MEDIA_DIRECTORY;
		$currentdironly = false;
	}
	// show external links only if looking at top level directory
	$showExternal = ($folder == $MEDIA_DIRECTORY) ? true : false;
	$medialist=get_medialist2($currentdironly, $folder, true, false, $showExternal);

	//-- remove all private media objects
	foreach ($medialist as $key => $media) {
			echo ' ';
			$mediaobject=WT_Media::getInstance($media['XREF']);
			// Display when user has Edit rights or when object belongs to current GEDCOM
			$disp = WT_USER_CAN_EDIT || $mediaobject->ged_id==WT_GED_ID;
			// Display when Media objects aren't restricted by global privacy
			$disp &= $mediaobject->canDisplayDetails();
			// Display when this Media object isn't restricted
			$disp &= canDisplayFact($mediaobject->getXref(), $mediaobject->ged_id, $mediaobject->getGedcomRecord());
		if (!$disp) unset($medialist[$key]);
	}
	usort($medialist, 'mediasort'); // Reset numbering of medialist array
// save the array
$_SESSION['Medialist'] = $medialist;
}
// ************************  END = 'Build the medialist array' ************************
// ************************  BEGIN = 'Build the input form' ************************
// A form for filtering the media items
?>
<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter" />
	<input type="hidden" name="search" value="yes" />
	<table class="list-table center width75 <?php echo $TEXT_DIRECTION; ?>">
	<!-- Build the form cells -->
	<tr>
<!-- // NOTE: Row 1, left: -->
	<!-- // begin select media folders -->
		<td class="descriptionbox wrap width25">
			<?php echo WT_I18N::translate('Media directory'), help_link('view_server_folder'); ?></td>
		<td class="optionbox wrap width25">
			<?php
				//if ($MEDIA_DIRECTORY_LEVELS > 0) {
				if (empty($folder)) {
					if (!empty($_SESSION['upload_folder'])) $folder = $_SESSION['upload_folder'];
					else $folder = 'ALL';
				}
					$folders = array_merge(array('ALL'), get_media_folders());
					echo '<span dir="ltr"><select name="folder">';
				foreach ($folders as $f) {
					echo '<option value="', $f, '"';
					if ($folder==$f) echo ' selected="selected"';
					echo '>';
					if ($f=='ALL') echo WT_I18N::translate('All');
					else echo $f;
					echo '</option>';
				}
				echo '</select></span><br />';
		//} else echo $MEDIA_DIRECTORY, '<input name="folder" type="hidden" value="ALL" />';
					?>
			</td>
	<!-- // end select media folders -->
<!-- // NOTE: Row 1 right: -->
	<!-- begin sort files -->
			<td class="descriptionbox wrap width25">
					<?php echo WT_I18N::translate('Sort by title or file name'), help_link('sortby'); ?>
			</td>
			<td class="optionbox wrap width25"><select name="sortby">
				<option value="title" <?php if ($sortby=='title') echo 'selected="selected"'; ?>>
					<?php echo WT_I18N::translate('Sort by title'); ?></option>
				<option value="file" <?php if ($sortby=='file') echo 'selected="selected"'; ?>>
					<?php echo WT_I18N::translate('Sort by file name'); ?></option>
				</select>
			</td>
	<!-- //end sort files -->
	</tr><tr>
<!-- // NOTE: Row 2 left:-->
	<!-- // begin sub directories -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Include subdirectories'), help_link('medialist_recursive'); ?>
			</td>
			<td class="optionbox wrap width25">
				<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?> />
			</td>
				<?php // } ?>
	<!-- // end subdirectories -->
<!-- // NOTE: Row 2 right:-->
	<!-- // begin media objects per page -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Media objects per page'), help_link('media_objects_pp');; ?>
			</td>
			<td class="optionbox wrap width25">
				<select name="max">
					<?php
					foreach (array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200') as $selectEntry) {
						echo '<option value="', $selectEntry, '"';
						if ($selectEntry==$max) echo ' selected="selected"';
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select>
			</td>
	<!-- // end media objects per page -->
	</tr><tr>
<!-- // NOTE: Row 3 left:-->
	<!-- // begin search filter -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Search filters'), help_link('medialist_filters'); ?>
			</td>
			<td class="optionbox wrap width25">
		<!-- // begin Text field for filter and "submit" button -->
				<input id="filter1" name="filter1" value="<?php echo PrintReady($filter1); ?>" size="14" />
				<select name="filter_type">
					<?php
					foreach (array($or, $and) as $selectEntry) {
						echo '<option value="', $selectEntry, '"';
						if ($selectEntry==$filter_type) echo ' selected="selected"';
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select><br />
				<input id="filter2" name="filter2" value="<?php echo PrintReady($filter2); ?>" size="14" />
			</td>
	<!-- // end search filter -->
<!-- // NOTE: Row 3 right:-->
	<!-- // begin columns per page -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Columns per page'), help_link('media_columns_pp'); ?>
				<br />
			<?php echo WT_I18N::translate('Show thumbnails'), help_link('media_thumbs'); ?>
			</td>
			<td class="optionbox wrap width25">
				<select name="columns">
					<?php
					foreach (array('1', '2') as $selectEntry) {
						echo '<option value="', $selectEntry, '"';
						if ($selectEntry==$columns) echo ' selected="selected"';
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select>
				<br /><input type="checkbox" id="thumbnails" name="thumbnails"
				<?php if ($show_thumbnail) { ?>checked="checked"<?php } ?> />
			</td>

	<!-- // end columns per page -->
	</tr><tr>
<!-- // NOTE: Row 4 left:-->
	<!-- // begin search buttons  -->
			<td class="descriptionbox wrap width25">
			</td>
			<td class="optionbox wrap width25">
				<input type="submit" name="apply_filter" value="<?php echo WT_I18N::translate('Search'); ?>" />
				<input type="submit" name="reset" value="<?php echo WT_I18N::translate('Reset'); ?>" />
			</td>
	<!-- // end search buttons  -->
<!-- // NOTE: Row 4 right:-->
	<!-- // thumbnail option  -->
			<td class="descriptionbox wrap width25">&nbsp;
			</td>
			<td class="optionbox wrap width25">&nbsp;
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
if ($action=='filter' && (!empty($filtered_medialist))) {
	$temp_filter = $filter_type;
	if ($filter_type == $or) {
		if ((strlen($filter1) > 1) && (strlen($filter2)) > 1) {
			foreach ($filtered_medialist as $key => $media) {
				if (!filterMedia2($media, $filter1, 'http') && !filterMedia2($media, $filter2, 'http'))
				unset($filtered_medialist[$key]);
			}
			usort($filtered_medialist, 'mediasort'); // Reset numbering of medialist array
		// If either of the filters is empty use the "and" filter
		} else $filter_type = $and;
	}
	if ($filter_type == $and) {
		if ((strlen($filter1) > 1) || (strlen($filter2)) > 1) {
			foreach ($filtered_medialist as $key => $media) {
				if (!filterMedia2($media, $filter1, 'http')) unset($filtered_medialist[$key]);
				if (!filterMedia2($media, $filter2, 'http')) unset($filtered_medialist[$key]);
			}
			usort($filtered_medialist, 'mediasort'); // Reset numbering of medialist array
		}
	}
// Restore filter type
$filter_type = $temp_filter;
}
// ************************  END = 'Filter the medialist array' ************************
// *****************************  BEGIN Set SESSION variables ********************************************
if ($search=='yes') {
	if ($filtered_medialist) $_SESSION['Filtered_medialist'] = $filtered_medialist;
	$_SESSION['Filter_type']=$filter_type;
	$_SESSION['Medialist_filter1']=$filter1;
	$_SESSION['Medialist_filter2']=$filter2;
	$_SESSION['Medialist_folder']=$folder;
	$_SESSION['Medialist_sortby']=$sortby;
	$_SESSION['Medialist_max']=$max;
	$_SESSION['Medialist_columns']=$columns;
	$_SESSION['Medialist_currentdironly']=$currentdironly;
	$_SESSION['Medialist_thumbnail']=$show_thumbnail;
}
// *****************************  End Set SESSION variables ********************************************
// ************************  BEGIN = 'Print the medialist array' ************************
if ($show == 'yes') {
	if (!empty($filtered_medialist)) {
		$sortedMediaList = $filtered_medialist; // Default sort (by title) has already been done
		if ($sortby=='file') usort($sortedMediaList, 'filesort');
		// Count the number of items in the medialist
		$ct=count($sortedMediaList);
		$start = 0;
		//$max = 20;
		if (isset($_GET['start'])) $start = $_GET['start'];
		$count = $max;
		if ($start+$count > $ct) $count = $ct-$start;
	} else $ct = '0';

	echo '<div align="center">', WT_I18N::translate('Media Objects found'), ' ', $ct, ' <br /><br />';

  if ($ct>0) {
	$currentPage = ((int) ($start / $max)) + 1;
	$lastPage = (int) (($ct + $max - 1) / $max);
	$IconRarrow = '<img src="'.$WT_IMAGES['rarrow'].'" width="20" height="20" border="0" alt="" />';
	$IconLarrow = '<img src="'.$WT_IMAGES['larrow'].'" width="20" height="20" border="0" alt="" />';
	$IconRDarrow = '<img src="'.$WT_IMAGES['rdarrow'].'" width="20" height="20" border="0" alt="" />';
	$IconLDarrow = '<img src="'.$WT_IMAGES['ldarrow'].'" width="20" height="20" border="0" alt="" />';

	echo '<table class="list_table">';
	// echo page back, page number, page forward controls
	echo '<tr><td colspan="2">';
	echo '<table class="list_table width100">';
	echo '<tr>';
	echo '<td class="width30" align="', $TEXT_DIRECTION == 'ltr'?'left':'right', '">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '">', $IconLDarrow, '</a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconLarrow, '</a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '">', $IconRDarrow, '</a>';
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconRarrow, '</a>';
			}
		}
	}
	echo '</td>';
	echo '<td align="center">', WT_I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
	echo '<td class="width30" align="', $TEXT_DIRECTION == 'ltr'?'right':'left', '">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconRarrow, '</a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '">', $IconRDarrow, '</a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconLarrow, '</a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '">', $IconLDarrow, '</a>';
			}
		}
	}
	echo '</td>';
	echo '</tr></table></td></tr>';

	echo '<tr>';
	for ($i=0; $i<$count; $i++) { 	// begin looping through the media
		$media = $sortedMediaList[$start+$i];
		$mediaobject = WT_Media::getInstance($media['XREF']);
		if (!$mediaobject) {
			// the media object was apparently deleted after the medialist was stored in the session
			continue;
		}
		$isExternal = $mediaobject->isExternal();
		if ($columns == '1') echo '<td class="list_value_wrap" width="80%">';
		if ($columns == '2') echo '<td class="list_value_wrap" width="50%">';

		echo '<table class="', $TEXT_DIRECTION, '"><tr><td valign="top" style="white-space: normal;">';

		//-- Thumbnail field
		if ($show_thumbnail) {
			echo $mediaobject->displayMedia(array('which'=>'thumb'));
			echo '</td><td class="list_value_wrap" style="border: none;" width="100%">';

			if (WT_USE_LIGHTBOX) {
				if (WT_USER_CAN_EDIT) {
					echo '<table border=0><tr>';
					// ---------- Edit Media --------------------
					echo '<td class="width33 wrap center font9" valign="top">';
					echo "<a href=\"javascript:;\" title=\"" . WT_I18N::translate('Edit this Media Item\'s Details') . "\" onclick=\" return window.open('addmedia.php?action=editmedia&pid=".$mediaobject->getXref()."&linktoid=', '_blank', 'top=50, left=50, width=600, height=600, resizable=1, scrollbars=1');\">";
					echo '<img src="'.WT_MODULES_DIR.'lightbox/images/image_edit.gif" alt="" class="icon" title="', WT_I18N::translate('Edit this Media Item\'s Details'), '" />&nbsp;&nbsp;&nbsp;';
					echo '<br />';
					echo WT_I18N::translate('Edit Details') ;
					echo '</a>';
					echo '</td>';
					// ---------- Link Media to person, family or source  ---------------
					echo '<td class="width33 wrap center font9" valign="top">';
					require  WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_link.php';
					echo '</td>';
					// ---------- View Media Details (mediaviewer) --------------------
					echo '<td class="width33 wrap center font9" valign="top">';
					echo '<a href="'.$mediaobject->getHtmlUrl().'" title="', WT_I18N::translate('View this Media Item\'s Details 
Plus other Media Options - MediaViewer page'), '">';
					echo '&nbsp;&nbsp;&nbsp;<img src="'.WT_MODULES_DIR.'lightbox/images/image_view.gif" alt="" class="icon" title="', WT_I18N::translate('View this Media Item\'s Details 
Plus other Media Options - MediaViewer page'), '" />';
					echo '<br />';
					echo WT_I18N::translate('View Details') ;
					echo '</a>';
					echo '</td>';
					echo '</tr></table>';
					// ------------ Linespace ---------------------
					echo '<br />';
				}
			}
		}
		// -- new naming structure ---------
		if ($sortby == 'title') {
			$name_disp1 = $mediaobject->getFullName();
			$name_disp2 = basename($mediaobject->getFilename());
			if ($isExternal) $name_disp2 = 'URL';
			$name_disp3 = $mediaobject->getLocalFilename();
			$name_disp4 = WT_I18N::translate('Filename');
		} else {
			$name_disp1 = basename($mediaobject->getFilename());
			if ($isExternal) $name_disp1 = 'URL';
			$name_disp2 = $mediaobject->getFullName();
			$name_disp3 = $mediaobject->getLocalFilename();
			$name_disp4 = WT_I18N::translate('Title');
		}
		echo '<a href="'.$mediaobject->getHtmlUrl().'">';
		echo '<b>', PrintReady($name_disp1), '</b>';
		echo '</a>';
		if (WT_USER_CAN_EDIT) {
				echo '<br /><br /><sub><span dir="ltr"><b>', PrintReady($name_disp4), ': </b>', PrintReady($name_disp2), '</span></sub>';
				echo '<br /><sub><span dir="ltr"><b>', WT_I18N::translate('Location'), ': </b>', PrintReady($name_disp3), '</span></sub>';
		}
		echo '<br />';
		if (!$isExternal) {
			if (!$mediaobject->fileExists()) {
				echo '<br /><span class="error">', WT_I18N::translate('File not found.'), ' <span dir="ltr">', PrintReady(basename($mediaobject->getFilename())), '</span></span>';
			} else {
				$imgsize = $mediaobject->getImageAttributes();
				echo '<span class="label"><br />', WT_I18N::translate('Media Format'), ': </span>'; 
				echo '<span class="field" style="direction: ltr;">', ($imgsize['ext']) ? $imgsize['ext'] : '--', '</span>';
				echo '&nbsp;&nbsp;&nbsp;<span class="field" style="direction: ltr;">', $mediaobject->getFilesize(), '</span>';
				if ($imgsize['WxH']) {
					echo '<span class="label"><br />', WT_I18N::translate('Image Dimensions'), ': </span> <span class="field">', $imgsize['WxH'], '</span>';
				}
			}
		}
			echo '<div style="white-space: normal; width: 95%;">';
			print_fact_sources($mediaobject->getGedcomRecord(), 1);
			print_fact_notes($mediaobject->getGedcomRecord(), 1);
			echo '</div>';
			echo $mediaobject->printLinkedRecords('small');
			echo '</td></tr></table>';
			echo '</td>';
			if ($columns == '1') echo '</tr><tr>';
			if (($columns == '2') && ($i%2 == 1 && $i < ($count-1)))
			echo '</tr><tr>';
	} // end media loop
	echo '</tr>';
	// echo page back, page number, page forward controls
	echo '<tr><td colspan="2">';
	echo '<table class="list_table width100">';
	echo '<tr>';
	echo '<td class="width30" align="', $TEXT_DIRECTION == 'ltr'?'left':'right', '">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '">', $IconLDarrow, '</a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconLarrow, '</a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '">', $IconRDarrow, '</a>';
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconRarrow, '</a>';
			}
		}
	}
	echo '</td>';
	echo '<td align="center">', WT_I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
	echo '<td class="width30" align="', $TEXT_DIRECTION == 'ltr'?'right':'left', '">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconRarrow, '</a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '">', $IconRDarrow, '</a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '">', $IconLarrow, '</a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '">', $IconLDarrow, '</a>';
			}
		}
	}
	echo '</td>';
	echo '</tr></table></td></tr>';
	echo '</table><br />';
  }
  echo '</div>';
}
// ************************  END = 'Print the medialist array' ************************
print_footer();
