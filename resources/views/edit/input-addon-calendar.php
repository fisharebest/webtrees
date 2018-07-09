<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="input-group-append">
	<span class="input-group-text">
		<?= FontAwesome::linkIcon('calendar', I18N::translate('Select a date'), ['href' => '#', 'onclick' => 'return calendarWidget("caldiv' . $id . '", "' . $id . '");']) ?>
	</span>
</div>
