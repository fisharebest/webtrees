<?php use Fisharebest\Webtrees\Functions\FunctionsPrintFacts; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="wt-media-tab py-4">
	<table class="table wt-facts-table">
		<?php foreach ($facts as $fact): ?>
			<?php FunctionsPrintFacts::printMainMedia($fact, 1) ?>
			<?php FunctionsPrintFacts::printMainMedia($fact, 2) ?>
			<?php FunctionsPrintFacts::printMainMedia($fact, 3) ?>
			<?php FunctionsPrintFacts::printMainMedia($fact, 4) ?>
		<?php endforeach ?>

		<?php if (empty($facts)): ?>
			<tr>
				<td colspan="2">
					<?= I18N::translate('There are no media objects for this individual.') ?>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>
