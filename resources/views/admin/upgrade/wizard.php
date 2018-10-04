<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), $title]]) ?>

<h1><?= $title ?></h1>

<?php if ($latest_version === '') : ?>
    <div class="alert alert-warning">
        <?= I18N::translate('No upgrade information is available.') ?>
    </div>
<?php elseif (version_compare($current_version, $latest_version) >= 0) : ?>
    <div class="alert alert-info">
        <?= I18N::translate('This is the latest version of webtrees. No upgrade is available.') ?>
    </div>
<?php else : ?>
    <p>
        <?= I18N::translate('A new version of webtrees is available.') ?>
    </p>

    <p>
        <?= I18N::translate('Depending on your server configuration, you may be able to upgrade automatically.') ?>
    </p>

    <form class="form-horizontal">
        <input type="hidden" name="route" value="upgrade">

        <button type="submit" class="btn btn-primary" name="continue" value="1">
            <?= I18N::translate('continue') ?>
        </button>
    </form>
<?php endif ?>
