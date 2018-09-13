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
                <?= view('admin/control-panel-tree-list', [
                    'all_trees'    => $all_trees,
                    'changes'      => $changes,
                    'individuals'  => $individuals,
                    'families'     => $families,
                    'sources'      => $sources,
                    'repositories' => $repositories,
                    'media'        => $media,
                    'notes'        => $notes,
                ]); ?>
			</tbody>
		</table>
	</div>
</div>
