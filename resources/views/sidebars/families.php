<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>
<?php use Fisharebest\Webtrees\View; ?>

<form method="post" action="<?= e(Html::url('module.php' ,['mod' => 'families', 'mod_action' => 'ajax'])) ?>" onsubmit="return false;">
	<input type="search" name="sb_fam_name" id="sb_fam_name" placeholder="<?= I18N::translate('Search') ?>">
	<p>
		<?php foreach ($initials as $letter => $count): ?>
			<a href="<?= e(Html::url('module.php', ['mod' => 'families', 'mod_action' => 'ajax', 'alpha' => $letter])) ?>" class="sb_fam_letter">
				<?php if ($letter === '@'): ?>
					<?= I18N::translateContext('Unknown surname', '…') ?>
				<?php elseif ($letter === ','): ?>
					<?= I18N::translate('None') ?>
				<?php elseif ($letter === ' '): ?>
					<?= '&nbsp;' ?>
				<?php else: ?>
					<?= e($letter) ?>
				<?php endif ?>
			</a>
		<?php endforeach ?>
	</p>
	<div id="sb_fam_content"></div>
</form>

<?php View::push('javascript') ?>
<script>
  var famloadedNames = [];

  function fsearchQ() {
    var query = $("#sb_fam_name").val();
    if (query.length>1) {
      $("#sb_fam_content").load("module.php?mod=families&mod_action=ajax&search=" + query);
    }
  }

  var famtimerid = null;
  $("#sb_fam_name").keyup(function(e) {
    if (famtimerid) window.clearTimeout(famtimerid);
    famtimerid = window.setTimeout("fsearchQ()", 500);
  });
  $("#sidebar-content-families").on("click", ".sb_fam_letter", function() {
    $("#sb_fam_content").load(this.href);
    return false;
  });
  $("#sidebar-content-families").on("click", ".sb_fam_surname", function() {
    var element = $(this);
    var surname = element.data("surname");
    var alpha   = element.data("alpha");

    if (!famloadedNames[surname]) {
      jQuery.ajax({
        url:     "module.php?mod=families&mod_action=ajax&alpha=" + encodeURIComponent(alpha) + "&surname=" + encodeURIComponent(surname),
        cache:   false,
        success: function (html) {
          $("div.name_tree_div", element.closest("li"))
            .html(html)
            .show("fast")
            .css("list-style-image", "url(<?= Theme::theme()->parameter('image-minus') ?>)");
          famloadedNames[surname] = 2;
        }
      });
    } else if (famloadedNames[surname] === 1) {
      famloadedNames[surname] = 2;
      $("div.name_tree_div", $(this).closest("li"))
        .show()
        .css("list-style-image", "url(<?= Theme::theme()->parameter('image-minus') ?>)");
    } else {
      famloadedNames[surname] = 1;
      $("div.name_tree_div", $(this).closest("li"))
        .hide("fast")
        .css("list-style-image", "url(<?= Theme::theme()->parameter('image-plus') ?>)");
    }
    return false;
  });
</script>
<?php View::endpush() ?>
