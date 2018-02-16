<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>

<?php if ($summary !== ''): ?>
	<span class="details_label"><?= $summary ?></span>
<?php else: ?>
	<?php foreach ($facts as $fact): ?>
		<?php $record = $fact->getParent(); ?>
		<a href="<?= e($record->url()) ?>" class="list_item name2">
			<?= $record->getFullName() ?>
		</a>
		<?php if ($record instanceof Individual): ?>
			<?= $record->getSexImage() ?>
		<?php endif ?>
		<div class="indent">
			<?= $fact->getLabel() . ' — ' . $fact->getDate()->display(true); ?>
			<?= ' (' . I18N::timeAgo($fact->anniv * 365 * 24 * 60 * 60) . ')'; ?>
			<?php if (!$fact->getPlace()->isEmpty()): ?>
				<?= ' — <a href="' . $fact->getPlace()->getURL() . '">' . $fact->getPlace()->getFullName() . '</a>'; ?>
			<?php endif ?>
		</div>
	<?php endforeach ?>
<?php endif ?>
