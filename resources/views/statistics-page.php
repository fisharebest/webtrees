<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<div class="wt-page-content wt-chart wt-statistics-chart">
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link" href="#individual-statistics" data-toggle="tab" data-href="<?= e(route('statistics-individuals', ['ged' => $tree->getName()])) ?>" role="tab">
				<?= I18N::translate('Individuals') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#family-statistics" data-toggle="tab" data-href="<?= e(route('statistics-families', ['ged' => $tree->getName()])) ?>" role="tab">
				<?= I18N::translate('Families') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#other-statistics" data-toggle="tab" data-href="<?= e(route('statistics-other', ['ged' => $tree->getName()])) ?>" role="tab">
				<?= I18N::translate('Others') ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#custom-statistics" data-toggle="tab" data-href="<?= e(route('statistics-options', ['ged' => $tree->getName()])) ?>" role="tab">
				<?= I18N::translate('Own charts') ?>
			</a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="individual-statistics"></div>
		<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="family-statistics"></div>
		<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="other-statistics"></div>
		<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="custom-statistics"></div>
	</div>
</div>

<?php View::push('javascript') ?>
<script>
	$(function () {
    $("a[data-toggle=tab]:first").tab("show");
	});
</script>
<?php View::endpush() ?>
