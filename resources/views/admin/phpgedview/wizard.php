<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-horizontal">
    <input type="hidden" name="route" value="phpgedview-wizard">
    <?php if (!empty($pgv_paths)) : ?>
        <div class="row form-group">
            <div class="col-sm-3 col-form-label">
                <?= I18N::translate('PhpGedView might be installed in one of these folders:') ?>
            </div>
            <div class="col-sm-9">
                <?php foreach ($pgv_paths as $pgv_path) : ?>
                    <div>
                        <a href="#" onclick="document.getElementById('pgv_path').value=this.dataset.path; return false;" data-path="<?= e($pgv_path) ?>">
                            <?= e($pgv_path) ?>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <div class="row form-group">
        <label class="col-sm-3 col-form-label" for="pgv_path">
            <?= I18N::translate('Where is your PhpGedView installation?') ?>
        </label>
        <div class="col-sm-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= e(WT_ROOT) ?></span>
                </div>
                <input
                    type="text"
                    class="form-control"
                    dir="ltr"
                    id="pgv_path"
                    name="pgv_path"
                    size="40"
                    placeholder="<?= I18N::translate('Installation folder') ?>"
                    value="<?= count($pgv_paths) === 1 ? $pgv_paths[0] : '' ?>"
                    required
                >
                <div class="input-group-append">
                    <span class="input-group-text">config.php</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="offset-sm-3 col-sm-9">
            <button type="submit" class="btn btn-primary">
                <?= I18N::translate('continue') ?>
            </button>
        </div>
    </div>
</form>
