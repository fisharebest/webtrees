<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-options wt-page-options-clippings-download hidden-print">
	<input type="hidden" name="route" value="module">
	<input type="hidden" name="module" value="clippings">
	<input type="hidden" name="action" value="Download">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<?php if ($is_manager): ?>
		<div class="row form-group">
			<div class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Apply privacy settings') ?>
			</div>
			<div class="col-sm-9 wt-page-options-value">
				<input type="radio" name="privatize_export" value="none" checked>
				<?= I18N::translate('None') ?>
				<br>
				<input type="radio" name="privatize_export" value="gedadmin">
				<?= I18N::translate('Manager') ?>
				<br>
				<input type="radio" name="privatize_export" value="user">
				<?= I18N::translate('Member') ?>
				<br>
				<input type="radio" name="privatize_export" value="visitor">
				<?= I18N::translate('Visitor') ?>
			</div>
		</div>
	<?php elseif ($is_member): ?>
		<div class="row form-group">
			<div class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Apply privacy settings') ?>
			</div>
			<div class="col-sm-9 wt-page-options-value">
				<input type="radio" name="privatize_export" value="user">
				<?= I18N::translate('Member') ?>
				<br>
				<input type="radio" name="privatize_export" value="visitor">
				<?= I18N::translate('Visitor') ?>
			</div>
		</div>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="convert">
			<?= I18N::translate('Convert from UTF-8 to ISO-8859-1') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input type="checkbox" name="convert" id="convert">
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-3 col-form-label wt-page-options-label">
		</div>
		<div class="col-sm-9 wt-page-options-value">
			<button type="submit" class="btn btn-primary">
				<?= FontAwesome::decorativeIcon('download') ?>
				<?= I18N::translate('download') ?>
			</button>
			<a href="<?= e(route('module', ['module' => 'clippings', 'action' => 'Show', 'ged' => $tree->getName()])) ?>" class="btn btn-secondary">
				<?= FontAwesome::decorativeIcon('cancel') ?>
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>
</form>
