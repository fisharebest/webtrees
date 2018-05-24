<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form class="wt-page-options wt-page-options-search hidden-print mb-4" name="searchform" onsubmit="return checknames(this);">
	<input type="hidden" name="route" value="search-general">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="query">
			<?= I18N::translate('Search for') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<div class="input-group input-group-sm">
				<input id="query" class="form-control form-control-sm" type="text" name="query" value="<?= e($query) ?>" required>
				<div class="input-group-append">
						<span class="input-group-text">
							<?= FunctionsEdit::inputAddonKeyboard('query') ?>
						</span>
				</div>
			</div>
		</div>
	</div>
	<fieldset class="form-group">
		<div class="row">
			<label class="col-sm-3 col-form-label wt-page-options-label">
				<?= I18N::translate('Records') ?>
			</label>
			<div class="col-sm-9 wt-page-options-value">
				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" <?= $search_individuals ? 'checked' : '' ?> name="search_individuals" type="checkbox">
						<?= I18N::translate('Individuals') ?>
					</label>
				</div>

				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" <?= $search_families ? 'checked' : '' ?> name="search_families" type="checkbox">
						<?= I18N::translate('Families') ?>
					</label>
				</div>

				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" <?= $search_sources ? 'checked' : '' ?> name="search_sources" type="checkbox">
						<?= I18N::translate('Sources') ?>
					</label>
				</div>

				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" <?= $search_repositories ? 'checked' : '' ?> name="search_repositories" type="checkbox">
						<?= I18N::translate('Repositories') ?>
					</label>
				</div>

				<div class="form-check form-check-inline">
					<label class="form-check-label">
						<input class="form-check-input" <?= $search_notes ? 'checked' : '' ?> name="search_notes" type="checkbox">
						<?= I18N::translate('Shared notes') ?>
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

<?php if ($query !== ''): ?>
	<?php if (empty($individuals) && empty($families) && empty($repositories) && empty($sources) && empty($notes)): ?>
		<div class="alert alert-info row">
			<?= I18N::translate('No results found.') ?>
		</div>
	<?php else: ?>
		<?= view('search-results', ['families' => $families, 'individuals' => $individuals, 'notes' => $notes, 'repositories' => $repositories, 'sources' => $sources, 'search_families' => $search_families, 'search_individuals' => $search_individuals, 'search_notes' => $search_notes, 'search_repositories' => $search_repositories, 'search_sources' => $search_sources]) ?>
	<?php endif ?>
<?php endif ?>

<?= view('modals/on-screen-keyboard') ?>
