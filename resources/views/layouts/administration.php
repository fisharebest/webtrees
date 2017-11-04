<?php use Fisharebest\Webtrees\Filter; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf" content="<?= Html::escape(Filter::getCsrfToken()) ?>">

		<title><?= $title ?></title>

		<link rel="icon" type="image/png" href="<?= Html::escape($theme_url) ?>favicon.png">
		<link rel="icon" type="image/png" href="<?= Html::escape($theme_url) ?>favicon192.png" sizes="192x192">
		<link rel="apple-touch-icon" sizes="180x180" href="<?= Html::escape($theme_url) ?>favicon180.png">

		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_BOOTSTRAP_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_FONT_AWESOME_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_FONT_AWESOME_RTL_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_DATATABLES_BOOTSTRAP_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_SELECT2_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_DATATABLES_BOOTSTRAP_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape(WT_BOOTSTRAP_DATETIMEPICKER_CSS_URL) ?>">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape($common_url) ?>style.css">
		<link rel="stylesheet" type="text/css" href="<?= Html::escape($theme_url) ?>style.css">

		<?php if (I18N::direction() === 'rtl'): ?>
			<link rel="stylesheet" type="text/css" href="<?= WT_BOOTSTRAP_RTL_CSS_URL ?>">
		<?php endif ?>
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
					<a class="nav-link active" href="index.php"><?= I18N::translate('My page') ?></a>
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
					<a class="nav-link" href="logout.php"><?= I18N::translate('Sign out') ?></a>
				</li>
			</ul>
		</header>

		<div id="content"></div>
		<?= $content ?>

		<script src="<?= Html::escape(WT_JQUERY_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_POPPER_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_DATATABLES_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_DATATABLES_BOOTSTRAP_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_SELECT2_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_WEBTREES_JS_URL) ?>"></script>
		<script src="<?= Html::escape(WT_ADMIN_JS_URL) ?>"></script>
		<script><?= $javascript ?></script>
	</body>
</html>
