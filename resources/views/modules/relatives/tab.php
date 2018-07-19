<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-sources-tab py-4">
	<table class="table table-sm wt-facts-table" role="presentation">
	<tbody>
		<tr>
			<td>
				<label>
					<input id="show-date-differences" type="checkbox" checked>
					<?= I18N::translate('Date differences') ?>
				</label>
			</td>
		</tr>
	</tbody>
</table>

<?php if (empty($parent_families) && $can_edit): ?>
	<table class="table table-sm wt-facts-table" role="presentation">
		<tbody>
			<tr>
				<td>
					<a href="<?= e(route('add-parent-to-individual', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'gender' => 'M'])) ?>">
						<?= I18N::translate('Add a father') ?>
					</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href="<?= e(route('add-parent-to-individual', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'gender' => 'F'])) ?>">
						<?= I18N::translate('Add a mother') ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
<?php endif ?>

<!-- Parents -->
<?php foreach ($parent_families as $family): ?>
	<?= view('modules/relatives/family', [
		'individual'       => $individual,
		'family'           => $family,
		'type'             => 'FAMC',
		'label'            => $individual->getChildFamilyLabel($family),
		'fam_access_level' => $fam_access_level,
	]) ?>
<?php endforeach ?>

<!-- step-parents -->
<?php foreach ($step_parent_families as $family): ?>
	<?= view('modules/relatives/family', [
		'individual'       => $individual,
		'family'           => $family,
		'type'             => 'FAMC',
		'label'            => $individual->getStepFamilyLabel($family),
		'fam_access_level' => $fam_access_level,
	]) ?>
<?php endforeach ?>

<!-- spouses -->
<?php foreach ($spouse_families as $family): ?>
	<?= view('modules/relatives/family', [
		'individual'       => $individual,
		'family'           => $family,
		'type'             => 'FAMS',
		'label'            => $individual->getSpouseFamilyLabel($family),
		'fam_access_level' => $fam_access_level,
	]) ?>
<?php endforeach ?>

<!-- step-children -->
<?php foreach ($step_child_familiess as $family): ?>
	<?= view('modules/relatives/family', [
		'individual'       => $individual,
		'family'           => $family,
		'type'             => 'FAMS',
		'label'            => $family->getFullName(),
		'fam_access_level' => $fam_access_level,
	]) ?>
<?php endforeach ?>

<?php if ($can_edit): ?>
	<br>
	<table class="table table-sm wt-facts-table" role="presentation">
		<tbody>
			<?php if (count($spouse_families) > 1): ?>
				<tr>
					<td>
						<a href="<?= e(route('reorder-spouses', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
							<?= I18N::translate('Re-order families') ?>
						</a>
					</td>
				</tr>
			<?php endif ?>
			<tr>
				<td>
					<a href="<?= e(route('link-child-to-family', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
						<?= I18N::translate('Link this individual to an existing family as a child') ?>
					</a>
				</td>
			</tr>

			<tr>
				<td>
					<a href="<?= e(route('add-spouse-to-individual', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
						<?php if ($individual->getSex() !== 'F'): ?>
							<?= I18N::translate('Add a wife') ?>
						<?php else: ?>
							<?= I18N::translate('Add a husband') ?>
						<?php endif ?>
					</a>
				</td>
			</tr>

			<tr>
				<td>
					<a href="<?= e(route('link-spouse-to-individual', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
						<?php if ($individual->getSex() !== 'F'): ?>
							<?= I18N::translate('Add a wife using an existing individual') ?>
						<?php else: ?>
							<?= I18N::translate('Add a husband using an existing individual') ?>
						<?php endif ?>
					</a>
				</td>
			</tr>

			<tr>
				<td>
					<a href="<?= e(route('add-child-to-individual', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'gender' => 'U'])) ?>">
						<?= I18N::translate('Add a child to create a one-parent family') ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
<?php endif ?>
</div>

<?php View::push('javascript') ?>
<script>
    'use strict';

    persistent_toggle("show-date-differences", ".elderdate");
</script>
<?php View::endpush() ?>
