<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<form method="POST" autocomplete="off">
	<input name="route" type="hidden" value="setup">
	<input name="dbhost" type="hidden" value="<?= Html::escape($dbhost) ?>">
	<input name="dbport" type="hidden" value="<?= Html::escape($dbport) ?>">
	<input name="dbuser" type="hidden" value="<?= Html::escape($dbuser) ?>">
	<input name="dbpass" type="hidden" value="<?= Html::escape($dbpass) ?>">
	<input name="dbname" type="hidden" value="<?= Html::escape($dbname) ?>">
	<input name="tblpfx" type="hidden" value="<?= Html::escape($tblpfx) ?>">
	<input name="wtname" type="hidden" value="<?= Html::escape($wtname) ?>">
	<input name="wtuser" type="hidden" value="<?= Html::escape($wtuser) ?>">
	<input name="wtpass" type="hidden" value="<?= Html::escape($wtpass) ?>">
	<input name="wtemail" type="hidden" value="<?= Html::escape($wtemail) ?>">

	<div class="row form-group">
		<label class="col-form-label col-sm-3" for="lang">
			<?= I18N::translate('Language') ?>
		</label>
		<div class="col-sm-9">
			<select class="form-control" id="lang" name="lang">
				<?php foreach ($locales as $locale): ?>
					<option value="<?= $locale->languageTag() ?>" <?= $lang === $locale->languageTag() ? 'selected' : '' ?>>
						<?= $locale->endonym() ?>
					</option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<hr>

	<button class="btn btn-primary" name="step" type="submit" value="2">
		<?= I18N::translate('next') ?>
	</button>
</form>
