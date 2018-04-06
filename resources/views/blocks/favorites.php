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

		<?= e((string) $favorite->note) ?>
	</div>

	<?php if ($ctype == 'user' || $is_manager): ?>
		<p class="small">
			<a href="index.php?ctype=<?= $ctype ?>&amp;ged=<?= e($tree->getName()) ?>&amp;action=deletefav&amp;favorite_id=<?= $favorite->favorite_id ?>" data-confirm="<?= I18N::translate('Are you sure you want to remove this item from your list of favorites?') ?>" onclick="return confirm(this.dataset.confirm);">
				<?= I18N::translate('Remove') ?>
			</a>
		</p>
	<?php endif ?>
<?php endforeach ?>
