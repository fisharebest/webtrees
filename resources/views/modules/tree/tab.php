<?php use Fisharebest\Webtrees\View; ?>

<div class="wt-tree-tab py-4">
	<?= $html ?>
</div>

<?php View::push('javascript') ?>
<script src="<?= e($treeview_js) ?>"></script>

<script>
  var newSheet=document.createElement("link");
  newSheet.setAttribute("rel","stylesheet");
  newSheet.setAttribute("type","text/css");
  newSheet.setAttribute("href","<?= $treeview_css ?>");
  document.head.appendChild(newSheet);

  <?= $js ?>
</script>
<?php View::endpush() ?>
