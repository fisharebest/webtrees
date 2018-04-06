<?php use Fisharebest\Webtrees\DebugBar; ?>
<?php use Fisharebest\Webtrees\FlashMessages; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf" content="<?= e(csrf_token()) ?>">

		<title><?= $title ?></title>

		<link rel="icon" href="favicon.ico" type="image/x-icon">

		<?php if (I18N::direction() === 'rtl'): ?>
			<link rel="stylesheet" type="text/css" href="<?= e(WT_ASSETS_URL . 'css/vendor-rtl.css') ?>">
		<?php else: ?>
			<link rel="stylesheet" type="text/css" href="<?= e(WT_ASSETS_URL . 'css/vendor.css') ?>">
		<?php endif ?>
		<link rel="stylesheet" type="text/css" href="themes/_common/css-2.0.0/style.css">
		<link rel="stylesheet" type="text/css" href="themes/_administration/css-2.0.0/style.css">
		<?= DebugBar::renderHead() ?>
	</head>
	<body class="container wt-global">
		<header>
			<div class="wt-accessibility-links">
				<a class="sr-only sr-only-focusable btn btn-info btn-sm" href="#content">
					<?= /* I18N: Skip over the headers and menus, to the main content of the page */ I18N::translate('Skip to content') ?>
				</a>
			</div>

			<ul class="nav small d-flex justify-content-end">
				<li class="nav-item menu-mypage">
					<a class="nav-link active" href="<?= e(route('user-page')) ?>"><?= I18N::translate('My page') ?></a>
				</li>
				<li class="nav-item dropdown menu-language">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<?= I18N::translate('Language') ?>
					</a>
					<div class="dropdown-menu">
						<?php foreach (I18N::activeLocales() as $locale): ?>
							<a class="dropdown-item menu-language-<?= $locale->languageTag() ?> <?= WT_LOCALE === $locale->languageTag() ? ' active' : ''?>" data-language="<?= $locale->languageTag() ?>" href="#"><?= $locale->endonym() ?></a>
						<?php endforeach ?>
					</div>
				</li>
				<li class="nav-item menu-logout">
					<a class="nav-link" href="<?= e(route('logout')) ?>"><?= I18N::translate('Sign out') ?></a>
				</li>
			</ul>
		</header>

		<div id="content"></div>

		<?php foreach (FlashMessages::getMessages() as $message): ?>
			<div class="alert alert-<?= $message->status ?> alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="<?= I18N::translate('close') ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<?= $message->text ?>
			</div>
		<?php endforeach ?>

		<?= $content ?>

		<script src="<?= e(WT_ASSETS_URL . 'js/vendor.js') ?>"></script>
		<script src="<?= e(WT_ASSETS_URL . 'js/webtrees.js') ?>"></script>

		<?= View::stack('javascript') ?>

		<?= DebugBar::render() ?>
	</body>
</html>
