<?php use Fisharebest\Webtrees\Date; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Ramsey\Uuid\Uuid; ?>

<?php
$table_id = 'table-fam-' . Uuid::uuid4()->toString(); // lists requires a unique ID in case there are multiple lists per page
$hundred_years_ago = new Date(date('Y') - 100);
?>

<?php View::push('javascript') ?>
<script>
    $("#<?= e($table_id) ?>").dataTable( {
dom: '<"H"<"filtersH_<?= e($table_id) ?>"><"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_<?= e($table_id) ?>">>',
<?= I18N::datatablesI18N() ?>,
autoWidth: false,
processing: true,
retrieve: true,
columns: [
/* Given names         */ { type: "text" },
/* Surnames            */ { type: "text" },
/* Age                 */ { type: "num" },
/* Given names         */ { type: "text" },
/* Surnames            */ { type: "text" },
/* Age                 */ { type: "num" },
/* Marriage date       */ { type: "num" },
/* Anniversary         */ { type: "num" },
/* Marriage place      */ { type: "text" },
/* Children            */ { type: "num" },
/* Last change         */ { visible: <?= json_encode((bool) $tree->getPreference('SHOW_LAST_CHANGE')) ?> },
/* Filter marriage     */ { sortable: false },
/* Filter alive/dead   */ { sortable: false },
/* Filter tree         */ { sortable: false }
],
sorting: [[1, "asc"]],
displayLength: 20,
pagingType: "full_numbers"
})
/* Hide/show parents */
.on("click", ".btn-toggle-parents", function() {
$(this).toggleClass("ui-state-active");
$(".parents", $(this).closest("table").DataTable().rows().nodes()).slideToggle();
})
/* Hide/show statistics */
.on("click",  ".btn-toggle-statistics", function() {
$(this).toggleClass("ui-state-active");
$("#family-charts-<?= e($table_id) ?>").slideToggle();
})
/* Filter buttons in table header */
.on("click", "button[data-filter-column]", function() {
var btn = $(this);
// De-activate the other buttons in this button group
btn.siblings().removeClass("active");
// Apply (or clear) this filter
var col = $("#<?= e($table_id) ?>").DataTable().column(btn.data("filter-column"));
if (btn.hasClass("active")) {
col.search("").draw();
} else {
col.search(btn.data("filter-value")).draw();
}
});
</script>
<?php View::endpush() ?>

<?php
$max_age = (int) $tree->getPreference('MAX_ALIVE_AGE');

// init chart data
$marr_by_age = [];
for ($age = 0; $age <= $max_age; $age++) {
    $marr_by_age[$age] = '';
}
$birt_by_decade = [];
$marr_by_decade = [];
for ($year = 1550; $year < 2030; $year += 10) {
    $birt_by_decade[$year] = '';
    $marr_by_decade[$year] = '';
}
?>

<div class="fam-list">
    <table id="<?= e($table_id) ?>">
        <thead>
            <tr>
                <th colspan="14">
                    <div class="btn-toolbar d-flex justify-content-between mb-2">
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="N"
                                title="' . I18N::translate('Show individuals who are alive or couples where both partners are alive.') ?>"
                            >
                                <?= I18N::translate('Both alive') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="W"
                                title="<?= I18N::translate('Show couples where only the female partner is dead.') ?>"
                            >
                                <?= I18N::translate('Widower') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="H"
                                title="<?= I18N::translate('Show couples where only the male partner is dead.') ?>"
                            >
                                <?= I18N::translate('Widow') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="Y"
                                title="<?= I18N::translate('Show individuals who are dead or couples where both partners are dead.') ?>"
                            >
                                <?= I18N::translate('Both dead') ?>
                            </button>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="13"
                                data-filter-value="R"
                                title="<?= I18N::translate('Show “roots” couples or individuals. These individuals may also be called “patriarchs”. They are individuals who have no parents recorded in the database.') ?>"
                            >
                                <?= I18N::translate('Roots') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="13"
                                data-filter-value="L"
                                title="<?= I18N::translate('Show “leaves” couples or individuals. These are individuals who are alive but have no children recorded in the database.') ?>"
                            >
                                <?= I18N::translate('Leaves') ?>
                            </button>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="11"
                                data-filter-value="U"
                                title="<?= I18N::translate('Show couples with an unknown marriage date.') ?>"
                            >
                                <?= I18N::translate('Marriage') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="11"
                                data-filter-value="YES"
                                title="<?= I18N::translate('Show couples who married more than 100 years ago.') ?>"
                            >
                                <?= I18N::translate('Marriage') ?>&gt;100
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="11"
                                data-filter-value="Y100"
                                title="<?= I18N::translate('Show couples who married within the last 100 years.') ?>"
                            >
                                <?= I18N::translate('Marriage') ?>&lt;=100
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="11"
                                data-filter-value="D"
                                title="<?= I18N::translate('Show divorced couples.') ?>"
                            >
                                <?= I18N::translate('Divorce') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="11"
                                data-filter-value="M"
                                title="<?= I18N::translate('Show couples where either partner married more than once.') ?>"
                            >
                                <?= I18N::translate('Multiple marriages') ?>
                            </button>
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th><?= I18N::translate('Given names') ?></th>
                <th><?= I18N::translate('Surname') ?></th>
                <th><?= I18N::translate('Age') ?></th>
                <th><?= I18N::translate('Given names') ?></th>
                <th><?= I18N::translate('Surname') ?></th>
                <th><?= I18N::translate('Age') ?></th>
                <th><?= I18N::translate('Marriage') ?></th>
                <th><i class="icon-reminder" title="<?= I18N::translate('Anniversary') ?>"></i></th>
                <th><?= I18N::translate('Place') ?></th>
                <th><i class="icon-children" title="<?= I18N::translate('Children') ?>"></i></th>
                <th><?= I18N::translate('Last change') ?></th>
                <th hidden></th>
                <th hidden></th>
                <th hidden></th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th colspan="14">
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <button class="ui-state-default btn-toggle-parents">
                                <?= I18N::translate('Show parents') ?>
                            </button>
                            <button class="ui-state-default btn-toggle-statistics">
                                <?= I18N::translate('Show statistics charts') ?>
                            </button>
                        </div>
                    </div>
                </th>
            </tr>
        </tfoot>
        <tbody>

        <?php foreach ($families as $family) : ?>
            <?php $husb = $family->getHusband() ?? new Individual('H', '0 @H@ INDI', null, $family->getTree()) ?>
            <?php $wife = $family->getWife() ?? new Individual('W', '0 @W@ INDI', null, $family->getTree()) ?>

            <tr class="<?= $family->isPendingDeletion() ? 'old' : ($family->isPendingAddition() ? 'new' : '') ?>">
                <!-- Husband name -->
                <td colspan="2" data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', implode(',', array_reverse(explode(',', $husb->getSortName()))))) ?>">
                    <?php foreach ($husb->getAllNames() as $num => $name) : ?>
                        <?php if ($name['type'] != '_MARNM' || $num == $husb->getPrimaryName()) : ?>
                        <a title="<?= $name['type'] === 'NAME' ? '' : GedcomTag::getLabel($name['type'], $husb) ?>" href="<?= e($family->url()) ?>" class="<?= $num === $husb->getPrimaryName() ? 'name2' : '' ?>">
                            <?= $name['full'] ?>
                        </a>
                            <?php if ($num === $husb->getPrimaryName()) : ?>
                                <?= $husb->getSexImage() ?>
                            <?php endif ?>
                        <br>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?= $husb->getPrimaryParentsNames('parents details1', 'none') ?>
                </td>

                <td hidden data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', $husb->getSortName())) ?>"></td>

                <!-- Husband age -->
                <?php
                $mdate = $family->getMarriageDate();
                $hdate = $husb->getBirthDate();
                if ($hdate->isOK() && $mdate->isOK()) {
                    if ($hdate->gregorianYear() >= 1550 && $hdate->gregorianYear() < 2030) {
                        $birt_by_decade[(int) ($hdate->gregorianYear() / 10) * 10] .= $husb->getSex();
                    }
                    $hage = Date::getAgeYears($hdate, $mdate);
                    if ($hage >= 0 && $hage <= $max_age) {
                        $marr_by_age[$hage] .= $husb->getSex();
                    }
                }
                ?>
                <td class="center" data-sort="<?= Date::getAgeDays($hdate, $mdate) ?>">
                    <?= Date::getAge($hdate, $mdate) ?>
                </td>

                <!-- Wife name -->
                <td colspan="2" data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', implode(',', array_reverse(explode(',', $wife->getSortName()))))) ?>">
                    <?php foreach ($wife->getAllNames() as $num => $name) : ?>
                        <?php if ($name['type'] != '_MARNM' || $num == $wife->getPrimaryName()) : ?>
                            <a title="<?= $name['type'] === 'NAME' ? '' : GedcomTag::getLabel($name['type'], $wife) ?>" href="<?= e($family->url()) ?>" class="<?= $num === $wife->getPrimaryName() ? 'name2' : '' ?>">
                                <?= $name['full'] ?>
                            </a>
                            <?php if ($num === $wife->getPrimaryName()) : ?>
                                <?= $wife->getSexImage() ?>
                            <?php endif ?>
                            <br>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?= $wife->getPrimaryParentsNames('parents details1', 'none') ?>
                </td>

                <td hidden data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', $wife->getSortName())) ?>"></td>

                <!-- Wife age -->
                <?php
                $wdate = $wife->getBirthDate();
                if ($wdate->isOK() && $mdate->isOK()) {
                    if ($wdate->gregorianYear() >= 1550 && $wdate->gregorianYear() < 2030) {
                        $birt_by_decade[(int) ($wdate->gregorianYear() / 10) * 10] .= $wife->getSex();
                    }
                    $wage = Date::getAgeYears($wdate, $mdate);
                    if ($wage >= 0 && $wage <= $max_age) {
                        $marr_by_age[$wage] .= $wife->getSex();
                    }
                }
                ?>

                <td class="center" data-sort="<?= Date::getAgeDays($wdate, $mdate) ?>">
                    <?= Date::getAge($wdate, $mdate) ?>
                </td>

                <!-- Marriage date -->
                <td data-sort="<?= $family->getMarriageDate()->julianDay() ?>">
                    <?php if ($marriage_dates = $family->getAllMarriageDates()) : ?>
                        <?php foreach ($marriage_dates as $n => $marriage_date) : ?>
                            <div><?= $marriage_date->display(true) ?></div>
                        <?php endforeach ?>
                        <?php if ($marriage_dates[0]->gregorianYear() >= 1550 && $marriage_dates[0]->gregorianYear() < 2030) : ?>
                            <?php $marr_by_decade[(int) ($marriage_dates[0]->gregorianYear() / 10) * 10] .= $husb->getSex() . $wife->getSex() ?>
                        <?php endif ?>
                    <?php elseif ($family->getFacts('_NMR')) : ?>
                        <?= I18N::translate('no') ?>
                    <?php elseif ($family->getFacts('MARR')) : ?>
                            <?= I18N::translate('yes') ?>
                    <?php endif ?>
                </td>

                <!-- Marriage anniversary -->
                <td class="center" data-sort="<?= -$family->getMarriageDate()->julianDay() ?>">
                    <?= Date::getAge($family->getMarriageDate(), null) ?>
                </td>

                <!-- Marriage place -->
                <td>
                    <?php foreach ($family->getAllMarriagePlaces() as $n => $marriage_place) : ?>
                        <a href="<?= $marriage_place->getURL() ?>" title="<?= strip_tags($marriage_place->getFullName()) ?>">
                            <?= $marriage_place->getShortName() ?>
                        </a>
                        <br>
                    <?php endforeach ?>
                </td>

                <!-- Number of children -->
                <td class="center" data-sort="<?= $family->getNumberOfChildren() ?>">
                    <?= I18N::number($family->getNumberOfChildren()) ?>
                </td>

                <!-- Last change -->
                <td data-sort="<?= $family->lastChangeTimestamp(true) ?>">
                    <?= $family->lastChangeTimestamp() ?>
                </td>

                <!-- Filter by marriage date -->
                <td hidden>
                    <?php if (!$family->canShow() || !$mdate->isOK()) : ?>
                        U
                    <?php elseif (Date::compare($mdate, $hundred_years_ago) > 0) : ?>
                        Y100
                    <?php else : ?>
                        YES
                    <?php endif ?>
                    <?php if ($family->getFacts(WT_EVENTS_DIV)) : ?>
                        D
                    <?php endif ?>
                    <?php if (count($husb->getSpouseFamilies()) > 1 || count($wife->getSpouseFamilies()) > 1) : ?>
                        M
                    <?php endif ?>
                </td>

                <!-- Filter by alive/dead -->
                <td hidden>
                    <?php if ($husb->isDead() && $wife->isDead()) : ?>
                        Y
                    <?php endif ?>
                    <?php if ($husb->isDead() && !$wife->isDead()) : ?>
                        <?php if ($wife->getSex() == 'F') : ?>
                            H
                        <?php endif ?>
                        <?php if ($wife->getSex() == 'M') : ?>
                            W
                        <?php endif ?>
                    <?php endif ?>
                    <?php if (!$husb->isDead() && $wife->isDead()) : ?>
                        <?php if ($husb->getSex() == 'M') : ?>
                            W
                        <?php endif ?>
                        <?php if ($husb->getSex() == 'F') : ?>
                            H
                        <?php endif ?>
                    <?php endif ?>
                    <?php if (!$husb->isDead() && !$wife->isDead()) : ?>
                        N
                    <?php endif ?>
                </td>

                <!-- Filter by roots/leaves -->
                <td hidden>
                    <?php if (!$husb->getChildFamilies() && !$wife->getChildFamilies()) : ?>
                        R
                    <?php elseif (!$husb->isDead() && !$wife->isDead() && $family->getNumberOfChildren() === 0) : ?>
                        L
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <div id="family-charts-<?= e($table_id) ?>" style="display:none">
        <table class="list-charts">
            <tr>
                <td>
                    <?= view('lists/chart-by-decade', ['data' => $birt_by_decade, 'title' => I18N::translate('Decade of birth')]) ?>
        </td>
        <td>
                    <?= view('lists/chart-by-decade', ['data' => $marr_by_decade, 'title' => I18N::translate('Decade of marriage')]) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?= view('lists/chart-by-age', ['data' => $marr_by_age, 'title' => I18N::translate('Age in year of marriage')]) ?>
                </td>
            </tr>
        </table>
    </div>
</div>
