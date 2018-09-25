<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Config; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Ramsey\Uuid\Uuid; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-content" action="<?= e(route('update-fact', ['ged' => $tree->getName(), 'xref' => $record->getXref(), 'fact_id' => $edit_fact->getFactId()])) ?>" method="post">	<?= csrf_field() ?>

	<?php FunctionsEdit::createEditForm($edit_fact) ?>

	<?php
	$level1type = $edit_fact->getTag();
	switch ($record::RECORD_TYPE) {
		case 'REPO':
			// REPO:NAME facts may take a NOTE (but the REPO record may not).
			if ($level1type === 'NAME') {
				echo view('cards/add-note', [
					'level' => 2,
					'tree' => $tree,
				]);
				echo view('addSimpleTag($tree, ', [
					'level' => 2,
					'tree' => $tree,
				]);
			}
			break;
		case 'FAM':
		case 'INDI':
			// FAM and INDI records have real facts. They can take NOTE/SOUR/OBJE/etc.
			if ($level1type !== 'SEX' && $level1type !== 'NOTE' && $level1type !== 'ALIA') {
				if ($level1type !== 'SOUR') {
					echo view('cards/add-source-citation', [
						'level'          => 2,
						'full_citations' => $tree->getPreference('FULL_SOURCES'),
						'tree'           => $tree,
					]); }
				if ($level1type !== 'OBJE') {
					if ($tree->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($tree)) {
						echo view('cards/add-media-object', [
							'level' => 2,
							'tree'  => $tree,
						]);
					}
				}
				echo view('cards/add-note', [
					'level' => 2,
					'tree' => $tree,
				]);
				echo view('cards/add-shared-note', [
					'level' => 2,
					'tree' => $tree,
				]);
				if ($level1type !== 'ASSO' && $level1type !== 'NOTE' && $level1type !== 'SOUR') {
					echo view('cards/add-associate', [
						'id'    => Uuid::uuid4()->toString(),
						'level' => 2,
						'tree' => $tree,
					]);
				}
				// allow to add godfather and godmother for CHR fact or best man and bridesmaid  for MARR fact in one window
				if (in_array($level1type, Config::twoAssociates())) {
					echo view('cards/add-associate', [
						'id'    => Uuid::uuid4()->toString(),
						'level' => 2,
						'tree' => $tree,
					]);
				}
				if ($level1type !== 'SOUR') {
					echo view('cards/add-restriction', [
						'level' => 2,
						'tree' => $tree,
					]);
				}
			}
			break;
		default:
			// Other types of record do not have these lower-level records
			break;
	}

	?>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="keep_chan">
			<?= I18N::translate('Last change') ?>
		</label>
		<div class="col-sm-9">
			<?= Bootstrap4::checkbox(I18N::translate('Keep the existing “last change” information'), true, ['name' => 'keep_chan', 'checked' => (bool) $tree->getPreference('NO_UPDATE_CHAN')]) ?>
			<?= GedcomTag::getLabelValue('DATE', $record->lastChangeTimestamp()) ?>
			<?= GedcomTag::getLabelValue('_WT_USER', e($record->lastChangeUser())) ?>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */
				I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($record->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */
				I18N::translate('cancel') ?>
			</a>
			<?php if ($can_edit_raw): ?>
				<a class="btn btn-link" href="<?= e(route('edit-raw-fact', ['xref' => $record->getXref(), 'fact_id' => $edit_fact->getFactId(), 'ged' => $tree->getName()])) ?>">
					<?= I18N::translate('Edit the raw GEDCOM') ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</form>

<?= view('modals/on-screen-keyboard') ?>
<?= view('modals/ajax') ?>
<?= view('edit/initialize-calendar-popup') ?>
