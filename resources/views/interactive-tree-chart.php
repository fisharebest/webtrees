<?php use Fisharebest\Webtrees\I18N; ?>

<div id="<?= $name ?>_out" class="tv_out">
	<div id="tv_tools">
		<ul>
			<li id="tvbCompact" class="tv_button">
				<img src="<?= WT_MODULES_DIR ?>tree/images/compact.png" alt="<?= I18N::translate('Use compact layout') ?>" title="<?= I18N::translate('Use compact layout') ?>">
			</li>
			<li class="tv_button" id="<?= $name ?>_loading">
				<i class="icon-loading-small"></i>
			</li>
		</ul>
	</div>
	<div id="<?= $name ?>_in" class="tv_in" dir="ltr">
		<?= $individual ?>
	</div>
</div>
