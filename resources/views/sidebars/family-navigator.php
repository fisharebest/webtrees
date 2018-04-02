<div class="wt-sidebar-content wt-sidebar-family-navigator">
	<!-- parent families -->
	<?php foreach ($individual->getChildFamilies() as $family): ?>
		<?= view('sidebars/family-navigator-family', ['individual' => $individual, 'family' => $family, 'title' => $individual->getChildFamilyLabel($family)]) ?>
	<?php endforeach ?>

	<!-- step parents -->
	<?php foreach ($individual->getChildStepFamilies() as $family): ?>
		<?= view('sidebars/family-navigator-family', ['individual' => $individual, 'family' => $family, 'title' => $individual->getStepFamilyLabel($family)]) ?>
	<?php endforeach ?>

	<!-- spouse and children -->
	<?php foreach ($individual->getSpouseFamilies() as $family): ?>
		<?= view('sidebars/family-navigator-family', ['individual' => $individual, 'family' => $family, 'title' => $individual->getSpouseFamilyLabel($family)]) ?>
	<?php endforeach ?>

	<!-- step children -->
	<?php foreach ($individual->getSpouseStepFamilies() as $family): ?>
		<?= view('sidebars/family-navigator-family', ['individual' => $individual, 'family' => $family, 'title' => $family->getFullName()]) ?>
	<?php endforeach ?>
</div>
