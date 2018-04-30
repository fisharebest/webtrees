<?php use Fisharebest\Webtrees\Functions\FunctionsDate; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Tree; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<?php if (empty($changes)): ?>
	<p>
		<?= I18N::translate('There are no pending changes.') ?>
	</p>
	<p>
		<a class="btn btn-primary" href="<?= e($url) ?>">
			<?= I18N::translate('continue') ?>
		</a>
	</p>
<?php endif ?>

<ul class="nav nav-tabs" role="tablist">
	<?php foreach ($changes as $tree_id => $gedcom_changes): ?>
		<li class="nav-item">
			<a class="nav-link <?= $tree_id === $active_tree_id ? 'active' : '' ?>" data-toggle="tab" href="#tree-<?= e($tree_id) ?>" aria-controls="tree-<?= e($tree_id) ?>" id="tree-<?= e($tree_id) ?>-tab">
				<?= e(Tree::findById($tree_id)->getTitle()) ?>
				<span class="badge badge-secondary">
				<?= I18N::number(count($gedcom_changes)) ?>
			</span>
			</a>
		</li>
	<?php endforeach ?>
</ul>

<div class="tab-content">
	<?php foreach ($changes as $tree_id => $gedcom_changes): ?>
		<div class="tab-pane fade <?= $tree_id === $active_tree_id ? 'show active' : '' ?>" id="tree-<?= e($tree_id) ?>" role="tabpanel" aria-labelledby="tree-<?= e($tree_id) ?>-tab">
			<?php foreach ($gedcom_changes as $xref => $record_changes): ?>
				<h3 class="pt-2">
					<a href="<?= e($record_changes[0]->record->url()) ?>"><?= $record_changes[0]->record->getFullName() ?></a>
				</h3>

				<table class="table table-bordered table-sm">
					<thead class="thead-default">
						<tr>
							<th><?= I18N::translate('Accept') ?></th>
							<th><?= I18N::translate('Changes') ?></th>
							<th><?= I18N::translate('User') ?></th>
							<th><?= I18N::translate('Date') ?></th>
							<th><?= I18N::translate('Reject') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($record_changes as $record_change): ?>
							<tr>
								<td>
									<form action="<?= e(route('accept-pending', ['change_id' => $record_change->change_id, 'xref' => $record_change->xref, 'ged' => $record_change->gedcom_name, 'url' => $url])) ?>" method="POST">
										<?= csrf_field() ?>
										<button class="btn btn-primary" type="submit">
											<?= I18N::translate('Accept') ?>
										</button>
									</form>
								</td>
								<td>
									<?php foreach ($record_change->record->getFacts() as $fact): ?>
										<?php if ($fact->getTag() !== 'CHAN' && $fact->isPendingAddition()): ?>
											<div class="new">
												<?= strip_tags($fact->summary()) ?>
											</div>
										<?php elseif ($fact->getTag() !== 'CHAN' && $fact->isPendingDeletion()): ?>
											<div class="old">
												<?= strip_tags($fact->summary()) ?>
											</div>
										<?php endif ?>
									<?php endforeach ?>
								</td>
								<td>
									<a href="<?= e(route('message', ['to' => $record_change->user_name, 'subject' => I18N::translate('Pending changes') . ' - ' . strip_tags($record_change->record->getFullName()), 'body' => WT_BASE_URL . $record_change->record->url(), 'ged' => $record_change->gedcom_name,])) ?>" title="<?= I18N::translate('Send a message') ?>">
										<?= e($record_change->real_name) ?> - <?= e($record_change->user_name) ?>
									</a>
								</td>
								<td>
									<?= FunctionsDate::formatTimestamp($record_change->change_timestamp) ?>
								</td>
								<td>
									<form action="<?= e(route('reject-pending', ['change_id' => $record_change->change_id, 'xref' => $record_change->xref, 'ged' => $record_change->gedcom_name, 'url' => $url])) ?>" method="POST">
										<?= csrf_field() ?>
										<button class="btn btn-secondary" type="submit">
											<?= I18N::translate('Reject') ?>
										</button>
									</form>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php endforeach ?>

			<div class="d-flex justify-content-between">
				<form action="<?= e(route('accept-all-changes', ['ged' => $tree->getName(), 'url' => $url])) ?>" method="POST">
					<?= csrf_field() ?>
					<button class="btn btn-primary" type="submit">
						<?= I18N::translate('Accept all changes') ?>
					</button>
				</form>

				<form action="<?= e(route('reject-all-changes', ['ged' => $tree->getName(), 'url' => $url])) ?>" method="POST">
					<?= csrf_field() ?>
					<button class="btn btn-secondary" type="submit" data-confirm="<?= I18N::translate('Are you sure you want to reject all the changes to this family tree?') ?>" onclick="return confirm(this.dataset.confirm);">
						<?= I18N::translate('Reject all changes') ?>
					</button>
				</form>
			</div>
		</div>
	<?php endforeach ?>
</div>
