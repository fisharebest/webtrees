<?php use Fisharebest\Webtrees\I18N; ?>

<div class="progress" id="progress<?= e($tree->getTreeId()) ?>">
	<div
		class="progress-bar"
		role="progressbar"
		aria-valuenow="<?= $progress * 100 ?>"
		aria-valuemin="0"
		aria-valuemax="100"
		style="width: <?= $progress * 100 ?>%; min-width: 40px;"
	>
		<?= I18N::percentage($progress, 1) ?>
	</div>
</div>

<script>
  $("#import<?= e($tree->getTreeId()) ?>")
	  .load("<?= route('import', ['ged' => $tree->getName()]) ?>", {});
</script>
