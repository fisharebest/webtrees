<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-content" method="post">
	<?= csrf_field() ?>
	<input type="hidden" name="ged" value="<?= e($fact->getParent()->getTree()->getName()) ?>">
	<input type="hidden" name="xref" value="<?= e($fact->getParent()->getXref()) ?>">
	<input type="hidden" name="fact_id" value="<?= e($fact->getFactId()) ?>">


	<p class="text-muted small">
		<?= I18N::translate('This page allows you to bypass the usual forms, and edit the underlying data directly. It is an advanced option, and you should not use it unless you understand the GEDCOM format. If you make a mistake here, it can be difficult to fix.') ?>
	</p>
	<p class="text-muted small">
		<?= /* I18N: %s is a URL */ I18N::translate('You can download a copy of the GEDCOM specification from %s.', '<a href="https://wiki.webtrees.net/w/images-en/Ged551-5.pdf">https://wiki.webtrees.net/w/images-en/Ged551-5.pdf</a>') ?>
	</p>

	<div class="card my-2">
		<label class="card-header py-1 px-2 d-flex" for="gedcom">
			<?= $fact->summary() ?>
		</label>
		<textarea class="card-body form-control py-1 px-2" dir="ltr" id="gedcom" name="gedcom" pattern="<?= e($pattern) ?>" rows="<?= 5 + preg_match_all('/\n/', $fact->getGedcom()) ?>"><?= e($fact->getGedcom()) ?></textarea>
	</div>

	<div class="row form-group">
		<div class="col-sm-9 offset-sm-3">
			<button class="btn btn-primary" type="submit">
				<?= FontAwesome::decorativeIcon('save') ?>
				<?= /* I18N: A button label. */ I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= e($fact->getParent()->url()) ?>">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>

