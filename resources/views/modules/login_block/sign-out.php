<?php use Fisharebest\Webtrees\I18N; ?>

<p>
<?= I18N::translate('You are signed in as %s.', e($user->getUserName())) ?>
</p>

<form method="POST" action="<?= e(route('logout')) ?>">
	<?= csrf_field() ?>

	<button type="submit" class="btn btn-primary">
		<?= /* I18N: A button label. */ I18N::translate('sign out') ?>
	</button>
</form>
