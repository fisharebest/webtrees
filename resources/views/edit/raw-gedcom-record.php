<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-content" method="post">
	<?= csrf_field() ?>
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<input type="hidden" name="xref" value="<?= e($record->getXref()) ?>">

	<p class="text-muted small">
		<?= I18N::translate('This page allows you to bypass the usual forms, and edit the underlying data directly. It is an advanced option, and you should not use it unless you understand the GEDCOM format. If you make a mistake here, it can be difficult to fix.') ?>
	</p>
	<p class="text-muted small">
		<?= /* I18N: %s is a URL */ I18N::translate('You can download a copy of the GEDCOM specification from %s.', '<a href="https://wiki.webtrees.net/w/images-en/Ged551-5.pdf">https://wiki.webtrees.net/w/images-en/Ged551-5.pdf</a>') ?>
	</p>

	<div class="card">
		<label class="card-header py-1 px-2" for="fact0">
			<?= $record->getFullName() ?>
		</label>
		<div class="card-body form-control py-1 px-2">
			<textarea class="card-body form-control py-1 px-2" dir="ltr" id="fact0" rows="1">0 @<?= e($record->getXref()) ?>@ <?= e($record::RECORD_TYPE) ?></textarea>
		</div>
	</div>

	<div id="raw-gedcom-list">
		<?php foreach ($record->getFacts() as $fact): ?>
			<?php if (!$fact->isPendingDeletion()): ?>
				<div class="card my-2">
					<label class="card-header py-1 px-2 d-flex" for="fact-<?= e($fact->getFactId()) ?>">
						<i class="fas fa-sort fa-fw drag-handle" style="cursor:move;"></i>
						<?= $fact->summary() ?>
					</label>
					<input type="hidden" name="fact_id[]" value="<?= e($fact->getFactId()) ?>">
					<textarea class="card-body form-control py-1 px-2" dir="ltr" id="fact-<?= e($fact->getFactId()) ?>" name="fact[]" pattern="<?= e($pattern) ?>" rows="<?= 1 + preg_match_all('/\n/', $fact->getGedcom()) ?>"><?= e($fact->getGedcom()) ?></textarea>
				</div>
			<?php endif ?>
		<?php endforeach ?>

		<div class="card my-2">
			<label class="card-header py-1 px-2" for="fact-add">
				<?= I18N::translate('Add a fact') ?>
			</label>
			<input type="hidden" name="fact_id[]" value="<?= e($fact->getFactId()) ?>">
			<textarea class="card-body form-control py-1 px-2" dir="ltr" id="fact-add" name="fact[]" pattern="<?= e($pattern) ?>" rows="2"></textarea>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */ I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($record->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
<script>
  new Sortable(document.getElementById("raw-gedcom-list"), {
    handle: '.drag-handle'
  });
</script>
<?php View::endpush() ?>
