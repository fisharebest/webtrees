<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>
<?php use Fisharebest\Webtrees\View; ?>

<form method="post" action="<?= e(Html::url('module.php' ,['mod' => 'individuals', 'mod_action' => 'ajax'])) ?>" onsubmit="return false;">
	<input type="search" name="sb_indi_name" id="sb_indi_name" placeholder="<?= I18N::translate('Search') ?>">
	<p>
		<?php foreach ($initials as $letter => $count): ?>
			<a href="<?= e(Html::url('module.php', ['mod' => 'individuals', 'mod_action' => 'ajax', 'alpha' => $letter])) ?>" class="sb_indi_letter">
				<?php switch ($letter): ?>
<?php case '@': ?>
						<?= I18N::translateContext('Unknown surname', '…') ?>
						<?php break ?>
					<?php case ',': ?>
						<?= I18N::translate('None') ?>
						<?php break ?>
					<?php case ' ': ?>
						<?= '&nbsp;' ?>
						<?php break ?>
					<?php default: ?>
						<?= e($letter) ?>
						<?php break ?>
				<?php endswitch ?>
			</a>
		<?php endforeach ?>

	</p>
	<div id="sb_indi_content"></div>
</form>

<?php View::push('javascript') ?>
<script>
  var loadedNames = [];

  function isearchQ() {
    var query = $("#sb_indi_name").val();
    if (query.length > 1) {
      $("#sb_indi_content").load("module.php?mod=individuals&mod_action=ajax&search=" + query);
    }
  }

  var timerid = null;
  $("#sb_indi_name").keyup(function (e) {
    if (timerid) window.clearTimeout(timerid);
    timerid = window.setTimeout("isearchQ()", 500);
  });
  $("#sidebar-content-individuals").on("click", ".sb_indi_letter", function () {
    $("#sb_indi_content").load(this.href);
    return false;
  });
  $("#sidebar-content-individuals").on("click", ".sb_indi_surname", function () {
    var element = $(this);
    var surname = element.data("surname");
    var alpha   = element.data("alpha");

    if (!loadedNames[surname]) {
      jQuery.ajax({
        url:     "module.php?mod=individuals&mod_action=ajax&alpha=" + encodeURIComponent(alpha) + "&surname=" + encodeURIComponent(surname),
        cache:   false,
        success: function (html) {
          $("div.name_tree_div", element.closest("li"))
            .html(html)
            .show("fast")
            .css("list-style-image", "url(<?= Theme::theme()->parameter('image-minus') ?>)");
          loadedNames[surname] = 2;
        }
      });
    } else if (loadedNames[surname] === 1) {
      loadedNames[surname] = 2;
      $("div.name_tree_div", $(this).closest("li"))
        .show()
        .css("list-style-image", "url(<?= Theme::theme()->parameter('image-minus') ?>)");
    } else {
      loadedNames[surname] = 1;
      $("div.name_tree_div", $(this).closest("li"))
        .hide("fast")
        .css("list-style-image", "url(<?= Theme::theme()->parameter('image-plus') ?>)");
    }
    return false;
  });
</script>
<?php View::endpush() ?>
