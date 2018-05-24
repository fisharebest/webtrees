<?php use Fisharebest\Webtrees\Functions\Functionsedit; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-ancestors-chart hidden-print mb-4" name="searchform" onsubmit="return checknames(this);">
	<input type="hidden" name="route" value="search-phonetic">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="firstname">
			<?= I18N::translate('Given name') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<div class="input-group input-group-sm">
				<input class= "form-control form-control-sm" type="text" name="firstname" id="firstname" value="<?= e($firstname) ?>" autofocus>
				<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsEdit::inputAddonKeyboard('query') ?>
						</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label"  for="lastname">
			<?= I18N::translate('Surname') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<div class="input-group input-group-sm">
				<input class="form-control form-control-sm" type="text" name="lastname" id="lastname" value="<?= e($lastname) ?>">
				<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsEdit::inputAddonKeyboard('query') ?>
						</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="place">
			<?= I18N::translate('Place') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<input class="form-control form-control-sm" type="text" name="place" id="place" value="<?= e($place) ?>">
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Phonetic algorithm') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="radio" name="soundex" value="Russell" <?= $soundex === 'Russell' ? 'checked' : '' ?>>
						<?= I18N::translate('Russell') ?>
					</label>
				</div>
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" type="radio" name="soundex" value="DaitchM" <?= $soundex === 'DaitchM' ? 'checked' : '' ?>>
						<?= I18N::translate('Daitch-Mokotoff') ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<?php if (count($all_trees) > 1): ?>
		<fieldset class="form-group">
			<div class="row">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Family trees') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value pt-2">
					<div class="d-flex justify-content-between">
						<div id="search-trees" class="form-check">
							<?php foreach ($all_trees as $tree): ?>
								<div class="col px-0">
									<label class="form-check-label">
										<input class="form-check form-check-input" type="checkbox" <?= in_array($tree, $search_trees) ? 'checked' : '' ?> value="<?= ($tree->getName()) ?>" name="search_trees[]">
										<?= e($tree->getTitle()) ?>
									</label>
								</div>
							<?php endforeach ?>
						</div>
						<?php if (count($all_trees) > 3): ?>
							<div class="d-row align-self-end mb-2">
								<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select all (of the family trees) */ I18N::translate('select all') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', true);});return false;">
								<input type="button" class="btn btn-sm btn-secondary mx-1" value="<?= /* I18N: select none (of the family trees) */ I18N::translate('select none') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', false);});return false;">
								<?php if (count($all_trees) > 10): ?>
									<input type="button" value="<?= I18N::translate('invert selection') ?>" onclick="$('#search-trees :checkbox').each(function(){$(this).attr('checked', !$(this).attr('checked'));});return false;">
								<?php endif ?>
							</div>
						<?php endif ?>
					</div>
				</div>
			</div>
		</fieldset>
	<?php endif ?>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label"></label>
		<div class="col-sm-9 wt-page-options-value">
			<input type="submit" class="btn btn-primary" value="<?=  /* I18N: A button label. */ I18N::translate('search') ?>">
		</div>
	</div>
</form>

<?php if ($firstname !== '' || $lastname !== '' || $place !== ''): ?>
	<?php if (empty($individuals)): ?>
		<div class="alert alert-info row">
			<?= I18N::translate('No results found.') ?>
		</div>
	<?php else: ?>
		<?= view('search-results', ['individuals' => $individuals, 'search_families' => false, 'search_individuals' => true, 'search_notes' => false, 'search_sources' => false, 'search_repositories' => false]) ?>
	<?php endif ?>
<?php endif ?>

<?= view('modals/on-screen-keyboard') ?>
