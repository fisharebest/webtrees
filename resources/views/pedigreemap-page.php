<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-options wt-page-options-pedigreemap-chart d-print-none">
    <input type="hidden" name="route" value="pedigreemap">
    <input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">

    <div class="form-group row">
        <label class="col-sm-3 col-form-label wt-page-options-label" for="rootid">
			<?= I18N::translate('Individual') ?>
        </label>
        <div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($individual, ['id' => 'xref', 'name' => 'xref']) ?>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
        </label>
        <div class="col-sm-9 wt-page-options-value">
			<?=
			Bootstrap4::select(
				FunctionsEdit::numericOptions(range(2, $maxgenerations)),
				$generations,
				['id' => 'generations', 'name' => 'generations']
			)
			?>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-3 wt-page-options-label"></div>
        <div class="col-sm-9 wt-page-options-value">
            <input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */
			I18N::translate('view')
			?>">
        </div>
    </div>
</form>


<div class="wt-ajax-load wt-page-content wt-chart wt-pedigreemap-chart" data-ajax-url="<?= e(route('pedigreemap-chart',
	['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName(), 'generations' => $generations]
)
) ?>"></div>

<?php View::push('javascript') ?>
<script>
	'use strict';
	// Load the module's CSS files
	let link = document.createElement("link");
	link.setAttribute("rel", "stylesheet");
	link.setAttribute("type", "text/css");
	let tmp;
	<?php foreach ($assets['css_files'] as $css_file): ?>
	tmp = link.cloneNode(true);
	tmp.setAttribute("href", "<?= $css_file ?>");
	document.head.appendChild(tmp);
	<?php endforeach ?>
</script>

<?php foreach ($assets['js_files'] as $js_file): ?>
    <script src="<?= e($js_file) ?>"></script>
<?php endforeach ?>
<?php View::endpush() ?>
