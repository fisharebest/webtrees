<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([route('admin-control-panel') => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<form method="post" action="<?= e(route('broadcast')) ?>">
	<?= csrf_field() ?>
	<input type="hidden" name="url" value="<?= e($url) ?>">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="from">
			<?= I18N::translate('From') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="from" type="text" value="<?= e($from->getRealName()) ?>" disabled>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="to">
			<?= I18N::translate('To') ?>
		</label>
		<div class="col-sm-9">
			<input type="hidden" name="to" value="<?= e($to) ?>">
			<input class="form-control" id="to" type="text" value="<?= e(implode(', ', $to_names)) ?>" disabled>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="subject">
			<?= I18N::translate('Subject') ?>
		</label>
		<div class="col-sm-9">
			<input class="form-control" id="subject" type="text" name="subject" value="<?= e($subject) ?>" required>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="body">
			<?= I18N::translate('Message') ?>
		</label>
		<div class="col-sm-9">
			<textarea class="form-control" id="body" rows="5" type="text" name="body" required><?= e($body) ?></textarea>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-3 col-form-label"></div>
		<div class="col-sm-9">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('Send') ?>
			</button>
			<a class="btn btn-link" href="<?= e($url) ?>">
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
