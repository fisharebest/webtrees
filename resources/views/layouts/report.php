<?php use Fisharebest\Webtrees\DebugBar; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<!DOCTYPE html>
<html <?= I18N::htmlAttributes() ?>>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title><?= $title ?></title>

		<link rel="icon" href="favicon.ico" type="image/x-icon">

		<?= DebugBar::renderHead() ?>
	</head>
	<body class="container wt-global wt-report-page">
		<?= $content ?>

		<?= DebugBar::render() ?>
	</body>
</html>
