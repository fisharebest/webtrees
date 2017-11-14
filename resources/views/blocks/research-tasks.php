<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<table class="table table-bordered table-sm datatables table-research-task table-responsive" data-columns="[null, null, null, null]" data-info="false" data-paging="false" data-searching="false" data-state-save="true">
	<thead>
		<tr>
			<th class="d-none d-md-table-cell wt-side-block-optional">
				<?= I18N::translate('Date') ?>
			</th>
			<th>
				<?= I18N::translate('Record') ?>
			</th>
			<th class="d-none d-md-table-cell wt-side-block-optional">
				<?= I18N::translate('Username') ?>
			</th>
			<th>
				<?= I18N::translate('Research task') ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($tasks as $task): ?>
			<tr>
				<td data-sort="<?= $task->getDate()->julianDay() ?>" class="d-none d-md-table-cell wt-side-block-optional">
					<?= $task->getDate()->display() ?>
				</td>
				<td data-sort="<?= Html::escape($task->getParent()->getSortName()) ?>">
					<a href="<?= $task->getParent()->getHtmlUrl() ?>">
						<?= $task->getParent()->getFullName() ?>
					</a>
				</td>
				<td class="d-none d-md-table-cell wt-side-block-optional">
					<?= Html::escape($task->getAttribute('_WT_USER')) ?>
				</td>
				<td dir="auto">
					<?= Html::escape($task->getValue()) ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
