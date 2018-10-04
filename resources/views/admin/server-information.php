<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<h2>
    <?= I18N::translate('PHP information') ?>
</h2>

<div class="php-info" dir="ltr">
    <?= $phpinfo ?>
</div>


<h2>
    <?= I18N::translate('MySQL variables') ?>
</h2>
<dl>
    <?php foreach ($mysql_variables as $variable => $value) : ?>
        <dt><?= e($variable) ?></dt>
        <dd><?= e($value) ?></dd>
    <?php endforeach ?>
</dl>
