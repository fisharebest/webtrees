<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-media-list d-print-none">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<input type="hidden" name="route" value="media-list">
	<input type="hidden" name="action" value="1">
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
			<?= Bootstrap4::select(['' => ''] + $formats, $form_type, ['id' => 'form-type', 'name' => 'form_type']) ?>
		</div>

		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-3 wt-page-options-value">
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="filter">
			<?= I18N::translate('Search filters') ?>
		</label>
		<div class="col-sm-3 wt-page-options-value">
			<input type="text" class="form-control" name="filter" id="filter" value="<?= e($filter) ?>">
		</div>

		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-3 wt-page-options-value">
			<button type="submit" name="action" value="1" class="btn btn-primary">
				<?= /* I18N: A button label. */ I18N::translate('search') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e(route('media-list', ['ged' => $tree->getName()])) ?>">
				<?= /* I18N: A button label. */ I18N::translate('reset') ?>
			</a>
		</div>
	</div>
</form>

<div class="wt-page-content">
	<?php if (!empty($media_objects)): ?>
		<p class="text-center mt-4"><?= I18N::translate('Media objects found') ?> <?= I18N::number($count) ?></p>

		<div class="row text-center">
			<div class="col">
				<?php if ($page > 1): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => 1])) ?>">
						<?= I18N::translate('first') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?php if ($page > 1): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $page - 1])) ?>">
						<?= I18N::translate('previous') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?= I18N::translate('Page %s of %s', $page, $pages) ?>
			</div>
			<div class="col">
				<?php if ($page < $pages): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $page + 1])) ?>">
						<?= I18N::translate('next') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?php if ($page < $pages): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $pages])) ?>">
						<?= I18N::translate('last') ?>
					</a>
				<?php endif ?>
			</div>
		</div>

		<div class="card-deck row mb-4 mt-4">
			<?php foreach ($media_objects as $n => $media_object): ?>
				<div class="col-xs-12 col-sm-6 col-lg-4 d-flex">
					<div class="card mb-4">
						<div class="card-header">
							<h4 class="card-title">
								<a href="<?= e($media_object->url()) ?>"><?= $media_object->getFullName() ?></a>
							</h4>
						</div>
						<div class="card-body">
							<?php foreach ($media_object->mediaFiles() as $media_file): ?>
								<?= $media_file->displayImage(300, 200, 'contain', ['class' => 'img-fluid']) ?>
							<?php endforeach ?>

							<p class="card-text">
								<?php
								// Show file details
								$mediatype = $media_file->type();
								if ($mediatype) {
									echo GedcomTag::getLabelValue('TYPE', GedcomTag::getFileFormTypeValue($mediatype));
								}
								echo FunctionsPrintFacts::printFactSources($media_object->getGedcom(), 1);
								echo FunctionsPrint::printFactNotes($media_object->getGedcom(), 1);
								if ($media_file->isExternal()) {
									echo GedcomTag::getLabelValue('URL', $media_file->filename());
								} elseif ($media_file->fileExists()) {
									?>
								<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#details-<?= e($n) ?>" aria-expanded="false" aria-controls="details-<?= e($n) ?>">
									<?= I18N::translate('Media file') ?>
								</button>
							<div class="collapse" id="details-<?= e($n) ?>">
								<?php
								if (Auth::isEditor($tree)) {
									echo GedcomTag::getLabelValue('FILE', $media_file->filename());
								}
								echo GedcomTag::getLabelValue('FORM', $media_file->mimeType());
								echo GedcomTag::getLabelValue('__FILE_SIZE__', $media_file->fileSizeKB());
								$imgsize = $media_file->getImageAttributes();
								if ($imgsize['WxH']) {
									echo GedcomTag::getLabelValue('__IMAGE_SIZE__', $imgsize['WxH']);
								}
								?>
							</div>
						<?php
								} else {
									echo '<p class="alert alert-danger">', /* I18N: %s is a filename */ I18N::translate('The file “%s” does not exist.', $media_file->filename()), '</p>';
								}
								?>
							</p>
						</div>
						<div class="card-footer">
							<?php
							foreach ($media_object->linkedIndividuals('OBJE') as $individual) {
								echo '<a href="' . e($individual->url()) . '">' . FontAwesome::semanticIcon('individual', I18N::translate('Individual')) . ' ' . $individual->getFullName() . '</a><br>';
							}
							foreach ($media_object->linkedFamilies('OBJE') as $family) {
								echo '<a href="' . e($family->url()) . '">' . FontAwesome::semanticicon('family', I18N::translate('Family')) . ' ' . $family->getFullName() . '</a><br>';
							}
							foreach ($media_object->linkedSources('OBJE') as $source) {
								echo '<a href="' . e($source->url()) . '">' . FontAwesome::semanticIcon('source', I18N::translate('Source')) . ' ' . $source->getFullName() . '</a><br>';
							} ?>
						</div>
					</div>
				</div>
			<?php endforeach ?>
		</div>

		<div class="row text-center">
			<div class="col">
				<?php if ($page > 1): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => 1])) ?>">
						<?= I18N::translate('first') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?php if ($page > 1): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $page - 1])) ?>">
						<?= I18N::translate('previous') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?= I18N::translate('Page %s of %s', $page, $pages) ?>
			</div>
			<div class="col">
				<?php if ($page < $pages): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $page + 1])) ?>">
						<?= I18N::translate('next') ?>
					</a>
				<?php endif ?>
			</div>
			<div class="col">
				<?php if ($page < $pages): ?>
					<a href="<?= e(route('media-list', ['ged' => $tree->getName(), 'action' => '1', 'folder' => $folder, 'subdirs' => $subdirs, 'filter' => $filter, 'form_type' => $form_type, 'max' => $max, 'page' => $pages])) ?>">
						<?= I18N::translate('last') ?>
					</a>
				<?php endif ?>
			</div>
		</div>
	<?php endif ?>
</div>
