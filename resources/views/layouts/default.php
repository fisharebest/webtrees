<?php use Fisharebest\Webtrees\Auth; ?>
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
					<div class="wt-accessibility-links">
						<a class="sr-only sr-only-focusable btn btn-info btn-sm" href="#content">
							<?= /* I18N: Skip over the headers and menus, to the main content of the page */ I18N::translate('Skip to content') ?>
						</a>
					</div>
					<div class="col wt-site-logo"></div>

					<?php if ($tree !== null): ?>
						<h1 class="col wt-site-title"><?= e($tree->getTitle()) ?></h1>

						<div class="col wt-header-search">
							<form class="wt-header-search-form" role="search">
								<input type="hidden" name="route" value="search-quick">
								<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
								<div class="input-group">
									<label class="sr-only" for="quick-search"><?= I18N::translate('Search') ?></label>
									<input type="search" class="form-control wt-header-search-field" id="quick-search" name="query" size="15" placeholder="<?= I18N::translate('Search') ?>">
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary wt-header-search-button">
											<i class="fas fa-search"></i>
										</button>
									</span>
								</div>
							</form>
						</div>
					<?php endif ?>

					<div class="col wt-secondary-navigation">
						<ul class="nav wt-secondary-menu">
							<?php foreach (Theme::theme()->secondaryMenu() as $menu): ?>
								<?= $menu->bootstrap4() ?>
							<?php endforeach ?>
						</ul>
					</div>

					<?php if ($tree !== null): ?>
					<nav class="col wt-primary-navigation">
						<ul class="nav wt-primary-menu">
							<?php foreach (Theme::theme()->primaryMenu($individual ?? $tree->significantIndividual(Auth::user())) as $menu): ?>
								<?= $menu->bootstrap4() ?>
							<?php endforeach ?>
						</ul>
					</nav>
					<?php endif ?>
				</div>
			</div>
		</header>

		<main id="content" class="wt-main-wrapper">
			<div class="container wt-main-container">
				<div class="flash-messages">
					<?php foreach (FlashMessages::getMessages() as $message): ?>
						<div class="alert alert-<?= e($message->status) ?> alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="<?= I18N::translate('close') ?>">
								<span aria-hidden="true">&times;</span>
							</button>
							<?= $message->text ?>
						</div>
					<?php endforeach ?>
				</div>

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

		<script>
      activate_colorbox();
      jQuery.extend(jQuery.colorbox.settings, {
       width: "85%",
       height: "85%",
       transition: "none",
       slideshowStart: "<?= I18N::translate('Play') ?>",
       slideshowStop: "<?= I18N::translate('Stop') ?>",
       title: function() { return this.dataset.title; }
      });
    </script>

		<?= View::stack('javascript') ?>

		<?= DebugBar::render() ?>
	</body>
</html>
