<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\GedcomCode\GedcomCodeName; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-content" method="post">
	<?= csrf_field() ?>
	<div class="wt-sortable-list">
		<?php foreach ($individual->getFacts('NAME') as $fact): ?>
			<div class="card mb-2 wt-sortable-item">
				<input type="hidden" name="order[]" value="<?= $fact->getFactId() ?>">
				<h3 class="card-header">
					<?= FontAwesome::semanticIcon('drag-handle', '') ?>
					<?= $fact->getValue() ?>
				</h3>
				<div class="card-body">
					<?= GedcomTag::getLabelValue('TYPE', GedcomCodeName::getValue($fact->getAttribute('TYPE'), $fact->getParent())) ?>
				</div>
			</div>
		<?php endforeach ?>
	</div>

	<p>
		<button class="btn btn-primary" type="submit">
			<?= FontAwesome::decorativeIcon('save') ?>
			<?= /* I18N: A button label. */ I18N::translate('save') ?>
		</button>

		<a class="btn btn-secondary" href="<?= e($individual->url()) ?>">
			<?= FontAwesome::decorativeIcon('cancel') ?>
			<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
		</a>
	</p>
</form>

<?php View::push('javascript') ?>
<script>
  new Sortable(document.querySelector(".wt-sortable-list"), {});
</script>
<?php View::endpush() ?>
