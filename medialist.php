<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree   $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\MediaController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Query\QueryMedia;

define('WT_SCRIPT_NAME', 'medialist.php');
require './includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate('Media objects'))
	->pageHeader();

$action = Filter::get('action');
$sortby = Filter::get('sortby', 'file|title', 'title');
if (!Auth::isEditor($WT_TREE)) {
	$sortby = 'title';
}
$page           = Filter::getInteger('page');
$max            = Filter::get('max', '10|20|30|40|50|75|100|125|150|200', '20');
$folder         = Filter::get('folder', null, ''); // MySQL needs an empty string, not NULL
$filter         = Filter::get('filter', null, ''); // MySQL needs an empty string, not NULL
$columns        = Filter::getInteger('columns', 1, 2, 2);
$subdirs        = Filter::get('subdirs', 'on');
$form_type      = Filter::get('form_type', implode('|', array_keys(GedcomTag::getFileFormTypes())));
$currentdironly = ($subdirs === 'on') ? false : true;

// reset all variables
if ($action === 'reset') {
	$sortby         = 'title';
	$max            = '20';
	$folder         = '';
	$columns        = '2';
	$currentdironly = true;
	$filter         = '';
	$form_type      = '';
}

// A list of all subfolders used by this tree
$folders = QueryMedia::folderList();

// A list of all media objects matching the search criteria
$medialist = QueryMedia::mediaList(
	$folder,
	$currentdironly ? 'exclude' : 'include',
	$sortby,
	$filter,
	$form_type
);

?>
<div id="medialist-page">

<h2><?php echo $controller->getPageTitle(); ?></h2>

<form action="medialist.php" method="get">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml() ?>">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="search" value="yes">
	<table class="list_table">
		<tbody>
			<tr>
				<td class="descriptionbox wrap">
					<label for="folder">
						<?php echo I18N::translate('Folder'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<?php echo FunctionsEdit::selectEditControl('folder', $folders, null, $folder); ?>
				</td>
				<?php if (Auth::isEditor($WT_TREE)): ?>
				<td class="descriptionbox wrap">
					<label for="sortby">
						<?php echo I18N::translate('Sort order'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<select name="sortby" id="sortby">
						<option value="title" <?php echo $sortby === 'title' ? 'selected' : ''; ?>>
							<?php echo /* I18N: An option in a list-box */ I18N::translate('sort by title'); ?>
						</option>
						<option value="file" <?php echo $sortby === 'file' ? 'selected' : ''; ?>>
							<?php echo /* I18N: An option in a list-box */ I18N::translate('sort by filename'); ?>
						</option>
					</select>
				</td>
				<?php else: ?>
					<td class="descriptionbox wrap"></td>
					<td class="optionbox wrap"></td>
				<?php endif; ?>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<label for="subdirs">
						<?php echo /* I18N: Label for check-box */ I18N::translate('Include subfolders'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<input type="checkbox" id="subdirs" name="subdirs" <?php echo $currentdironly ? '' : 'checked'; ?>>
				</td>
				<td class="descriptionbox wrap">
					<label for="max">
						<?php echo I18N::translate('Media objects per page'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<select name="max" id="max">
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
					<label for="form-type">
						<?php echo I18N::translate('Type'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<select name="form_type" id="form-type">
						<option value=""></option>
						<?php foreach (GedcomTag::getFileFormTypes() as $value => $label): ?>
							<option value="<?php echo $value; ?>" <?php echo $value === $form_type ? 'selected' : ''; ?>>
								<?php echo $label; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
				<td class="descriptionbox wrap">
					<label for="columns">
						<?php echo I18N::translate('Columns per page'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<select name="columns" id="columns">
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
					<label for="filter">
						<?php echo I18N::translate('Search filters'); ?>
					</label>
				</td>
				<td class="optionbox wrap">
					<input type="text" id="filter" name="filter" value="<?php echo Filter::escapeHtml($filter); ?>" size="14" dir="auto">
				</td>
				<td class="descriptionbox wrap"></td>
				<td class="optionbox wrap">
					<button type="submit" name="action" value="submit">
						<?php echo I18N::translate('Search'); ?>
					</button>
					<button type="submit" name="action" value="reset">
						<?php echo I18N::translate('reset'); ?>
					</button>
				</td>
			</tr>
		</tbody>
	</table>
</form>

<?php
if ($action === 'submit') {
	$url = 'medialist.php?action=submit' .
		'&amp;ged=' . $WT_TREE->getNameHtml() .
		'&amp;folder=' . Filter::escapeUrl($folder) .
		'&amp;sortby=' . Filter::escapeUrl($sortby) .
		'&amp;subdirs=' . Filter::escapeUrl($subdirs) .
		'&amp;filter=' . Filter::escapeUrl($filter) .
		'&amp;form_type=' . Filter::escapeUrl($form_type) .
		'&amp;columns=' . Filter::escapeUrl($columns) .
		'&amp;max=' . Filter::escapeUrl($max);

	$count = count($medialist);
	$pages = (int) (($count + $max - 1) / $max);
	$page  = max(min($page, $pages), 1);

	if ($page === $pages && $count % $max !== 0) {
		// Last page may have    fewer than $max pages
		$number_on_page = $count % $max;
	} else {
		$number_on_page = $max;
	}

	if (I18N::direction() === 'ltr') {
		$icons = array('first' => 'ldarrow', 'previous' => 'larrow', 'next' => 'rarrow', 'last' => 'rdarrow');
	} else {
		$icons = array('first' => 'rdarrow', 'previous' => 'rarrow', 'next' => 'larrow', 'last' => 'ldarrow');
	}

	echo '<div><p>', I18N::translate('Media objects found'), ' ', $count, '</p>';

	if ($count > 0) {
		echo '<table class="list_table">';
		// Display controls twice - at the top and bottom of the table
		foreach (array('thead', 'tfoot') as $tsection) {
			echo '<', $tsection, '><tr><td colspan="2">';
			echo '<table class="list_table_controls"><tr><td>';
			if ($page > 1) {
				echo '<a href="', $url, '&amp;page=1" class="icon-', $icons['first'], '"></a>';
				echo '<a href="', $url, '&amp;page=', $page - 1, '" class="icon-', $icons['previous'], '"></a>';
			}
			echo '</td><td>', I18N::translate('Page %s of %s', $page, $pages), '</td><td>';
			if ($page < $pages) {
				echo '<a href="', $url, '&amp;page=', $page + 1, '" class="icon-', $icons['next'], '"></a>';
				echo '<a href="', $url, '&amp;page=', $pages, '" class="icon-', $icons['last'], '"></a>';
			}
			echo '</td></tr></table>';
			echo '</td></tr></', $tsection, '>';
		}

		echo '<tbody><tr>';
		for ($i = 0, $n = 0; $i < $number_on_page; ++$i) {
			$mediaobject = $medialist[($page - 1) * $max + $i];

			if ($columns === 1) {
				echo '<td class="media-col1 list_value_wrap">';
			}
			if ($columns === 2) {
				echo '<td class="media-col2 list_value_wrap">';
			}

			echo '<table><tr><td class="media-image">';
			echo $mediaobject->displayImage();
			echo '</td><td class="media-col list_value_wrap">';
			if (Auth::isEditor($WT_TREE)) {
				echo MediaController::getMediaListMenu($mediaobject);
			}
			// If sorting by title, highlight the title. If sorting by filename, highlight the filename
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
					if (Auth::isEditor($WT_TREE)) {
						echo GedcomTag::getLabelValue('FILE', $mediaobject->getFilename());
						$mediatype = $mediaobject->getMediaType();
						if ($mediatype) {
						echo GedcomTag::getLabelValue('TYPE', GedcomTag::getFileFormTypeValue($mediatype));
						}
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
			echo FunctionsPrintFacts::printFactSources($mediaobject->getGedcom(), 1);
			echo FunctionsPrint::printFactNotes($mediaobject->getGedcom(), 1);
			echo '</div>';
			foreach ($mediaobject->linkedIndividuals('OBJE') as $individual) {
				echo '<a href="' . $individual->getHtmlUrl() . '">' . I18N::translate('View the individual') . ' — ' . $individual->getFullName() . '</a><br>';
			}
			foreach ($mediaobject->linkedFamilies('OBJE') as $family) {
				echo '<a href="' . $family->getHtmlUrl() . '">' . I18N::translate('View the family') . ' — ' . $family->getFullName() . '</a><br>';
			}
			foreach ($mediaobject->linkedSources('OBJE') as $source) {
				echo '<a href="' . $source->getHtmlUrl() . '">' . I18N::translate('View the source') . ' — ' . $source->getFullName() . '</a><br>';
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
