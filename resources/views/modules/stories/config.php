<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-inline mb-4">
	<input type="hidden" name="route" value="module">
	<input type="hidden" name="module" value="stories">
	<input type="hidden" name="action" value="Admin">

	<label for="ged" class="sr-only">
		<?= I18N::translate('Family tree') ?>
	</label>

	<?= Bootstrap4::select($tree_names, $tree->getName(), ['id' => 'ged', 'name' => 'ged']) ?>
	<button type="submit" class="btn btn-primary">
		<?= I18N::translate('show') ?>
	</button>
</form>

<p>
	<a href="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminEdit', 'ged' => $tree->getName()])) ?>" class="btn btn-link">
		<i class="fas fa-plus"></i>
		<?= I18N::translate('Add a story') ?>
	</a>
</p>

<table class="table table-bordered table-sm">
	<thead>
		<tr>
			<th><?= I18N::translate('Individual') ?></th>
			<th><?= I18N::translate('Story title') ?></th>
			<th><?= I18N::translate('Edit') ?></th>
			<th><?= I18N::translate('Delete') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($stories as $story): ?>
			<tr>
				<td>
					<?php if ($story->individual !== null): ?>
						<a href="<?= e($story->individual->url()) ?>#tab-stories">
							<?= $story->individual->getFullName() ?>
						</a>
					<?php else: ?>
						<?= $story->xref ?>
					<?php endif ?>
				</td>
				<td>
					<?= e($story->title) ?>
				</td>
				<td>
					<a class="btn btn-primary" href="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminEdit', 'ged' => $tree->getName(), 'block_id' => $story->block_id])) ?>">
						<i class="fas fa-pencil-alt"></i> <?= I18N::translate('Edit') ?>
					</a>
				</td>
				<td>
					<form action="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminDelete', 'ged' => $tree->getName(), 'block_id' => $story->block_id])) ?>" method="post">
						<?= csrf_field() ?>
						<button type="submit" class="btn btn-danger" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($story->title)) ?>" onclick="return confirm(this.dataset.confirm);">
							<i class="fas fa-trash-alt"></i> <?= I18N::translate('Delete') ?>
						</button>
					</form>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
