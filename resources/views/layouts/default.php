<?php use Fisharebest\Webtrees\DebugBar; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<?= $theme_head ?>
	<?= $theme_body_header ?>

		<?= $content ?>

		<?= $theme_footer_container ?>

		<script src="<?= e(WT_ASSETS_URL . 'js/vendor.js') ?>"></script>
		<script src="<?= e(WT_ASSETS_URL . 'js/webtrees.js') ?>"></script>

	<?= View::stack('javascript') ?>

	<?= $theme_footer_javascript ?>

		<?= DebugBar::renderHead() ?>
		<?= DebugBar::render() ?>
	</body>
</html>
