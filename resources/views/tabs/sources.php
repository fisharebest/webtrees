<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-sources-tab py-4">
	<table class="table wt-facts-table">
		<tr>
			<td colspan="2">
				<label>
					<input id="show-level-2-sources" type="checkbox">
					<?= I18N::translate('Show all sources') ?>
				</label>
			</td>
		</tr>

		<?php foreach ($facts as $fact): ?>
			<?php FunctionsPrintFacts::printMainSources($fact, 1) ?>
			<?php FunctionsPrintFacts::printMainSources($fact, 2) ?>
		<?php endforeach ?>

		<?php if (empty($facts)): ?>
			<tr>
				<td colspan="2">
					<?= I18N::translate('There are no source citations for this individual.') ?>
					</td>
			</tr>
		<?php endif ?>

		<?php if ($can_edit): ?>
			<tr>
				<th scope="row">
					<?= I18N::translate('Source') ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref(), 'fact' => 'SOUR'])) ?>">
						<?= I18N::translate('Add a source citation') ?>
					</a>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>

<?php View::push('javascript') ?>
<script>
  'use strict';

  persistent_toggle("show-level-2-sources", ".row_sour2");
</script>
<?php View::endpush() ?>
