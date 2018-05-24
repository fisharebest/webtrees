<?php use Fisharebest\Webtrees\I18N; ?>

<form action="<?= e(route('admin-webtrees1-thumbs-action')) ?>" method="POST">
	<input type="hidden" name="thumbnail" value="<?= e($thumbnail) ?>">
	<?php foreach ($media as $medium): ?>
		<input type="hidden" name="ged[]" value="<?= e($medium->getTree()->getName()) ?>">
		<input type="hidden" name="xref[]" value="<?= e($medium->getXref()) ?>">
	<?php endforeach ?>
	<div class="btn-group">
		<?php if (!empty($media)): ?>
			<button class="btn <?= $difference < 99 ? 'btn-primary' : 'btn-secondary' ?> wt-fix-button" type="button" data-action="add">
				<i class="fas fa-plus"></i>
				<?= I18N::translate('add') ?>
				<?php if (count($media) > 1): ?>
					<?= I18N::number(count($media)) ?>
				<?php endif ?>
			</button>
		<?php endif ?>
		<button class="btn <?= $difference >= 99 ? 'btn-primary' : 'btn-secondary' ?> wt-fix-button" type="button" data-action="delete">
			<i class="fas fa-trash-alt"></i>
			<?= I18N::translate('delete') ?>
		</button>
	</div>
</form>
