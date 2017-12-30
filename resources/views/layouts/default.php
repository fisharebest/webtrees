<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\DebugBar; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<?= $theme_head ?>
	<?= $theme_body_header ?>

		<?= $content ?>

		<?= $theme_footer_container ?>

		<script>
			// Global constants
			var WT_MODULES_DIR = <?= json_encode(WT_MODULES_DIR) ?>;
			var WT_GEDCOM      = <?= json_encode($tree ? $tree->getName() : '') ?>;
			var textDirection  = <?= json_encode(I18N::direction()) ?>;
			var WT_LOCALE      = <?= json_encode(WT_LOCALE) ?>;
		</script>

		<script src="<?= e(WT_JQUERY_JS_URL) ?>"></script>
		<script src="<?= e(WT_POPPER_JS_URL) ?>"></script>
		<script src="<?= e(WT_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= e(WT_DATATABLES_JS_URL) ?>"></script>
		<script src="<?= e(WT_DATATABLES_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= e(WT_SELECT2_JS_URL) ?>"></script>
		<script src="<?= e(WT_SELECT2_JS_URL) ?>"></script>
		<script src="<?= e(WT_TYPEAHEAD_JS_URL) ?>"></script>
		<?= $theme_footer_javascript ?>

		<?= DebugBar::renderHead() ?>
		<?= DebugBar::render() ?>
	</body>
</html>
