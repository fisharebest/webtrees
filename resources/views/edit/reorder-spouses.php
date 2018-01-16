<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2><?= $title ?></h2>

<form name="reorder_form" method="post">
	<?= csrf_field() ?>
	<div class="wt-sortable-list">
		<?php foreach ($individual->getFacts('FAMS') as $fact): ?>
			<div class="card mb-2 wt-sortable-item" data-sortbydate="<?= $fact->getTarget()->getMarriageDate()->julianDay() ?>">
				<input type="hidden" name="order[]" value="<?= $fact->getFactId() ?>">
				<h3 class="card-header">
					<?= FontAwesome::semanticIcon('drag-handle', '') ?>
					<?= $fact->getTarget()->getFullName() ?>
				</h3>
				<div class="card-body">
					<?= $fact->getTarget()->formatFirstMajorFact(WT_EVENTS_MARR, 2) ?>
					<?= $fact->getTarget()->formatFirstMajorFact(WT_EVENTS_DIV, 2) ?>
				</div>
			</div>
		<?php endforeach ?>
	</div>

	<p>
		<button class="btn btn-primary" type="submit">
			<?= FontAwesome::decorativeIcon('save') ?>
			<?= /* I18N: A button label. */
			I18N::translate('save') ?>
		</button>
		<button class="btn btn-secondary" id="btn-default-order" type="button">
			<?= FontAwesome::decorativeIcon('sort') ?>
			<?= /* I18N: A button label. */ I18N::translate('sort by date of marriage') ?>
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

  $("#btn-default-order").on("click", function() {
    $(".wt-sortable-list .wt-sortable-item").sort(function(x, y) {
      return Math.sign(x.dataset.sortbydate - y.dataset.sortbydate);
    }).appendTo(".wt-sortable-list");
  });
</script>
<?php View::endpush() ?>
