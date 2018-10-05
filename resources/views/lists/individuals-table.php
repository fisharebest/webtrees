<?php use Fisharebest\Webtrees\Date; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\View; ?>
<?php use Ramsey\Uuid\Uuid; ?>


<?php
// lists requires a unique ID in case there are multiple lists per page
$table_id = 'table-indi-' . Uuid::uuid4()->toString();

$hundred_years_ago = new Date(date('Y') - 100);
$unique_indis      = []; // Don't double-count indis with multiple names.
?>

<?php View::push('javascript') ?>
<script>
$("#<?= e($table_id) ?>").dataTable( {
    dom: '<"H"<"filtersH_<?= e($table_id) ?>">T<"dt-clear">pf<"dt-clear">irl>t<"F"pl<"dt-clear"><"filtersF_<?= e($table_id) ?>">>',
    <?= I18N::datatablesI18N() ?>,
    autoWidth: false,
    processing: true,
    retrieve: true,
    columns: [
        /* Given names  */ { type: "text" },
        /* Surnames     */ { type: "text" },
        /* SOSA numnber */ { type: "num", visible: <?= json_encode($sosa) ?> },
        /* Birth date   */ { type: "num" },
        /* Anniversary  */ { type: "num" },
        /* Birthplace   */ { type: "text" },
        /* Children     */ { type: "num" },
        /* Deate date   */ { type: "num" },
        /* Anniversary  */ { type: "num" },
        /* Age          */ { type: "num" },
        /* Death place  */ { type: "text" },
        /* Last change  */ { visible: <?= json_encode($tree->getPreference('SHOW_LAST_CHANGE')) ?> },
        /* Filter sex   */ { sortable: false },
        /* Filter birth */ { sortable: false },
        /* Filter death */ { sortable: false },
        /* Filter tree  */ { sortable: false }
    ],
    sorting: <?= json_encode($sosa ? [[4, "asc"]] : [[1, "asc"]]) ?>,
    displayLength: 20,
    pagingType: "full_numbers"
});

$("#<?= e($table_id) ?>")
/* Hide/show parents */
.on("click", ".btn-toggle-parents", function() {
    $(this).toggleClass("ui-state-active");
    $(".parents", $(this).closest("table").DataTable().rows().nodes()).slideToggle();
})
/* Hide/show statistics */
.on("click", ".btn-toggle-statistics", function() {
    $(this).toggleClass("ui-state-active");
    $("#individual-charts-<?= e($table_id) ?>").slideToggle();
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

// Inititialise chart data
$deat_by_age = [];
for ($age = 0; $age <= $max_age; $age++) {
    $deat_by_age[$age] = '';
}
$birt_by_decade = [];
$deat_by_decade = [];
for ($year = 1550; $year < 2030; $year += 10) {
    $birt_by_decade[$year] = '';
    $deat_by_decade[$year] = '';
}
?>

<div class="indi-list">
    <table id="<?= e($table_id) ?>">
        <thead>
            <tr>
                <th colspan="16">
                    <div class="btn-toolbar d-flex justify-content-between mb-2" role="toolbar">
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="M"
                                title="<?= I18N::translate('Show only males.') ?>"
                            >
                                <?= Individual::sexImage('M', 'large') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="F"
                                title="<?= I18N::translate('Show only females.') ?>"
                            >
                                <?= Individual::sexImage('F', 'large') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="12"
                                data-filter-value="U"
                                title="<?= I18N::translate('Show only individuals for whom the gender is not known.') ?>"
                            >
                                <?= Individual::sexImage('U', 'large') ?>
                            </button>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="14"
                                data-filter-value="N"
                                title="<?= I18N::translate('Show individuals who are alive or couples where both partners are alive.') ?>"
                            >
                                <?= I18N::translate('Alive') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="14"
                                data-filter-value="Y"
                                title="<?= I18N::translate('Show individuals who are dead or couples where both partners are dead.') ?>"
                            >
                                <?= I18N::translate('Dead') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="14"
                                data-filter-value="YES"
                                title="<?= I18N::translate('Show individuals who died more than 100 years ago.') ?>"
                            >
                                <?= I18N::translate('Death') ?>&gt;100
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="14"
                                data-filter-value="Y100"
                                title="<?= I18N::translate('Show individuals who died within the last 100 years.') ?>"
                            >
                                <?= I18N::translate('Death') ?>&lt&lt;=100
                            </button>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="13"
                                data-filter-value="YES"
                                title="<?= I18N::translate('Show individuals born more than 100 years ago.') ?>"
                            >
                                <?= I18N::translate('Birth') ?>&gt;100
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="13"
                                data-filter-value="Y100"
                                title="<?= I18N::translate('Show individuals born within the last 100 years.') ?>"
                            >
                                <?= I18N::translate('Birth') ?>&lt;=100
                            </button>
                        </div>
                        <div class="btn-group" data-toggle="buttons">
                            <button
                                class="btn btn-secondary"
                                data-filter-column="15"
                                data-filter-value="R"
                                title="<?= I18N::translate('Show “roots” couples or individuals. These individuals may also be called “patriarchs”. They are individuals who have no parents recorded in the database.') ?>"
                            >
                                <?= I18N::translate('Roots') ?>
                            </button>
                            <button
                                class="btn btn-secondary"
                                data-filter-column="15"
                                data-filter-value="L"
                                title="<?= I18N::translate('Show “leaves” couples or individuals. These are individuals who are alive but have no children recorded in the database.') ?>"
                            >
                                <?= I18N::translate('Leaves') ?>
                            </button>
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th><?= I18N::translate('Given names') ?></th>
                <th><?= I18N::translate('Surname') ?></th>
                <th><?= /* I18N: Abbreviation for “Sosa-Stradonitz number”. This is an individual’s surname, so may need transliterating into non-latin alphabets. */
                    I18N::translate('Sosa') ?></th>
                <th><?= I18N::translate('Birth') ?></th>
                <th>
                    <i class="icon-reminder" title="<?= I18N::translate('Anniversary') ?>"></i>
                </th>
                <th><?= I18N::translate('Place') ?></th>
                <th>
                    <i class="icon-children" title="<?= I18N::translate('Children') ?>"></i>
                </th>
                <th><?= I18N::translate('Death') ?></th>
                <th>
                    <i class="icon-reminder" title="<?= I18N::translate('Anniversary') ?>"></i>
                </th>
                <th><?= I18N::translate('Age') ?></th>
                <th><?= I18N::translate('Place') ?></th>
                <th><?= I18N::translate('Last change') ?></th>
                <th hidden></th>
                <th hidden></th>
                <th hidden></th>
                <th hidden></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="16">
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
            <?php foreach ($individuals as $key => $individual) : ?>
            <tr class="<?= $individual->isPendingDeletion() ? 'old' : ($individual->isPendingAddition() ? 'new' : '') ?>">
                <td colspan="2" data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', implode(',', array_reverse(explode(',', $individual->getSortName()))))) ?>">
                    <?php foreach ($individual->getAllNames() as $num => $name) : ?>
                        <a title="<?= $name['type'] === 'NAME' ? '' : GedcomTag::getLabel($name['type'], $individual) ?>" href="<?= e($individual->url()) ?>" class="<?= $num === $individual->getPrimaryName() ? 'name2' : '' ?>">
                            <?= $name['full'] ?>
                        </a>
                        <?php if ($num === $individual->getPrimaryName()) : ?>
                            <?= $individual->getSexImage() ?>
                        <?php endif ?>
                        <br>
                    <?php endforeach ?>
                    <?= $individual->getPrimaryParentsNames('parents details1', 'none') ?>
                </td>

                <td hidden data-sort="<?= e(str_replace([',', '@P.N.', '@N.N.'], 'AAAA', $individual->getSortName())) ?>"></td>

                <td class="center" data-sort="<?= $key ?>">
                    <?php if ($sosa) : ?>
                        <a href="<?= e(route('relationships', ['xref1' => $individuals[1]->getXref(), 'xref2' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])) ?>" title="<?= I18N::translate('Relationships') ?>" rel="nofollow">
                            <?= I18N::number($key) ?>
                        </a>
                    <?php endif ?>
                </td>

                <!-- Birth date -->
                <td data-sort="<?= $individual->getEstimatedBirthDate()->julianDay() ?>">
                    <?php $birth_dates = $individual->getAllBirthDates(); ?>

                    <?php foreach ($birth_dates as $n => $birth_date) : ?>
                        <?= $birth_date->display(true) ?>
                        <br>
                    <?php endforeach ?>
                </td>

                <!-- Birth anniversary -->
                <td class="center" data-sort="<?= -$individual->getEstimatedBirthDate()->julianDay() ?>">
                    <?php if (isset($birth_dates[0]) && $birth_dates[0]->gregorianYear() >= 1550 && $birth_dates[0]->gregorianYear() < 2030 && !isset($unique_indis[$individual->getXref()])) : ?>
                        <?php $birt_by_decade[(int) ($birth_dates[0]->gregorianYear() / 10) * 10] .= $individual->getSex() ?>
                        <?= Date::getAge($birth_dates[0], null) ?>
                    <?php endif ?>
                </td>

                <!-- Birth place -->
                <td>
                    <?php foreach ($individual->getAllBirthPlaces() as $n => $birth_place) : ?>
                        <a href="<?= $birth_place->getURL() ?>" title="<?= strip_tags($birth_place->getFullName()) ?>">
                            <?= $birth_place->getShortName() ?>
                        </a>
                        <br>
                    <?php endforeach ?>
                </td>

                <!-- Number of children -->
                <td class="center" data-sort="<?= $individual->getNumberOfChildren() ?>">
                    <?= I18N::number($individual->getNumberOfChildren()) ?>
                </td>

                <!--    Death date -->
                <?php $death_dates = $individual->getAllDeathDates() ?>
                <td data-sort="<?= $individual->getEstimatedDeathDate()->julianDay() ?>">
                    <?php foreach ($death_dates as $num => $death_date) : ?>
                        <?= $death_date->display(true) ?>
                    <br>
                    <?php endforeach ?>
                </td>

                <!-- Death anniversary -->
                <td class="center" data-sort="<?= -$individual->getEstimatedDeathDate()->julianDay() ?>">
                    <?php if (isset($death_dates[0]) && $death_dates[0]->gregorianYear() >= 1550 && $death_dates[0]->gregorianYear() < 2030 && !isset($unique_indis[$individual->getXref()])) : ?>
                        <?php $deat_by_decade[(int) ($death_dates[0]->gregorianYear() / 10) * 10] .= $individual->getSex() ?>
                        <?= Date::getAge($death_dates[0], null) ?>
                    <?php endif ?>
                </td>

                <!-- Age at death -->
                <?php if (isset($birth_dates[0]) && isset($death_dates[0])) : ?>
                    <?php $age_at_death = I18N::number((int) Date::getAgeYears($birth_dates[0], $death_dates[0])); ?>
                    <?php $age_at_death_sort = Date::getAge($birth_dates[0], $death_dates[0]); ?>
                    <?php if (!isset($unique_indis[$individual->getXref()]) && $age_at_death >= 0 && $age_at_death <= $max_age) : ?>
                        <?php $deat_by_age[$age_at_death] .= $individual->getSex(); ?>
                    <?php endif ?>
                <?php else : ?>
                    <?php $age_at_death = ''; ?>
                    <?php $age_at_death_sort = PHP_INT_MAX; ?>
                <?php endif ?>
                <td class="center" data-sort="<?= e($age_at_death_sort) ?>">
                    <?= e($age_at_death) ?>
                </td>

                <!-- Death place -->
                <td>
                    <?php foreach ($individual->getAllDeathPlaces() as $n => $death_place) : ?>
                        <a href="<?= $death_place->getURL() ?>" title="<?= e(strip_tags($death_place->getFullName())) ?>">
                            <?= $death_place->getShortName() ?>
                        </a>
                        <br>
                    <?php endforeach ?>
                </td>

                <!-- Last change -->
                <td data-sort="<?= $individual->lastChangeTimestamp(true) ?>">
                    <?= $individual->lastChangeTimestamp() ?>
                </td>

                <!-- Filter by sex -->
                <td hidden>
                    <?= $individual->getSex() ?>
                </td>

                <!-- Filter by birth date -->
                <td hidden>
                    <?php if (!$individual->canShow() || Date::compare($individual->getEstimatedBirthDate(), $hundred_years_ago) > 0) : ?>
                        Y100
                    <?php else : ?>
                        YES
                    <?php endif ?>
                </td>

                <!-- Filter by death date -->
                <td hidden>
                    <?php if (isset($death_dates[0]) && Date::compare($death_dates[0], $hundred_years_ago) > 0) : ?>
                        Y100
                    <?php elseif ($individual->isDead()) : ?>
                        YES
                    <?php else : ?>
                        N
                    <?php endif ?>
                </td>

                <!-- Filter by roots/leaves -->
                <td hidden>
                    <?php if (!$individual->getChildFamilies()) : ?>
                        R
                    <?php elseif (!$individual->isDead() && $individual->getNumberOfChildren() < 1) : ?>
                        L
                    <?php endif ?>
                </td>
            </tr>

                <?php $unique_indis[$individual->getXref()] = true ?>
            <?php endforeach ?>
        </tbody>
    </table>

    <div id="individual-charts-<?= e($table_id) ?>" style="display:none">
        <table class="list-charts">
            <tr>
                <td>
                    <?= view('lists/chart-by-decade', ['data' => $birt_by_decade, 'title' => I18N::translate('Decade of birth')]) ?>
                </td>
                <td>
                    <?= view('lists/chart-by-decade', ['data' => $deat_by_decade, 'title' => I18N::translate('Decade of death')]) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?= view('lists/chart-by-age', ['data' => $deat_by_age, 'title' => I18N::translate('Age related to death year')]) ?>
                </td>
            </tr>
        </table>
    </div>
</div>
