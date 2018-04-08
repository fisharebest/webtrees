<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\GedcomCode\GedcomCodeName; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<h2 class="wt-page-title"><?= $title ?></h2>

<form class="wt-page-content" method="post">
	<?= csrf_field() ?>
</form>

<?php View::push('javascript') ?>
<script>
  new Sortable(document.querySelector(".wt-sortable-list"), {});
</script>
<?php View::endpush() ?>
