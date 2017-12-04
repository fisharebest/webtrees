<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([route('admin-control-panel') => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('Files marked with %s are required for proper operation and cannot be removed.', '<i class="fa fa-ban text-danger"></i>') ?>
</p>

<form method="post">
	<input type="hidden" value="admin-clean-data">
	<?= csrf_field() ?>
	<ul class="fa-ul">
		<?php
		foreach ($entries as $entry) {
			if (in_array($entry, $protected)) {
				echo '<li><i class="fa-li fa fa-ban text-danger"></i>', e($entry), '</li>';
			} else {
				echo '<li><i class="fa-li fa fa-trash-o"></i>';
				echo '<label>';
				echo '<input type="checkbox" name="to_delete[]" value="', e($entry), '"> ';
				echo e($entry);
				echo '</label></li>';
			}
		}
		?>
	</ul>

	<button class="btn btn-danger" type="submit">
		<i class="fa fa-trash-o"></i>
		<?= /* I18N: A button label. */ I18N::translate('delete') ?>
	</button>
</form>
