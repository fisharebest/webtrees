<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\View; ?>

<table class="table table-sm datatable wt-table-events" data-info="false" data-paging="false" data-searching="false" data-sorting="<?= e('[[1, "asc" ]]') ?>">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Record') ?>
			</th>
			<th>
				<?= I18N::translate('Date') ?>
			</th>
			<th>
				<i class="icon-reminder"></i>
				<span class="sr-only"><?= I18N::translate('Anniversary') ?></span>
			</th>
			<th>
				<?= I18N::translate('Event') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($facts as $fact): ?>
			<?php $record = $fact->getParent(); ?>
			<tr>
				<td data-sort="<?= e($record->getSortName()) ?>">
					<a href="<?= e($record->url()) ?>">
						<?= $record->getFullName() ?>
					</a>
					<?php if ($record instanceof Individual): ?>
						<?= $record->getSexImage() ?>
					<?php endif ?>
				</td>
				<td data-sort="<?= $fact->getDate()->julianDay() ?>">
					<?= $fact->getDate()->display(true); ?>
				</td>
				<td data-sort="<?= $fact->anniv ?>">
					<?= $fact->anniv ?>
				</td>
				<td data-sort="<?= $fact->getLabel() ?>">
					<?= $fact->getLabel() ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php View::push('javascript') ?>
<script>
  $(".wt-table-events").dataTable();
</script>
<?php View::endpush() ?>
