<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<span class="input-group-append">
	<span class="input-group-text">
		<?= FontAwesome::linkIcon('keyboard', I18N::translate('Find a special character'), ['class' => 'wt-osk-trigger', 'href' => '#', 'data-id' => $id]) ?>
	</span>
</span>
