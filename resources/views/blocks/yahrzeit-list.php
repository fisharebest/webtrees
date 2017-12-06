<?php use Fisharebest\Webtrees\I18N; ?>'

<?php foreach ($yahrzeits as $yahrzeit): ?>
	<a href="<?= e($yahrzeit->individual->getRawUrl()) ?>" class="list_item name2">
		<?= $yahrzeit->individual->getFullName() ?>
	</a>
	<?= $yahrzeit->individual->getSexImage() ?>
	<div class="indent">
		<?= $yahrzeit->fact->getDate()->display(true) ?>,
		<?= I18N::translate('%s year anniversary', $yahrzeit->fact->anniv) ?>
	</div>
<?php endforeach ?>
