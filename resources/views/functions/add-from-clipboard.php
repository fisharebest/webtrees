<?php

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;

?>

<?php $newRow = true; ?>
<?php foreach ($clipboard_data as $fact_id => $fact): ?>
    <?php if ($fact['type'] === $type || $fact['type'] === 'all'): ?>
        <?php if ($newRow): ?>
            <?php $newRow = false; ?>
            <tr>
                <th scope="row">
                    <?= I18N::translate('Add from clipboard'); ?>
                </th>
                <td>
                    <form name="newFromClipboard" onsubmit="return false;">
                        <select id="newClipboardFact">
        <?php endif; ?>

        <option value="<?= e($fact_id); ?>">
            <?php
                $label = GedcomTag::getLabel($fact['fact']);

                // TODO use the event class to store/parse the clipboard events
                if (preg_match('/^2 DATE (.+)/m', $fact['factrec'], $match)):
                    $label .= '; ' . (new Date($match[1]))->minimumDate()->format('%Y');
                endif;

                if (preg_match('/^2 PLAC ([^,\n]+)/m', $fact['factrec'], $match)):
                    $label .= '; ' . $match[1];
                endif;
            ?>
            <?= $label; ?>
        </option>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!$newRow): ?>
                </select>
                <input type="button"
                       value="<?= I18N::translate('add'); ?>"
                       onclick="return paste_fact('<?= e($tree->getName()); ?>', '<?= e($record->getXref()); ?>', '#newClipboardFact');">
            </form>
        </td>
    </tr>
<?php endif; ?>
