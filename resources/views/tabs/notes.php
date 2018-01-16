<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-notes-tab py-4">
	<table class="table wt-facts-table">
		<tr>
			<td colspan="2">
				<label>
					<input id="show-level-2-notes" type="checkbox">
					<?= I18N::translate('Show all notes') ?>
				</label>
			</td>
		</tr>

		<?php foreach ($facts as $fact): ?>
			<?php FunctionsPrintFacts::printMainNotes($fact, 1) ?>
			<?php FunctionsPrintFacts::printMainNotes($fact, 2) ?>
			<?php FunctionsPrintFacts::printMainNotes($fact, 3) ?>
			<?php FunctionsPrintFacts::printMainNotes($fact, 4) ?>
		<?php endforeach ?>

		<?php if (empty($facts)): ?>
			<tr>
				<td colspan="2">
					<?= I18N::translate('There are no notes for this individual.') ?>
				</td>
			</tr>
		<?php endif ?>

		<?php if ($can_edit): ?>
			<tr>
				<th scope="row">
					<?= I18N::translate('Note') ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'fact' => 'NOTE'])) ?>">
						<?= I18N::translate('Add a note') ?>
					</a>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?= I18N::translate('Shared note') ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'fact' => 'SHARED_NOTE'])) ?>">
						<?= I18N::translate('Add a shared note') ?>
					</a>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>

<?php View::push('javascript') ?>
<script>
  'use strict';

  persistent_toggle("show-level-2-notes", ".row_note2");
</script>
<?php View::endpush() ?>
