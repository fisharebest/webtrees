<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<dl>
	<?php foreach ($steps as $url => $text): ?>
	<dt><?= $text ?></dt>
	<dd class="wt-ajax-load" data-url="<?= e($url) ?>"></dd>
	<?php endforeach ?>
</dl>

<?php View::push('javascript') ?>
<script>
  function nextAjaxStep() {
    $("dd:empty:first").each(function(n, el) {
      $(el).load(el.dataset.url, {}, function (responseText, textStatus, req) {
        el.innerHTML = responseText;
        if (textStatus === "error") {
          $(".wt-ajax-load").removeClass("wt-ajax-load");
        } else {
          nextAjaxStep();
        }
      });

      // Only process one callback at a time.
      return false;
    });
  }

  nextAjaxStep();
</script>
<?php View::endpush() ?>
