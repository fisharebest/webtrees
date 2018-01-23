<?php use Fisharebest\Webtrees\I18N; ?>

<table class="table table-bordered datatables dt-responsive wt-table-tasks" data-columns="[null, null, null, null]" data-info="false" data-paging="false" data-searching="false" data-state-save="true">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Date') ?>
			</th>
			<th>
				<?= I18N::translate('Record') ?>
			</th>
			<th>
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
				<td data-sort="<?= $task->getDate()->julianDay() ?>">
					<?= $task->getDate()->display() ?>
				</td>
				<td data-sort="<?= e($task->getParent()->getSortName()) ?>">
					<a href="<?= e($task->getParent()->url()) ?>">
						<?= $task->getParent()->getFullName() ?>
					</a>
				</td>
				<td>
					<?= e($task->getAttribute('_WT_USER')) ?>
				</td>
				<td dir="auto">
					<?= e($task->getValue()) ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
