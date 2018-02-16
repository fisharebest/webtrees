<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<table class="table table-sm datatables wt-table-changes" data-filter="false" data-info="false" data-paging="false">
	<thead>
		<tr>
			<th>
				<span class="sr-only">
					<?= I18N::translate('Type') ?>
				</span>
			</th>
			<th>
				<?= I18N::translate('Record') ?>
			</th>
			<th>
				<?= I18N::translate('Last change') ?>
			</th>
			<?php if ($show_user): ?>
				<th>
					<?= I18N::translate('User') ?>
				</th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($records as $record): ?>
			<tr>
				<td data-sort="<?= $record::RECORD_TYPE ?>" class="text-centre">
					<?php if ($record::RECORD_TYPE === 'INDI') : ?>
						<?= FontAwesome::semanticIcon('individual', I18N::translate('Individual')) ?>
					<?php elseif ($record::RECORD_TYPE === 'FAM'): ?>
						<?= FontAwesome::semanticicon('family', I18N::translate('Family')) ?>
					<?php elseif ($record::RECORD_TYPE === 'OBJE'): ?>
						<?= FontAwesome::semanticIcon('media', I18N::translate('Media')) ?>
					<?php elseif ($record::RECORD_TYPE === 'NOTE'): ?>
						<?= FontAwesome::semanticIcon('note', I18N::translate('Note')) ?>
					<?php elseif ($record::RECORD_TYPE === 'SOUR'): ?>
						<?= FontAwesome::semanticIcon('source', I18N::translate('Source')) ?>
					<?php elseif ($record::RECORD_TYPE === 'SUBM'): ?>
						<?= FontAwesome::semanticIcon('submitter', I18N::translate('Submitter')) ?>
					<?php elseif ($record::RECORD_TYPE === 'REPO'): ?>
						<?= FontAwesome::semanticIcon('repository', I18N::translate('Repository')) ?>
					<?php endif ?>
				</td>
				<td data-sort="<?= e($record->getSortName()) ?>">
					<a href="<?= e($record->url()) ?>"><?= $record->getFullName() ?></a>
				</td>
				<td data-sort="<?= $record->lastChangeTimestamp(true) ?>">
					<?= $record->lastChangeTimestamp() ?>
				</td>
				<?php if ($show_user): ?>
					<td>
						<?= e($record->lastChangeUser()) ?>
					</td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php View::push('javascript') ?>
<script>
  $(".wt-table-changes").dataTable();
</script>
<?php View::endpush() ?>
