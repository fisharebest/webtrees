<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<form action="<?= e(route('link-media-to-record')) ?>" id="wt-modal-form" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
    <input type="hidden" name="xref" value="<?= e($media->getXref()) ?>">

    <?= view('modals/header', ['title' => I18N::translate('Link this media object to a family')]) ?>

    <div class="modal-body">
        <div class="form-group">
            <label class="col-form-label" for="link">
                <?= I18N::translate('Family') ?>
            </label>
            <?= FunctionsEdit::formControlFamily($tree, null, ['id' => 'link', 'name' => 'link']) ?>
        </div>
    </div>

    <?= view('modals/footer-save-cancel') ?>
</form>

