<?php
// Displays a list of the media objects
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

define('WT_SCRIPT_NAME', 'medialist.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';

$controller = new WT_Controller_Page();
$controller
	->setPageTitle(WT_I18N::translate('Media objects'))
	->pageHeader();

$search = WT_Filter::get('search');
$sortby = WT_Filter::get('sortby', 'file|title', 'title');
if (!WT_USER_CAN_EDIT && !WT_USER_CAN_ACCEPT) {
	$sortby='title';
}
$start          = WT_Filter::getInteger('start');
$max            = WT_Filter::get('max', '10|20|30|40|50|75|100|125|150|200', '20');
$folder         = WT_Filter::get('folder', null, ''); // MySQL needs an empty string, not NULL
$reset          = WT_Filter::get('reset');
$apply_filter   = WT_Filter::get('apply_filter');
$filter         = WT_Filter::get('filter', null, ''); // MySQL needs an empty string, not NULL
$columns        = WT_Filter::getInteger('columns', 1, 2, 2);
$subdirs        = WT_Filter::get('subdirs', 'on');
$currentdironly = ($subdirs=='on') ? false : true;

// reset all variables
if ($reset == 'Reset') {
	$sortby = 'title';
	$max = '20';
	$folder = '';
	$columns = '2';
	$currentdironly = true;
	$filter = '';
}

// A list of all subfolders used by this tree
$folders = WT_Query_Media::folderList();

// A list of all media objects matching the search criteria
$medialist = WT_Query_Media::mediaList(
	$folder,
	$currentdironly ? 'exclude' : 'include',
	$sortby,
	$filter
);

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
				<input id="filter" name="filter" value="<?php echo WT_Filter::escapeHtml($filter); ?>" size="14" dir="auto">
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
	$ct = count($medialist);
	$count = $max;
	if ($start + $count > $ct) {
		$count = $ct - $start;
	}

	echo '<div><p style="text-align: center;">', WT_I18N::translate('Media objects found'), ' ', $ct, '</p>';

	if ($ct>0) {
		$currentPage = ((int) ($start / $max)) + 1;
		$lastPage = (int) (($ct + $max - 1) / $max);

		echo '<table class="list_table width100">';
		// Display controls twice - at the top and bottom of the table
		foreach (array('thead', 'tfoot') as $tsection) {
			echo '<', $tsection, '><tr><td colspan="2">';

			echo '<table class="list_table_controls"><tr><td>';
			if ($TEXT_DIRECTION=='ltr') {
				if ($ct>$max) {
					if ($currentPage > 1) {
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
					}
					if ($start>0) {
						$newstart = $start-$max;
						if ($start<0) $start = 0;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
					}
				}
			} else {
				if ($ct>$max) {
					if ($currentPage < $lastPage) {
						$lastStart = ((int) ($ct / $max)) * $max;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
					}
					if ($start+$max < $ct) {
						$newstart = $start+$count;
						if ($start<0) $start = 0;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
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
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
					}
					if ($currentPage < $lastPage) {
						$lastStart = ((int) ($ct / $max)) * $max;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
					}
				}
			} else {
				if ($ct>$max) {
					if ($start>0) {
						$newstart = $start-$max;
						if ($start<0) $start = 0;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
					}
					if ($currentPage > 1) {
						$lastStart = ((int) ($ct / $max)) * $max;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
					}
				}
			}
			echo '</td></tr></table>';

			echo '</td></tr></', $tsection, '>';
		}

		echo '<tbody><tr>';
		for ($i=$start, $n=0; $i<$start+$count; ++$i) {
			$mediaobject = $medialist[$i];

			if ($columns == '1') echo '<td class="list_value_wrap width80">';
			if ($columns == '2') echo '<td class="list_value_wrap width50">';

			echo '<table><tr><td style="vertical-align:top; white-space:normal;">';
			echo $mediaobject->displayImage();
			echo '</td><td class="list_value_wrap width100" style="border: none; padding-left: 5px;">';
			if (WT_USER_CAN_EDIT) {
				echo WT_Controller_Media::getMediaListMenu($mediaobject);
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
			echo print_fact_sources($mediaobject->getGedcom(), 1);
			echo print_fact_notes($mediaobject->getGedcom(), 1);
			echo '</div>';
			foreach ($mediaobject->linkedIndividuals('OBJE') as $individual) {
				echo '<a href="' . $individual->getHtmlUrl() . '">' . WT_I18N::translate('View person') . ' — ' . $individual->getFullname().'</a><br>';
			}
			foreach ($mediaobject->linkedFamilies('OBJE') as $family) {
				echo '<a href="' . $family->getHtmlUrl() . '">' . WT_I18N::translate('View family') . ' — ' . $family->getFullname().'</a><br>';
			}
			foreach ($mediaobject->linkedSources('OBJE') as $source) {
				echo '<a href="' . $source->getHtmlUrl() . '">' . WT_I18N::translate('View source') . ' — ' . $source->getFullname().'</a><br>';
			}
			echo '</td></tr></table>';
			echo '</td>';
			if ((++$n) % $columns == 0 && $n < $count) {
				echo '</tr><tr>';
			}
		} // end media loop

		// An odd number of media objects in two columns requires an empty cell
		if ($columns == 2 && $n%2 == 1) {
			echo '<td>&nbsp;</td>';
		}

		echo '</tr></tbody>';
		echo '</table>';
	}
  echo '</div>';
}
echo '</div>';

