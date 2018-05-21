<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees', ['ged' => $tree->getName()]) => I18N::translate('Manage family trees '), $title]]) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('This will update the highest-level part or parts of the place name. For example, “Mexico” will match “Quintana Roo, Mexico”, but not “Santa Fe, New Mexico”.') ?>
</p>

<form>
	<input type="hidden" name="route" value="admin-trees-places">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

	<dl>
		<dt>
			<label for="search"><?= I18N::translate('Search for') ?></label>
		</dt>
		<dd>
			<input name="search" id="search" type="text" size="60" value="<?= e($search) ?>" data-autocomplete-type="PLAC" required autofocus>
		</dd>
		<dt>
			<label for="replace"><?= I18N::translate('Replace with') ?></label>
		</dt>
		<dd>
			<input name="replace" id="replace" type="text" size="60" value="<?= e($replace) ?>" data-autocomplete-type="PLAC" required>
		</dd>
	</dl>

	<button class="btn btn-primary" type="submit" value="preview"><?= /* I18N: A button label. */
		I18N::translate('preview') ?></button>
</form>

<?php if ($search && $replace): ?>
	<?php if (empty($changes)): ?>
		<p>
			<?= I18N::translate('No places have been found.') ?>
		</p>
	<?php else: ?>
		<p>
			<?= I18N::translate('The following places would be changed:') ?>
		</p>

		<ul>
			<?php foreach ($changes as $old_place => $new_place) { ?>
				<li>
					<?= e($old_place) ?> &rarr; <?= e($new_place) ?>
				</li>
			<?php } ?>
		</ul>

		<form method="post" action="<?= e(route('admin-trees-places', ['ged' => $tree->getName(), 'search' => $search, 'replace' => $replace])) ?>">
			<?= csrf_field() ?>
			<button class="btn btn-primary" type="submit" value="update" name="confirm"><?= /* I18N: A button label. */
				I18N::translate('update') ?></button>
		</form>
	<?php endif ?>
<?php endif ?>
