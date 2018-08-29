<?php

use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;

?>

<tr>
    <th scope="row">
        <?= I18N::translate('Fact or event'); ?>
    </th>
    <td>
        <form onsubmit="if ($('#add-fact').val() === null) { event.preventDefault(); }">
            <input type="hidden" name="route" value="add-fact">
            <input type="hidden" name="xref" value="<?= e($record->getXref()); ?>">
            <input type="hidden" name="ged" value="<?= e($tree->getName()); ?>">
            <select id="add-fact" name="fact">
                <option value="" disabled selected><?= I18N::translate('&lt;select&gt;'); ?></option>
                <?php foreach ($translated_addfacts as $fact => $fact_name): ?>
                    <option value="<?= $fact; ?>"><?= $fact_name; ?></option>
                <?php endforeach; ?>
                <?php if ($type === 'INDI' || $type === 'FAM'): ?>
                    <option value="FACT"><?= I18N::translate('Custom fact'); ?></option>
                    <option value="EVEN"><?= I18N::translate('Custom event'); ?></option>
                <?php endif; ?>
            </select>
            <input type="submit" value="<?= I18N::translate('add'); ?>">
        </form>
        <span class="quickfacts">
             <?php foreach ($quickfacts as $fact): ?>
                    <a href="<?= e(
                        route('add-fact', [
                            'fact' => $fact,
                            'xref' => $record->getXref(),
                            'ged'  => $tree->getName(),
                        ])
                    ); ?>"><?= GedcomTag::getLabel($fact); ?></a>
            <?php endforeach; ?>
        </span>
    </td>
</tr>
