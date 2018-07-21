<?php use Fisharebest\Webtrees\GedcomTag; ?>
<div class="label">
    <?= $tag ?>
</div>

<?php if ($url): ?>
    <a href="<?= e($url) ?>">
        <?= $name ?>
    </a>
<?php endif ?>

<?php if($value): ?>
    <span>
        <?= $value ?>
    </span>
<?php endif ?>

<div>
    <?php if($addtag): ?>
        <?= GedcomTag::getLabel('BIRT') ?>:
    <?php endif ?>
    <?= $date ?>
</div>

<?php if (!$place->isEmpty()): ?>
    <div>
        <a href="<?= e($place->getUrl()) ?>">
            <?= $place->getFullName() ?>
        </a>
    </div>
<?php endif ?>
