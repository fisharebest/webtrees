<?php
// Displays a list of the media objects
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

$controller=new WT_Controller_Base();
$controller->setPageTitle(WT_I18N::translate('Media objects'));

$action = safe_GET('action');
$search = safe_GET('search');
$sortby = safe_GET('sortby', 'file', 'title');
if (!WT_USER_CAN_EDIT && !WT_USER_CAN_ACCEPT) {
	$sortby='title';
}
$max = safe_GET('max', array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200'), '20');
$folder = safe_GET('folder');
$show = 'no';
$build = 'no';
$reset = safe_GET('reset');
$apply_filter = safe_GET('apply_filter');
$filter1 = safe_GET('filter1');
$filter2 = safe_GET('filter2');
$or = WT_I18N::translate('or');
$and = WT_I18N::translate('and');
$filter_type = safe_GET('filter_type', array($or, $and), $or);
$columns = safe_GET('columns', array('1', '2'), '2');
$subdirs = safe_GET('subdirs');
$thumbnail = safe_GET('thumbnail');
$currentdironly = ($subdirs=='on') ? false : true;
$show_thumbnail = ($thumbnail=='on') ? true : false;

// reset all variables
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
	unset($WT_SESSION->Medialist);
}

// If the $folder is empty this is a new visit, a return, or a reset
if (empty($folder)) {
	$folder = $MEDIA_DIRECTORY; // default setting
	$show_thumbnail = true; // default setting
}

// re-build if anything changed
if (
	($WT_SESSION->Medialist_ged != WT_GEDCOM)
	|| ($WT_SESSION->Medialist_user != WT_USER_ID) // if an anonymous user logged in, get the data again
	|| ($filter1 != $WT_SESSION->Medialist_filter1) 
	|| ($filter2 != $WT_SESSION->Medialist_filter2) 
	|| ($filter_type != $WT_SESSION->Medialist_filter_type)
	|| ($sortby != $WT_SESSION->Medialist_sortby)
	|| ($folder != $WT_SESSION->Medialist_folder)
	|| ($currentdironly != $WT_SESSION->Medialist_currentdironly)
	) {
	$build = 'yes';
	unset($WT_SESSION->Medialist);
}

// If SESSION_medialist then it's a return
if (isset($WT_SESSION->Medialist)) {
	$show = 'yes';
	$search = 'yes';
	$action = false;
	$medialist = $WT_SESSION->Medialist;
} else {
	if ($action == 'filter') {
		// This is the first visit to the medialist page
		$build = 'yes';
		$show = 'yes';
	} else {
		// must have just reset
	}
}

// ************************  BEGIN = 'Build the medialist array' ************************
if ($build == 'yes') {
	if ($folder == 'ALL') {
		$folder = $MEDIA_DIRECTORY;
		$currentdironly = false;
	}
	// show external links only if looking at top level directory
	$showExternal = ($folder == $MEDIA_DIRECTORY) ? true : false;
	$medialist=get_medialist2($currentdironly, $folder, true, false, $showExternal, $sortby);

	foreach ($medialist as $key => $media) {
		// remove all private media objects
		$mediaobject=WT_Media::getInstance($media['XREF']);
		// Display when user has Edit rights or when object belongs to current GEDCOM
		$disp = WT_USER_CAN_EDIT || $mediaobject->ged_id==WT_GED_ID;
		// Display when Media objects aren't restricted by global privacy
		$disp &= $mediaobject->canDisplayDetails();
		// Display when this Media object isn't restricted
		$disp &= canDisplayFact($mediaobject->getXref(), $mediaobject->ged_id, $mediaobject->getGedcomRecord());
		if (!$disp) {
			// echo "removing $key - disp<br>";
			unset($medialist[$key]);
			continue;
		}
		// filter
		if ($filter1 || $filter2) {
			if (!$filter1 || !$filter2) $filter_type = $or;
			$default=($filter_type == $or) ? false : true;
			$found1=$filter1 ? filterMedia2($mediaobject, $filter1) : $default;
			$found2=$filter2 ? filterMedia2($mediaobject, $filter2) : $default;

			if ($filter_type == $or) {
				if (!$found1 && !$found2) {
					// echo "removing $key - OR<br>";
					unset($medialist[$key]);
				}
			} else {
				if (!$found1 || !$found2) {
					// echo "removing $key - AND<br>";
					unset($medialist[$key]);
				}
			}
		}
	}
	$medialist=array_values($medialist); // Reset numbering of medialist array

	// save the data into the session
	$WT_SESSION->Medialist=$medialist;
	$WT_SESSION->Medialist_filter_type=$filter_type;
	$WT_SESSION->Medialist_filter1=$filter1;
	$WT_SESSION->Medialist_filter2=$filter2;
	$WT_SESSION->Medialist_folder=$folder;
	$WT_SESSION->Medialist_sortby=$sortby;
	$WT_SESSION->Medialist_max=$max;
	$WT_SESSION->Medialist_columns=$columns;
	$WT_SESSION->Medialist_currentdironly=$currentdironly;
	$WT_SESSION->Medialist_thumbnail=$show_thumbnail;
	$WT_SESSION->Medialist_ged=WT_GEDCOM;
	$WT_SESSION->Medialist_user=WT_USER_ID;
}

$controller->pageHeader();

echo '<div id="medialist-page"><h2>', $controller->getPageTitle(), '</h2>';
if (WT_USE_LIGHTBOX) {
	$album = new lightbox_WT_Module();
	$album->getPreLoadContent();
}
// ************************  BEGIN = 'Build the input form' ************************
// A form for filtering the media items
?>
<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="search" value="yes">
	<table class="list_table width75">
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
					if (!empty($WT_SESSION->upload_folder)) $folder = $WT_SESSION->upload_folder;
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
				echo '</select></span><br>';
		//} else echo $MEDIA_DIRECTORY, '<input name="folder" type="hidden" value="ALL">';
					?>
			</td>
	<!-- // end select media folders -->
<!-- // NOTE: Row 1 right: -->
	<!-- begin sort files -->
			<?php	
			if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
				echo '<td class="descriptionbox wrap width25">';
				echo WT_I18N::translate('Sort order');
				echo '</td><td class="optionbox wrap width25">';
				echo '<select name="sortby">';
				echo '<option value="title" ', ($sortby=='title') ? 'selected="selected"' : '', '>';
				echo /* I18N: An option in a list-box */ WT_I18N::translate('sort by title');
				echo '</option>';
				echo '<option value="file" ', ($sortby=='file') ? 'selected="selected"' : '' , '>';
				echo /* I18N: An option in a list-box */ WT_I18N::translate('sort by filename');
				echo '</option>';
				echo '</select>';
				echo '</td>';
			} else {
				echo '<td class="descriptionbox wrap width25">&nbsp;</td>';
				echo '<td class="optionbox wrap width25">&nbsp;</td>';
			}
			?>
	<!-- //end sort files -->
	</tr><tr>
<!-- // NOTE: Row 2 left:-->
	<!-- // begin sub directories -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Include subdirectories'), help_link('medialist_recursive'); ?>
			</td>
			<td class="optionbox wrap width25">
				<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?>>
			</td>
				<?php // } ?>
	<!-- // end subdirectories -->
<!-- // NOTE: Row 2 right:-->
	<!-- // begin media objects per page -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Media objects per page'); ?>
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
				<?php echo WT_I18N::translate('Search filters'); ?>
			</td>
			<td class="optionbox wrap width25">
		<!-- // begin Text field for filter and "submit" button -->
				<input id="filter1" name="filter1" value="<?php echo $filter1; ?>" size="14" dir="auto">
				<select name="filter_type">
					<?php
					foreach (array($or, $and) as $selectEntry) {
						echo '<option value="', $selectEntry, '"';
						if ($selectEntry==$filter_type) echo ' selected="selected"';
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select><br>
				<input id="filter2" name="filter2" value="<?php echo $filter2; ?>" size="14" dir="auto">
			</td>
	<!-- // end search filter -->
<!-- // NOTE: Row 3 right:-->
	<!-- // begin columns per page -->
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Columns per page'); ?>
				<br>
			<?php echo WT_I18N::translate('Show thumbnails'); ?>
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
				<br><input type="checkbox" id="thumbnail" name="thumbnail"
				<?php if ($show_thumbnail) { ?>checked="checked"<?php } ?>>
			</td>

	<!-- // end columns per page -->
	</tr><tr>
<!-- // NOTE: Row 4 left:-->
	<!-- // begin search buttons  -->
			<td class="descriptionbox wrap width25">
			</td>
			<td class="optionbox wrap width25">
				<input type="submit" name="apply_filter" value="<?php echo WT_I18N::translate('Search'); ?>">
				<input type="submit" name="reset" value="<?php echo WT_I18N::translate('Reset'); ?>">
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
// ************************  BEGIN = 'Print the medialist array' ************************
if ($show == 'yes') {
	if (!empty($medialist)) {
		// Count the number of items in the medialist
		$ct=count($medialist);
		$start = 0;
		if (isset($_GET['start'])) $start = $_GET['start'];
		$count = $max;
		if ($start+$count > $ct) $count = $ct-$start;
	} else {
		$ct = '0';
	}

	echo '<div align="center">', WT_I18N::translate('Media Objects found'), ' ', $ct, ' <br><br>';

  if ($ct>0) {
	$currentPage = ((int) ($start / $max)) + 1;
	$lastPage = (int) (($ct + $max - 1) / $max);

	echo '<table class="list_table">';
	// echo page back, page number, page forward controls
	echo '<tr><td colspan="2">';
	echo '<table class="list_table width100">';
	echo '<tr>';
	echo '<td class="width30">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '<td align="center">', WT_I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
	echo '<td class="width30">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '"class="icon-rarrow"></a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '</tr></table></td></tr>';

	echo '<tr>';
	for ($i=0; $i<$count; $i++) { 	// begin looping through the media
		$media = $medialist[$start+$i];
		$mediaobject = WT_Media::getInstance($media['XREF']);
		if (!$mediaobject) {
			// the media object was apparently deleted after the medialist was stored in the session
			continue;
		}
		$isExternal = $mediaobject->isExternal();
		if ($columns == '1') echo '<td class="list_value_wrap" width="80%">';
		if ($columns == '2') echo '<td class="list_value_wrap" width="50%">';

		echo '<table><tr><td valign="top" style="white-space:normal;">';

		//-- Thumbnail field
		if ($show_thumbnail) {
			echo $mediaobject->displayMedia(array());
			echo '</td><td class="list_value_wrap" style="border:none;" width="100%">';
			if (WT_USE_LIGHTBOX && WT_USER_CAN_EDIT) {
				echo lightbox_WT_Module::getMediaListMenu($mediaobject);
			}
		}
		// If sorting by title, highlight the title.  If sorting by filename, highlight the filename
		if ($sortby=='title') {
			echo '<p><b><a href="', $mediaobject->getHtmlUrl(), '">';
			echo $mediaobject->getFullName();
			echo '</a></b></p>';
		} else {
			echo '<p><b><a href="', $mediaobject->getHtmlUrl(), '">';
			echo basename($mediaobject->getFilename());
			echo '</a></b></p>';
			echo WT_Gedcom_Tag::getLabelValue('TITL', $mediaobject->getFullName());
		}
		// Show file details
		if ($isExternal) {
			echo WT_Gedcom_Tag::getLabelValue('URL', $mediaobject->getLocalFilename());
		} else {
			if ($mediaobject->fileExists()) {
				if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
					echo WT_Gedcom_Tag::getLabelValue('FILE', $mediaobject->getLocalFilename());
				}
				echo WT_Gedcom_Tag::getLabelValue('FORM', $mediaobject->getMediaFormat());
				echo WT_Gedcom_Tag::getLabelValue('__FILE_SIZE__', $mediaobject->getFilesize());
				$imgsize = $mediaobject->getImageAttributes();
				if ($imgsize['WxH']) {
					echo WT_Gedcom_Tag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
				}
			} else {
				echo '<p class="ui-state-error">', /* I18N: %s is a filename */ WT_I18N::translate('The file “%s” does not exist.', $mediaobject->getLocalFilename()), '</p>';
			}
		}
		echo '<br>';
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
	echo '<td class="width30">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
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
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '<td align="center">', WT_I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
	echo '<td class="width30">';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;filter_type=', $filter_type, '&amp;filter2=', rawurlencode($filter2), '&amp;columns=', $columns, '&amp;thumbnail=', $thumbnail, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '</tr></table></td></tr>';
	echo '</table><br>';
  }
  echo '</div>
  </div>';// close medialist-page
}
