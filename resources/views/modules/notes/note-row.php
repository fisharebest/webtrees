<?php

use Fisharebest\Webtrees\Fact;

/**
 * @var Fact $fact
 */

$gedcom    = preg_replace('/\n\d CONT ?/', "\r", $fact->gedcom());
$hierarchy = explode(':', $fact->tag());

?>

<?php if (str_ends_with($fact->tag(), ':NOTE')) : ?>

<?php endif ?>

<?php foreach ([2 => 3, 3 => 4, 4 => 5] as $level => $sublevel) : ?>
    <?php preg_match_all('/\n' . $level . ' NOTE ?(.*(\n' . $sublevel . ' CONT.*)*(\n' . $sublevel . ' MIME (.+))?/', $fact->gedcom(), $matches) ?>
    <?php foreach ($matches as $match) : ?>
        <tr>
            <td>

            </td>
            <td>
                <?php $text = '' ?>
            </td>
        </tr>
    <?php endforeach ?>
<?php endforeach ?>
