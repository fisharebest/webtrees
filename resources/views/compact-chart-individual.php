<?php use Fisharebest\Webtrees\Html; ?>

<?php if ($individual === null): ?>
	<div class="h-100 person_boxNN person_box_template">&nbsp;</div>
<?php else: ?>
	<div class="h-100 person_box<?= ['M' => '', 'F' => 'F', 'U' => 'NN'][$individual->getSex()]?> person_box_template">
		<a href="<?= Html::escape($individual->getRawUrl()) ?>">
			<?= $individual->getFullName() ?>
		</a>
		<div class="small">
			<?= $individual->getLifeSpan() ?>
		</div>
	</div>
<?php endif ?>
