<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<fieldset class="form-group">
	<div class="row">
		<legend class="col-form-label col-sm-3">
			<?= I18N::translate('Last change') ?>
		</legend>
		<div class="col-sm-9">
			<?= Bootstrap4::checkbox(/* I18N: label for yes/no option */
				I18N::translate('Show date of last update'), false, ['name' => 'show_last_update', 'checked' => (bool) $show_last_update]) ?>
		</div>
	</div>
</fieldset>

<fieldset class="form-group">
	<div class="row">
		<legend class="col-form-label col-sm-3">
			<?= I18N::translate('Statistics') ?>
		</legend>
		<div class="col-sm-9">
			<?= Bootstrap4::checkbox(I18N::translate('Individuals'), false, ['name' => 'stat_indi', 'checked' => (bool) $stat_indi]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Total surnames'), false, ['name' => 'stat_surname', 'checked' => (bool) $stat_surname]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Families'), false, ['name' => 'stat_fam', 'checked' => (bool) $stat_fam]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Sources'), false, ['name' => 'stat_sour', 'checked' => (bool) $stat_sour]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Media objects'), false, ['name' => 'stat_media', 'checked' => (bool) $stat_media]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Repositories'), false, ['name' => 'stat_repo', 'checked' => (bool) $stat_repo]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Total events'), false, ['name' => 'stat_events', 'checked' => (bool) $stat_events]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Total users'), false, ['name' => 'stat_users', 'checked' => (bool) $stat_users]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Earliest birth'), false, ['name' => 'stat_first_birth', 'checked' => (bool) $stat_first_birth]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Latest birth'), false, ['name' => 'stat_last_birth', 'checked' => (bool) $stat_last_birth]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Earliest death'), false, ['name' => 'stat_first_death', 'checked' => (bool) $stat_first_death]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Latest death'), false, ['name' => 'stat_last_death', 'checked' => (bool) $stat_last_death]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Individual who lived the longest'), false, ['name' => 'stat_long_life', 'checked' => (bool) $stat_long_life]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Average age at death'), false, ['name' => 'stat_avg_life', 'checked' => (bool) $stat_avg_life]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Family with the most children'), false, ['name' => 'stat_most_chil', 'checked' => (bool) $stat_most_chil]) ?>
			<?= Bootstrap4::checkbox(I18N::translate('Average number of children per family'), false, ['name' => 'stat_avg_chil', 'checked' => (bool) $stat_avg_chil]) ?>
		</div>
	</div>
</fieldset>

<fieldset class="form-group">
	<div class="row">
		<legend class="col-form-label col-sm-3">
			<label for="show_common_surnames">
				<?= I18N::translate('Surnames') ?>
			</label>
		</legend>
		<div class="col-sm-9">
			<?= Bootstrap4::checkbox(I18N::translate('Most common surnames'), false, ['name' => 'show_common_surnames', 'checked' => (bool) $show_common_surnames]) ?>
			<label for="number_of_surnames">
				<?= /* I18N: ... to show in a list */
				I18N::translate('Number of surnames') ?>
				<input
					class="form-control"
					id="number_of_surnames"
					maxlength="5"
					name="number_of_surnames"
					pattern="[1-9][0-9]*"
					required
					type="text"
					value="<?= e($number_of_surnames) ?>"
				>
			</label>
		</div>
	</div>
</fieldset>
