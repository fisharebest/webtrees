<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h1><?= $title ?></h1>

<!-- WEBTREES VERSION -->
<div class="card mb-4">
	<div class="card-header">
		<h2 class="mb-0">
			<?= WT_WEBTREES ?>
			<span class="badge badge-secondary">
				<?= WT_VERSION ?>
			</span>
		</h2>
	</div>
	<div class="card-body">
		<p>
			<?= /* I18N: %s is a URL/link to the project website */
			I18N::translate('Support and documentation can be found at %s.', '<a href="https://webtrees.net/">webtrees.net</a>') ?>
		</p>
	</div>
</div>

<!-- FAMILY TREES -->
<div class="card mb-4 <?= array_sum($changes) ? 'card-outline-danger' : '' ?>">
	<div class="card-header">
		<h2 class="mb-0">
			<?= I18N::translate('Family trees') ?>
			<span class="badge badge-secondary">
					<?= I18N::number(count($all_trees)) ?>
				</span>
		</h2>
	</div>
	<div class="card-body">
		<table class="table table-sm">
			<caption class="sr-only">
				<?= I18N::translate('Family trees') ?>
			</caption>
			<thead>
				<tr>
					<th><?= I18N::translate('Family tree') ?></th>
					<th><span class="sr-only"><?= I18N::translate('Manage family trees') ?></span></th>
					<th class="text-right"><?= I18N::translate('Pending changes') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Individuals') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Families') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Sources') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Repositories') ?></th>
					<th class="d-none d-sm-table-cell text-right"><?= I18N::translate('Media') ?></th>
					<th class="d-none d-lg-table-cell text-right"><?= I18N::translate('Notes') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($all_trees as $tree): ?>
					<tr class="<?= $changes[$tree->getTreeId()] ? 'danger' : '' ?>">
						<th scope="row">
							<a href="<?= e(route('tree-page', ['ged' => $tree->getName()])) ?>">
								<?= e($tree->getName()) ?>
								-
								<?= e($tree->getTitle()) ?>
							</a>
						</th>
						<td>
							<?= FontAwesome::linkIcon('preferences', I18N::translate('Manage family trees'), ['href' => route('admin-trees', ['ged' => $tree->getName()])]) ?>
						</td>
						<td class="text-right">
							<?php if ($changes[$tree->getTreeId()]): ?>
								<a href="<?= e(route('show-pending', ['ged' => $tree->getName(), 'url' => route('admin-control-panel')])) ?>">
									<?= I18N::number($changes[$tree->getTreeId()]) ?>
									<span class="sr-only"><?= I18N::translate('Pending changes') ?> <?= e($tree->getTitle()) ?></span>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($individuals[$tree->getTreeId()]): ?>
								<a href="<?= e(route('individual-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($individuals[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($families[$tree->getTreeId()]): ?>
								<a href="<?= e(route('family-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($families[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($sources[$tree->getTreeId()]): ?>
								<a href="<?= e(route('source-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($sources[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($repositories[$tree->getTreeId()]): ?>
								<a href="<?= e(route('repository-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($repositories[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-sm-table-cell text-right">
							<?php if ($media[$tree->getTreeId()]): ?>
								<a href="<?= e(route('media-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($media[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
						<td class="d-none d-lg-table-cell text-right">
							<?php if ($notes[$tree->getTreeId()]): ?>
								<a href="<?= e(route('note-list', ['ged' => $tree->getName()])) ?>">
									<?= I18N::number($media[$tree->getTreeId()]) ?>
								</a>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
