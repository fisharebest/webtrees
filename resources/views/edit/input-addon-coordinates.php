<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="input-group-append">
	<span class="input-group-text">
		<?= FontAwesome::linkIcon('coordinates', I18N::translate('Latitude') . ' / ' . I18N::translate('Longitude'), ['data-toggle' => 'collapse', 'data-target' => '.child_of_' . $id]) ?>
	</span>
</div>
