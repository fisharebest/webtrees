<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<dl>
	<?php foreach ($steps as $url => $text): ?>
	<dt><?= $text ?></dt>
	<dd class="wt-ajax-load" data-url="<?= e($url) ?>"></dd>
	<?php endforeach ?>
</dl>

<?php View::push('javascript') ?>
<script>
	function nextUpgradeStep() {
	  $("dd:empty:first").each(function(el) {
      $(el).load(el.dataset.url, {}, nextUpgradeStep);
    });
	}

  nextUpgradeStep();
</script>
<?php View::endpush() ?>
