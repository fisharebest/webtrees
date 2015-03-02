<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

define('WT_SCRIPT_NAME', 'medialist.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate('Media objects'))
	->pageHeader();

$search = Filter::get('search');
$sortby = Filter::get('sortby', 'file|title', 'title');
if (!WT_USER_CAN_EDIT && !WT_USER_CAN_ACCEPT) {
	$sortby = 'title';
}
$start          = Filter::getInteger('start');
$max            = Filter::get('max', '10|20|30|40|50|75|100|125|150|200', '20');
$folder         = Filter::get('folder', null, ''); // MySQL needs an empty string, not NULL
$reset          = Filter::get('reset');
$apply_filter   = Filter::get('apply_filter');
$filter         = Filter::get('filter', null, ''); // MySQL needs an empty string, not NULL
$columns        = Filter::getInteger('columns', 1, 2, 2);
$subdirs        = Filter::get('subdirs', 'on');
$currentdironly = ($subdirs === 'on') ? false : true;

// reset all variables
if ($reset === 'Reset') {
	$sortby         = 'title';
	$max            = '20';
	$folder         = '';
	$columns        = '2';
	$currentdironly = true;
	$filter         = '';
}

// A list of all subfolders used by this tree
$folders = QueryMedia::folderList();

// A list of all media objects matching the search criteria
$medialist = QueryMedia::mediaList(
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
	<table class="list_table">
		<tr>
			<td class="descriptionbox wrap">
				<?php echo I18N::translate('Folder'); ?>
			</td>
			<td class="optionbox wrap">
				<?php echo select_edit_control('folder', $folders, null, $folder); ?>
			</td>
			<?php
			if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
				echo '<td class="descriptionbox wrap">';
				echo I18N::translate('Sort order');
				echo '</td><td class="optionbox wrap">';
				echo '<select name="sortby">';
				echo '<option value="title" ', ($sortby == 'title') ? 'selected' : '', '>';
				echo /* I18N: An option in a list-box */ I18N::translate('sort by title');
				echo '</option>';
				echo '<option value="file" ', ($sortby == 'file') ? 'selected' : '', '>';
				echo /* I18N: An option in a list-box */ I18N::translate('sort by filename');
				echo '</option>';
				echo '</select>';
				echo '</td>';
			} else {
				echo '<td class="descriptionbox wrap"></td>';
				echo '<td class="optionbox wrap"></td>';
			}
			?>
		</tr>
		<tr>
			<td class="descriptionbox wrap">
				<?php echo /* I18N: Label for check-box */ I18N::translate('Include subfolders'); ?>
			</td>
			<td class="optionbox wrap">
				<input type="checkbox" id="subdirs" name="subdirs" <?php echo $currentdironly ? '' : 'checked'; ?>>
			</td>
			<td class="descriptionbox wrap">
				<?php echo I18N::translate('Media objects per page'); ?>
			</td>
			<td class="optionbox wrap">
				<select name="max">
					<?php
					foreach (array('10', '20', '30', '40', '50', '75', '100', '125', '150', '200') as $selectEntry) {
						echo '<option value="', $selectEntry, '" ';
						if ($selectEntry == $max) {
							echo 'selected';
						}
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap">
				<?php echo I18N::translate('Search filters'); ?>
			</td>
			<td class="optionbox wrap">
				<input id="filter" name="filter" value="<?php echo Filter::escapeHtml($filter); ?>" size="14" dir="auto">
			</td>
			<td class="descriptionbox wrap">
				<?php echo I18N::translate('Columns per page'); ?>
			</td>
			<td class="optionbox wrap">
				<select name="columns">
					<?php
					foreach (array('1', '2') as $selectEntry) {
						echo '<option value="', $selectEntry, '" ';
						if ($selectEntry == $columns) {
							echo 'selected';
						}
						echo '>', $selectEntry, '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="descriptionbox wrap">
			</td>
			<td class="optionbox wrap">
				<input type="submit" name="apply_filter" value="<?php echo I18N::translate('Search'); ?>">
				<input type="submit" name="reset" value="<?php echo I18N::translate('Reset'); ?>">
			</td>
			<td class="descriptionbox wrap"></td>
			<td class="optionbox wrap"></td>
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

	echo '<div><p>', I18N::translate('Media objects found'), ' ', $ct, '</p>';

	if ($ct > 0) {
		$currentPage = ((int) ($start / $max)) + 1;
		$lastPage    = (int) (($ct + $max - 1) / $max);

		echo '<table class="list_table">';
		// Display controls twice - at the top and bottom of the table
		foreach (array('thead', 'tfoot') as $tsection) {
			echo '<', $tsection, '><tr><td colspan="2">';

			echo '<table class="list_table_controls"><tr><td>';
			if (I18N::direction() === 'ltr') {
				if ($ct > $max) {
					if ($currentPage > 1) {
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=0&amp;max=', $max, '" class="icon-ldarrow"></a>';
					}
					if ($start > 0) {
						$newstart = $start - $max;
						if ($start < 0) {
							$start = 0;
						}
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-larrow"></a>';
					}
				}
			} else {
				if ($ct > $max) {
					if ($currentPage < $lastPage) {
						$lastStart = ((int) ($ct / $max)) * $max;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
					}
					if ($start + $max < $ct) {
						$newstart = $start + $count;
						if ($start < 0) {
							$start = 0;
						}
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
					}
				}
			}
			echo '</td>';
			echo '<td>', I18N::translate('Page %s of %s', $currentPage, $lastPage), '</td>';
			echo '<td>';
			if (I18N::direction() === 'ltr') {
				if ($ct > $max) {
					if ($start + $max < $ct) {
						$newstart = $start + $count;
						if ($start < 0) {
							$start = 0;
						}
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $newstart, '&amp;max=', $max, '" class="icon-rarrow"></a>';
					}
					if ($currentPage < $lastPage) {
						$lastStart = ((int) ($ct / $max)) * $max;
						echo '<a href="medialist.php?action=no&amp;search=no&amp;folder=', rawurlencode($folder), '&amp;sortby=', $sortby, '&amp;subdirs=', $subdirs, '&amp;filter=', rawurlencode($filter), '&amp;columns=', $columns, '&amp;apply_filter=', $apply_filter, '&amp;start=', $lastStart, '&amp;max=', $max, '" class="icon-rdarrow"></a>';
					}
				}
			} else {
				if ($ct > $max) {
					if ($start > 0) {
						$newstart = $start - $max;
						if ($start < 0) {
							$start = 0;
						}
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
		for ($i = $start, $n = 0; $i < $start + $count; ++$i) {
			$mediaobject = $medialist[$i];

			if ($columns == '1') {
				echo '<td class="media-col1 list_value_wrap">';
			}
			if ($columns == '2') {
				echo '<td class="media-col2 list_value_wrap">';
			}

			echo '<table><tr><td class="media-image">';
			echo $mediaobject->displayImage();
			echo '</td><td class="media-col list_value_wrap">';
			if (WT_USER_CAN_EDIT) {
				echo MediaController::getMediaListMenu($mediaobject);
			}
			// If sorting by title, highlight the title.  If sorting by filename, highlight the filename
			if ($sortby === 'title') {
				echo '<p><b><a href="', $mediaobject->getHtmlUrl(), '">';
				echo $mediaobject->getFullName();
				echo '</a></b></p>';
			} else {
				echo '<p><b><a href="', $mediaobject->getHtmlUrl(), '">';
				echo basename($mediaobject->getFilename());
				echo '</a></b></p>';
				echo GedcomTag::getLabelValue('TITL', $mediaobject->getFullName());
			}
			// Show file details
			if ($mediaobject->isExternal()) {
				echo GedcomTag::getLabelValue('URL', $mediaobject->getFilename());
			} else {
				if ($mediaobject->fileExists()) {
					if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
						echo GedcomTag::getLabelValue('FILE', $mediaobject->getFilename());
					}
					echo GedcomTag::getLabelValue('FORM', $mediaobject->mimeType());
					echo GedcomTag::getLabelValue('__FILE_SIZE__', $mediaobject->getFilesize());
					$imgsize = $mediaobject->getImageAttributes();
					if ($imgsize['WxH']) {
						echo GedcomTag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
					}
				} else {
					echo '<p class="ui-state-error">', /* I18N: %s is a filename */ I18N::translate('The file “%s” does not exist.', $mediaobject->getFilename()), '</p>';
				}
			}
			echo '<br>';
			echo '<div>';
			echo print_fact_sources($mediaobject->getGedcom(), 1);
			echo print_fact_notes($mediaobject->getGedcom(), 1);
			echo '</div>';
			foreach ($mediaobject->linkedIndividuals('OBJE') as $individual) {
				echo '<a href="' . $individual->getHtmlUrl() . '">' . I18N::translate('View individual') . ' — ' . $individual->getFullname() . '</a><br>';
			}
			foreach ($mediaobject->linkedFamilies('OBJE') as $family) {
				echo '<a href="' . $family->getHtmlUrl() . '">' . I18N::translate('View family') . ' — ' . $family->getFullname() . '</a><br>';
			}
			foreach ($mediaobject->linkedSources('OBJE') as $source) {
				echo '<a href="' . $source->getHtmlUrl() . '">' . I18N::translate('View source') . ' — ' . $source->getFullname() . '</a><br>';
			}
			echo '</td></tr></table>';
			echo '</td>';
			if ((++$n) % $columns == 0 && $n < $count) {
				echo '</tr><tr>';
			}
		} // end media loop

		// An odd number of media objects in two columns requires an empty cell
		if ($columns == 2 && $n % 2 == 1) {
			echo '<td></td>';
		}

		echo '</tr></tbody>';
		echo '</table>';
	}
	echo '</div>';
}
echo '</div>';

