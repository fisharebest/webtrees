<?php use Fisharebest\Webtrees\DebugBar; ?>
<?php use Fisharebest\Webtrees\FlashMessages; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>
<?php use Fisharebest\Webtrees\View; ?>

<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<head>
		<meta charset="UTF-8">
		<meta name="csrf" content="<?= e(csrf_token()) ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="<?= e($meta_robots ?? 'noindex,nofollow') ?>">
		<meta name="generator" content="<?= e(WT_WEBTREES) ?>">
		<?php if ($tree !== null): ?>
			<meta name="description" content="<?= e($tree->getPreference('META_DESCRIPTION')) ?>">
		<?php endif ?>

		<title>
			<?= e(strip_tags($title)) ?>
			<?php if ($tree !== null && $tree->getPreference('META_TITLE') !== ''): ?>
				– <?= e($tree->getPreference('META_TITLE')) ?>
			<?php endif ?>
		</title>

		<link rel="icon" href="<?= Theme::theme()::ASSET_DIR ?>favicon.png" type="image/png">
		<link rel="icon" type="image/png" href="<?= Theme::theme()::ASSET_DIR ?>favicon192.png" sizes="192x192">
		<link rel="apple-touch-icon" sizes="180x180" href="<?= Theme::theme()::ASSET_DIR ?>favicon180.png">

		<?php foreach (Theme::theme()->stylesheets() as $stylesheet): ?>
			<link rel="stylesheet" type="text/css" href="<?=  $stylesheet ?>">
		<?php endforeach ?>

		<?= View::stack('styles') ?>

		<?= Theme::theme()->analytics() ?>

		<?= DebugBar::renderHead() ?>
	</head>

	<body class="wt-global">
		<header class="wt-header-wrapper d-print-none">
			<div class="container wt-header-container">
				<div class="row wt-header-content">
					<?= Theme::theme()->headerContent() ?>
				</div>
			</div>
		</header>

		<main id="content" class="wt-main-wrapper">
			<div class="container wt-main-container">
				<?= Theme::theme()->flashMessagesContainer(FlashMessages::getMessages()) ?>

				<?= $content ?>
			</div>
		</main>

		<footer class="wt-footer-container">
			<div class="wt-footer-content container d-print-none">
				<?= Theme::theme()->formatContactLinks() ?>
				<?= Theme::theme()->logoPoweredBy() ?>
				<?= Theme::theme()->formatPageViews(123) ?>
				<?= Theme::theme()->cookieWarning()?>
			</div>
		</footer>

		<script src="<?= e(WT_ASSETS_URL . 'js/vendor.js') ?>"></script>
		<script src="<?= e(WT_ASSETS_URL . 'js/webtrees.js') ?>"></script>

		<?= View::stack('javascript') ?>

		<?= Theme::theme()->hookFooterExtraJavascript() ?>

		<?= DebugBar::render() ?>
	</body>
</html>
