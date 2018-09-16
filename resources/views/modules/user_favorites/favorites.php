<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<?php foreach ($favorites as $favorite): ?>
	<div class="person_box action_header<?= $favorite->record instanceof Individual ? $favorite->record->getSex() : '' ?>">
		<?php if ($favorite->favorite_type === 'URL'): ?>
			<a href="<?= e($favorite->url) ?>"><b><?= e($favorite->title) ?></b></a>
		<?php elseif ($favorite->record instanceof Individual): ?>
			<?= Theme::theme()->individualBoxLarge($favorite->record) ?>
		<?php elseif ($favorite->record !== null) : ?>
			<?= $favorite->record->formatList() ?>
		<?php endif ?>

		<div class="wt-favorites-block-note">
            <?= e((string) $favorite->note) ?>
        </div>
	</div>

	<form action="<?= e(route('module', ['module' => 'user_favorites', 'action' => 'DeleteFavorite', 'ged' => $tree->getName(), 'favorite_id' => $favorite->favorite_id])) ?>" method="post">
		<?= csrf_field() ?>
		<button type="submit" class="btn btn-link btn-sm" data-confirm="<?= I18N::translate('Are you sure you want to remove this item from your list of favorites?') ?>" onclick="return confirm(this.dataset.confirm);">
			<?= I18N::translate('Remove') ?>
		</button>
	</form>
<?php endforeach ?>

<div class="add_fav_head">
	<a href="#" onclick="return expand_layer('add_fav<?= e($block_id) ?>');">
		<?= I18N::translate('Add a favorite') ?>
		<i id="add_fav<?= e($block_id) ?>_img" class="icon-plus"></i>
	</a>
</div>
<div id="add_fav<?= e($block_id) ?>" style="display: none;">
	<form action="<?= e(route('module', ['module' => 'user_favorites', 'action' => 'AddFavorite', 'ged' => $tree->getName()])) ?>" method="post">
		<?= csrf_field() ?>
		<div class="add_fav_ref">
			<input type="radio" name="fav_category" value="record" checked onclick="$('#xref<?= e($block_id) ?>').removeAttr('disabled'); $('#url, #title').attr('disabled','disabled').val('');">
			<label for="xref<?= e($block_id) ?>">
				<?= I18N::translate('Record') ?>
			</label>
			<input class="pedigree_form" data-autocomplete-type="IFSRO" type="text" name="xref" id="xref<?= e($block_id) ?>" size="5">
		</div>
		<div class="add_fav_url">
			<input type="radio" name="fav_category" value="url" onclick="$('#url, #title').removeAttr('disabled'); $('#xref<?= e($block_id) ?>').attr('disabled','disabled').val('');">
			<input type="text" name="url" id="url" size="20" value="" placeholder="<?= I18N::translate('URL') ?>" disabled>
			<input type="text" name="title" id="title" size="20" value="" placeholder="<?= I18N::translate('Title') ?>" disabled>
			<p>
				<?= I18N::translate('Enter an optional note about this favorite') ?>
			</p>
			<textarea name="note" rows="6" cols="50"></textarea>
		</div>
		<button type="submit" class="btn btn-primary">
			<?= /* I18N: A button label. */ I18N::translate('add') ?>
		</button>
	</form>
</div>
