<?php use Fisharebest\Webtrees\Database; ?>
<?php use Fisharebest\Webtrees\Html; ?>
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

		<script src="<?= Html::escape(WT_JQUERY_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_POPPER_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_DATATABLES_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_DATATABLES_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_SELECT2_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_WEBTREES_JS_URL) ?>"></script>

	</body>
</html>
<!-- webtrees: <?= WT_VERSION ?> -->' .
<!-- Execution time: <?= I18N::number(microtime(true) - WT_START_TIME, 3) ?> seconds -->
<!-- Memory: <?= I18N::number(memory_get_peak_usage(true) / 1024) ?> KB -->
<!-- SQL queries: <?= I18N::number(Database::getQueryCount()) ?> -->
