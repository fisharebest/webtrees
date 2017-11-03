<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\Query\QueryMedia;

/**
 * @global Tree   $WT_TREE
 */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;
$controller
	->setPageTitle(I18N::translate('Media objects'))
	->pageHeader();

$action    = Filter::get('action');
$page      = Filter::getInteger('page');
$max       = Filter::get('max', '10|20|30|40|50|75|100|125|150|200', '20');
$folder    = Filter::get('folder', null, ''); // MySQL needs an empty string, not NULL
$filter    = Filter::get('filter', null, ''); // MySQL needs an empty string, not NULL
$subdirs   = Filter::get('subdirs', '1');
$details   = Filter::get('details', '1');
$form_type = Filter::get('form_type', implode('|', array_keys(GedcomTag::getFileFormTypes())));

// reset all variables
if ($action === 'reset') {
	$max       = '20';
	$folder    = '';
	$subdirs   = '';
	$details   = '';
	$filter    = '';
	$form_type = '';
}

// A list of all subfolders used by this tree
$folders = QueryMedia::folderList();

// A list of all media objects matching the search criteria
$medialist = QueryMedia::mediaList(
	$folder,
	$subdirs === '1' ? 'include' : 'exclude',
	'title',
	$filter,
	$form_type
);

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-media-list d-print-none">
	<input type="hidden" name="ged" value="<?= $WT_TREE->getNameHtml() ?>">
	<input type="hidden" name="action" value="filter">
	<input type="hidden" name="search" value="yes">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="folder">
			<?= I18N::translate('Folder') ?>
		</label>
		<div class="col-sm-3 wt-page-options-value">
			<?= Bootstrap4::select($folders, $folder, ['id' => 'folder', 'name' => 'folder']) ?>
			<?= Bootstrap4::checkbox(/* I18N: Label for check-box */ I18N::translate('Include subfolders'), true, ['name' => 'subdirs', 'checked' => ($subdirs === '1')]) ?>
		</div>

		<label class="col-sm-3 col-form-label wt-page-options-label" for="max">
			<?= I18N::translate('Media objects per page') ?>
		</label>
		<div class="col-sm-3 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions([10, 20, 30, 40, 50, 75, 100, 150, 200]), $max, ['id' => 'max', 'name' => 'max']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="form-type">
			<?= I18N::translate('Type') ?>
		</label>
		<div class="col-sm-3 wt-page-options-value">
			<?= Bootstrap4::select(['' => ''] + GedcomTag::getFileFormTypes(), $form_type, ['id' => 'form-type', 'name' => 'form_type']) ?>
		</div>

		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-3 wt-page-options-value">
			<?= Bootstrap4::checkbox(I18N::translate('Details'), true, ['name' => 'details', 'checked' => ($details === '1')]) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="filter">
			<?= I18N::translate('Search filters') ?>
		</label>
		<div class="col-sm-3 wt-page-options-value">
			<input type="text" class="form-control" name="filter" id="filter" value="<?= Html::escape($filter) ?>">
		</div>

		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-3 wt-page-options-value">
			<button type="submit" name="action" value="submit" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('search') ?>
			</button>
			<button type="submit" name="action" value="reset" class="btn btn-default">
				<?= /* I18N: A button label. */ I18N::translate('reset') ?>
			</button>
		</div>
	</div>
</form>

<?php
if ($action === 'submit') {
	$url = 'medialist.php?action=submit' .
		'&amp;ged=' . rawurlencode($WT_TREE->getName()) .
		'&amp;folder=' . rawurlencode($folder) .
		'&amp;subdirs=' . rawurlencode($subdirs) .
		'&amp;filter=' . rawurlencode($filter) .
		'&amp;form_type=' . rawurlencode($form_type) .
		'&amp;max=' . rawurlencode($max);

	$count = count($medialist);
	$pages = (int) (($count + $max - 1) / $max);
	$page  = max(min($page, $pages), 1);

	?>
	<p class="text-center mt-4"><?= I18N::translate('Media objects found') ?> <?= I18N::number($count) ?></p>

	<div class="row text-center">
		<div class="col">
			<?php if ($page > 1): ?>
				<a href="<?= $url ?>&amp;page=1"><?= I18N::translate('first') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?php if ($page > 1): ?>
				<a href="<?= $url ?>&amp;page=<?= $page - 1 ?>"><?= I18N::translate('previous') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?= I18N::translate('Page %s of %s', $page, $pages) ?>
		</div>
		<div class="col">
			<?php if ($page < $pages): ?>
				<a href="<?= $url ?>&amp;page=<?= $page + 1 ?>"><?= I18N::translate('next') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?php if ($page < $pages): ?>
				<a href="<?= $url ?>&amp;page=<?= $pages ?>"><?= I18N::translate('last') ?></a>
			<?php endif ?>
		</div>
	</div>

	<div class="card-deck row mb-4 mt-4">
		<?php foreach (array_slice($medialist, ($page - 1) * $max, $max) as $media): ?>
			<div class="col-xs-12 col-sm-6 col-lg-4 d-flex">
				<div class="card mb-4">
					<div class="card-header">
						<h4 class="card-title">
							<a href="<?= $media->getHtmlUrl() ?>"><?= $media->getFullName() ?></a>
						</h4>
					</div>
					<div class="card-body">
						<?= $media->displayImage(300, 200, 'contain', ['class' => 'img-fluid']) ?>

						<p class="card-text">
							<?php
							// Show file details
							$mediatype = $media->getMediaType();
	if ($mediatype) {
		echo GedcomTag::getLabelValue('TYPE', GedcomTag::getFileFormTypeValue($mediatype));
	}
	if ($media->isExternal()) {
		echo GedcomTag::getLabelValue('URL', $media->getFilename());
	} else {
		if ($media->fileExists()) {
			if ($details === '1') {
				if (Auth::isEditor($WT_TREE)) {
					echo GedcomTag::getLabelValue('FILE', $media->getFilename());
				}
				echo GedcomTag::getLabelValue('FORM', $media->mimeType());
				echo GedcomTag::getLabelValue('__FILE_SIZE__', $media->getFilesize());
				$imgsize = $media->getImageAttributes();
				if ($imgsize['WxH']) {
					echo GedcomTag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
				}
			}
		} else {
			echo '<p class="alert alert-danger">', /* I18N: %s is a filename */ I18N::translate('The file “%s” does not exist.', $media->getFilename()), '</p>';
		}
	}
	echo FunctionsPrintFacts::printFactSources($media->getGedcom(), 1);
	echo FunctionsPrint::printFactNotes($media->getGedcom(), 1); ?>
						</p>
					</div>
					<div class="card-footer">
						<?php
						foreach ($media->linkedIndividuals('OBJE') as $individual) {
							echo '<a href="' . $individual->getHtmlUrl() . '">' . FontAwesome::semanticIcon('individual', I18N::translate('Individual')) . ' ' . $individual->getFullName() . '</a><br>';
						}
	foreach ($media->linkedFamilies('OBJE') as $family) {
		echo '<a href="' . $family->getHtmlUrl() . '">' . FontAwesome::semanticicon('family', I18N::translate('Family')) . ' ' . $family->getFullName() . '</a><br>';
	}
	foreach ($media->linkedSources('OBJE') as $source) {
		echo '<a href="' . $source->getHtmlUrl() . '">' . FontAwesome::semanticIcon('source', I18N::translate('Source')) . ' ' . $source->getFullName() . '</a><br>';
	} ?>
					</div>
				</div>
			</div>
		<?php endforeach ?>
	</div>

	<div class="row text-center">
		<div class="col">
			<?php if ($page > 1): ?>
				<a href="<?= $url ?>&amp;page=1"><?= I18N::translate('first') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?php if ($page > 1): ?>
				<a href="<?= $url ?>&amp;page=<?= $page - 1 ?>"><?= I18N::translate('previous') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?= I18N::translate('Page %s of %s', $page, $pages) ?>
		</div>
		<div class="col">
			<?php if ($page < $pages): ?>
				<a href="<?= $url ?>&amp;page=<?= $page + 1 ?>"><?= I18N::translate('next') ?></a>
			<?php endif ?>
		</div>
		<div class="col">
			<?php if ($page < $pages): ?>
				<a href="<?= $url ?>&amp;page=<?= $pages ?>"><?= I18N::translate('last') ?></a>
			<?php endif ?>
		</div>
	</div>
<?php
}

