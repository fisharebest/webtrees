<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>

<p>
	<?= /* I18N: FAQ = “Frequently Asked Question” */
	I18N::translate('FAQs are lists of questions and answers, which allow you to explain the site’s rules, policies, and procedures to your visitors. Questions are typically concerned with privacy, copyright, user-accounts, unsuitable content, requirement for source-citations, etc.') ?>
	<?= I18N::translate('You may use HTML to format the answer and to add links to other websites.') ?>
</p>

<p>
<form class="form form-inline">
	<input type="hidden" name="route" value="module">
	<input type="hidden" name="module" value="faq">
	<input type="hidden" name="action" value="Admin">
	<label for="ged" class="sr-only">
		<?= I18N::translate('Family tree') ?>
	</label>
	<?= Bootstrap4::select($tree_names, $tree->getName(), ['id' => 'ged', 'name' => 'ged']) ?>
	<input type="submit" class="btn btn-primary" value="<?= I18N::translate('show') ?>">
</form>
</p>

<p>
	<a href="<?= e(route('module', ['module' => 'faq', 'action' => 'AdminEdit', 'ged' => $tree->getName()])) ?>" class="btn btn-link">
		<i class="fas fa-plus"></i>
		<?= /* I18N: FAQ = “Frequently Asked Question” */
		I18N::translate('Add an FAQ') ?>
	</a>
</p>

<table class="table table-bordered">
	<thead>
		<tr>
			<th><?= I18N::translate('Sort order') ?></th>
			<th><?= I18N::translate('Family tree') ?></th>
			<th><?= I18N::translate('Question') ?></th>
			<th><span class="sr-only"><?= I18N::translate('Move up') ?></span></th>
			<th><span class="sr-only"><?= I18N::translate('Move down') ?></span></th>
			<th><span class="sr-only"><?= I18N::translate('Edit') ?></span></th>
			<th><span class="sr-only"><?= I18N::translate('Delete') ?></span></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($faqs as $faq): ?>
			<tr class="faq_edit_pos">
				<td>
					<?= I18N::number($faq->block_order + 1) ?>
				</td>
				<td>
					<?php if ($faq->gedcom_id === null): ?>
						<?= I18N::translate('All') ?>
					<?php else: ?>
						<?= e($tree->getTitle()) ?>
					<?php endif ?>
				</td>
				<td>
					<?= e($faq->header) ?>
				</td>
				<td>
					<?php if ($faq->block_order != $min_block_order): ?>
						<form action="<?= e(route('module', ['module' => 'faq', 'action' => 'AdminMoveUp', 'block_id' => $faq->block_id, 'ged' => $tree->getName()])) ?>" method="post">
							<?= csrf_field() ?>
							<button type="submit" class="btn btn-secondary">
								<i class="fas fa-arrow-up"></i>
								<?= I18N::translate('Move up') ?>
							</button>
						</form>
					<?php endif ?>
				</td>
				<td>
					<?php if ($faq->block_order != $max_block_order): ?>
						<form action="<?= e(route('module', ['module' => 'faq', 'action' => 'AdminMoveDown', 'block_id' => $faq->block_id, 'ged' => $tree->getName()])) ?>" method="post">
							<?= csrf_field() ?>
							<button type="submit" class="btn btn-secondary">
								<i class="fas fa-arrow-down"></i>
								<?= I18N::translate('Move down') ?>
							</button>
						</form>
					<?php endif ?>
				</td>
				<td>
					<a href="<?= e(route('module', ['module' => 'faq', 'action' => 'AdminEdit', 'block_id' => $faq->block_id, 'ged' => $tree->getName()])) ?>" class="btn btn-primary">
						<i class="fas fa-pencil-alt"></i>
						<?= I18N::translate('Edit') ?>
					</a>
				</td>
				<td>
					<form action="<?= e(route('module', ['module' => 'faq', 'action' => 'AdminDelete', 'block_id' => $faq->block_id, 'ged' => $tree->getName()])) ?>" method="post">
						<?= csrf_field() ?>
						<button type="submit" class="btn btn-danger" onclick="return confirm(this.dataset.confirm);" data-confirm="<?= I18N::translate('Are you sure you want to delete “%s”?', e($faq->header)) ?>">
							<i class="fas fa-trash-alt"></i>
							<?= I18N::translate('delete') ?>
						</button>
					</form>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
