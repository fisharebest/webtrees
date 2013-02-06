<?php
// Displays a list of the media objects
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';

$controller=new WT_Controller_Page();
$controller->setPageTitle(WT_I18N::translate('Media objects'));

$search = safe_GET('search');
$sortby = safe_GET('sortby', 'file', 'title');
if (!WT_USER_CAN_EDIT && !WT_USER_CAN_ACCEPT) {
	$sortby='title';
}
$max   = safe_GET('max', array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200'), '20');
$start = safe_GET('start', WT_REGEX_INTEGER);
$folder = safe_GET('folder');
$build = 'no';
$reset = safe_GET('reset');
$apply_filter = safe_GET('apply_filter');
$filter1 = safe_GET('filter1');
$or = WT_I18N::translate('or');
$and = WT_I18N::translate('and');
$columns = safe_GET('columns', array('1', '2'), '2');
$subdirs = safe_GET('subdirs');
$currentdironly = ($subdirs=='on') ? false : true;

// reset all variables
if ($reset == 'Reset') {
	$sortby = 'title';
	$max = '20';
	$folder = '';
	$columns = '2';
	$currentdironly = true;
	$filter1 = '';
}

// A list of all subfolders used by this tree
$folders = WT_Query_Media::folderList();

// A list of all media objects matching the search criteria
$medialist = WT_Query_Media::mediaList(
	$folder,
	$currentdironly ? 'exclude' : 'include',
	$sortby,
	$filter1
);

$controller->pageHeader();

?>
<div id="medialist-page"><h2><?php echo $controller->getPageTitle(); ?></h2>

<form action="medialist.php" method="get">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="search" value="yes">
	<table class="list_table width75">
		<tr>
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Folder'); ?>
			</td>
			<td class="optionbox wrap width25">
				<?php echo select_edit_control('folder', $folders, null, $folder); ?>
			</td>
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
		</tr>
		<tr>
			<td class="descriptionbox wrap width25">
				<?php echo /* I18N: Label for check-box */ WT_I18N::translate('Include subfolders'); ?>
			</td>
			<td class="optionbox wrap width25">
				<input type="checkbox" id="subdirs" name="subdirs" <?php if (!$currentdironly) { ?>checked="checked"<?php } ?>>
			</td>
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
		</tr>
		<tr>
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Search filters'); ?>
			</td>
			<td class="optionbox wrap width25">
				<input id="filter1" name="filter1" value="<?php echo $filter1; ?>" size="14" dir="auto">
			</td>
			<td class="descriptionbox wrap width25">
				<?php echo WT_I18N::translate('Columns per page'); ?>
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
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap width25">
			</td>
			<td class="optionbox wrap width25">
				<input type="submit" name="apply_filter" value="<?php echo WT_I18N::translate('Search'); ?>">
				<input type="submit" name="reset" value="<?php echo WT_I18N::translate('Reset'); ?>">
			</td>
			<td class="descriptionbox wrap width25">&nbsp;</td>
			<td class="optionbox wrap width25">&nbsp;</td>
		</tr>
	</table>
</form>

<?php
if ($search) {
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
	echo '<table class="list_table_controls">';
	echo '<tr>';
	echo '<td>';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($currentPage > 1) {
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '<td>', WT_I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
	echo '<td>';
	if ($TEXT_DIRECTION=='ltr') {
		if ($ct>$max) {
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '"class="icon-rarrow"></a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
		}
	}
	echo '</td>';
	echo '</tr></table></td></tr>';

	echo '<tr>';
	for ($i=$start, $n=0; $i<$start+$count; ++$i) {
		$mediaobject = $medialist[$i];

		if ($columns == '1') echo '<td class="list_value_wrap" width="80%">';
		if ($columns == '2') echo '<td class="list_value_wrap" width="50%">';

		echo '<table><tr><td valign="top" style="white-space:normal;">';
		echo $mediaobject->displayImage();
		echo '</td><td class="list_value_wrap" style="border:none;" width="100%">';
		if (WT_USE_LIGHTBOX && WT_USER_CAN_EDIT) {
			echo lightbox_WT_Module::getMediaListMenu($mediaobject);
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
		if ($mediaobject->isExternal()) {
			echo WT_Gedcom_Tag::getLabelValue('URL', $mediaobject->getFilename());
		} else {
			if ($mediaobject->fileExists()) {
				if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
					echo WT_Gedcom_Tag::getLabelValue('FILE', $mediaobject->getFilename());
				}
				echo WT_Gedcom_Tag::getLabelValue('FORM', $mediaobject->mimeType());
				echo WT_Gedcom_Tag::getLabelValue('__FILE_SIZE__', $mediaobject->getFilesize());
				$imgsize = $mediaobject->getImageAttributes();
				if ($imgsize['WxH']) {
					echo WT_Gedcom_Tag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
				}
			} else {
				echo '<p class="ui-state-error">', /* I18N: %s is a filename */ WT_I18N::translate('The file “%s” does not exist.', $mediaobject->getFilename()), '</p>';
			}
		}
		echo '<br>';
		echo '<div style="white-space: normal; width: 95%;">';
		print_fact_sources($mediaobject->getGedcomRecord(), 1);
		print_fact_notes($mediaobject->getGedcomRecord(), 1);
		echo '</div>';
		foreach ($mediaobject->fetchLinkedIndividuals() as $individual) {
			echo '<a href="' . $individual->getHtmlUrl() . '">' . WT_I18N::translate('View Person') . ' — ' . $individual->getFullname().'</a><br>';
		}
		foreach ($mediaobject->fetchLinkedFamilies() as $family) {
			echo '<a href="' . $family->getHtmlUrl() . '">' . WT_I18N::translate('View Family') . ' — ' . $family->getFullname().'</a><br>';
		}
		foreach ($mediaobject->fetchLinkedSources() as $source) {
			echo '<a href="' . $source->getHtmlUrl() . '">' . WT_I18N::translate('View Source') . ' — ' . $source->getFullname().'</a><br>';
		}
		echo '</td></tr></table>';
		echo '</td>';
		if ((++$n) % $columns == 0) {
			echo '</tr><tr>';
		}
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
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
			}
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
			if ($start+$max < $ct) {
				$newstart = $start+$count;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
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
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
			}
			if ($currentPage < $lastPage) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
			}
		}
	} else {
		if ($ct>$max) {
			if ($start>0) {
				$newstart = $start-$max;
				if ($start<0) $start = 0;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
			}
			if ($currentPage > 1) {
				$lastStart = ((int) ($ct / $max)) * $max;
				echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter1=', rawurlencode($filter1), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
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
