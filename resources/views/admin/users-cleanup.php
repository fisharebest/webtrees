<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsDate; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-users') => I18N::translate('User administration'), $title]]) ?>

<h1><?= $title ?></h1>

<form>
	<input type="hidden" name="route" value="admin-users-cleanup">
	<div class="form-group row">
		<label for="months" class="col-sm-8 col-form-label">
			<?= I18N::translate('Number of months since the last sign-in for a user’s account to be considered inactive: ') ?>
		</label>
		<div class="col-sm-2">
			<?= Bootstrap4::select($options, $months, ['id' => 'months', 'name' => 'months']) ?>
		</div>
		<div class="col-sm-2">
			<button type="submit" class="btn btn-primary">
				<?= I18N::translate('update') ?>
			</button>
		</div>
	</div>
</form>

<form method="post">
	<?= csrf_field() ?>

	<table class="table table-bordered">
		<?php foreach ($inactive_users as $user): ?>
			<tr>
				<td>
					<a href="<?= e(route('admin-users-edit', ['user_id' => $user->getUserId()])) ?>">
						<?= e($user->getUserName()) ?>
						—
						<span dir="auto"><?= e($user->getRealName()) ?></span>
					</a>
				</td>
				<td>
					<?= I18N::translate('User’s account has been inactive too long: ') . FunctionsDate::timestampToGedcomDate(max((int) $user->getPreference('reg_timestamp'), (int) $user->getPreference('sessiontime')))->display() ?>
				</td>
				<td>
					<input type="checkbox" name="del_<?= $user->getUserId() ?>">
				</td>
			</tr>
		<?php endforeach ?>

		<?php foreach ($unverified_users as $user): ?>
			<tr>
				<td>
					<a href="<?= e(route('admin-users-edit', ['user_id' => $user->getUserId()])) ?>">
						<?= e($user->getUserName()) ?>
						—
						<span dir="auto"><?= e($user->getRealName()) ?></span>
					</a>
				</td>
				<td>
					<?= I18N::translate('User didn’t verify within 7 days.') ?>
					<?php if ($user->getPreference('verified_by_admin') !== '1'): ?>
					<?= I18N::translate('User not verified by administrator.') ?>
					<?php endif ?>
				</td>
				<td>
					<input type="checkbox" name="del_<?= $user->getUserId() ?>">
				</td>
			</tr>
		<?php endforeach ?>
	</table>

	<p>
		<?php if (empty($inactive_users) && empty($unverified_users)): ?>
			<?= I18N::translate('Nothing found to cleanup') ?>
		<?php else: ?>
		<button type="submit" class="btn btn-primary">
			<?= I18N::translate('delete') ?>
		</button>
		<?php endif ?>
	</p>
</form>
