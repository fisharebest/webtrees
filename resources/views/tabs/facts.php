<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-facts-tab py-4">
	<table class="table wt-facts-table">
		<tbody>
			<tr>
				<td colspan="2">
					<label>
						<input id="show-relatives-facts" type="checkbox" data-toggle="collapse" data-target=".wt-relation-fact">
						<?= I18N::translate('Events of close relatives') ?>
					</label>
					<?php if ($has_historical_facts): ?>
						<label>
							<input id="show-historical-facts" type="checkbox" data-toggle="collapse" data-target=".wt-historic-fact">
							<?= I18N::translate('Historical facts') ?>
						</label>
					<?php endif ?>
				</td>
			</tr>

			<?php foreach ($facts as $fact): ?>
				<?php FunctionsPrintFacts::printFact($fact, $individual) ?>
			<?php endforeach ?>

			<?php if (empty($facts)): ?>
				<tr>
					<td colspan="2">
						<?= I18N::translate('There are no facts for this individual.') ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($individual->canEdit()): ?>
				<?php FunctionsPrint::printAddNewFact($individual->getXref(), $facts, 'INDI') ?>
			<?php endif ?>
		</tbody>
	</table>
</div>

<?php View::push('javascript') ?>
<script>
  'use strict';

  persistent_toggle("show-relatives-facts", "tr.rela");
  persistent_toggle("show-historical-facts", "tr.histo");
</script>
<?php View::endpush() ?>
