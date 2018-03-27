<?php use Fisharebest\Webtrees\I18N; ?>

<div class="row">
			<span class="col list_label mr-1">
				<i class="icon-place"></i>
				<?= I18N::translate('Place list') ?>
			</span>
</div>
<div class="row">
	<?php foreach ($columns as $column): ?>
		<ul class="col list_value_wrap mr-1">
			<?php foreach ($column as $item): ?>
				<li>
					<a href="<?= $item->getURL() ?>"><?= $item->getReverseName() ?></a>
				</li>
			<?php endforeach ?>
		</ul>
	<?php endforeach ?>
</div>

