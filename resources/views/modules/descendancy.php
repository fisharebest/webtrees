<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<form method="post" action="module.php?mod=descendancy&amp;mod_action=search" onsubmit="return false;">
	<input type="search" name="sb_desc_name" id="sb_desc_name" placeholder="<?= I18N::translate('Search') ?>">
</form>

<div id="sb_desc_content">
	<ul>
		<?= $individual_list ?>
	</ul>
</div>

<?php View::push('javascript') ?>
<script>
  function dsearchQ() {
    var query = $("#sb_desc_name").val();
    if (query.length>1) {
      $("#sb_desc_content").load("module.php?mod=descendancy&mod_action=search&search="+query);
    }
  }

  $("#sb_desc_name").focus(function(){this.select();});
  $("#sb_desc_name").blur(function(){if (this.value === "") this.value="' . I18N::translate('Search') . '";});
  var dtimerid = null;
  $("#sb_desc_name").keyup(function(e) {
    if (dtimerid) window.clearTimeout(dtimerid);
    dtimerid = window.setTimeout("dsearchQ()", 500);
  });

  $("#sb_desc_content").on("click", ".sb_desc_indi", function() {
    var self = $(this),
        state = self.children(".plusminus"),
        target = self.siblings("div");
    if(state.hasClass("icon-plus")) {
      if (jQuery.trim(target.html())) {
        target.show("fast"); // already got content so just show it
      } else {
        target
          .hide()
          .load(self.attr("href"), function(response, status, xhr) {
            if(status === "success" && response !== "") {
              target.show("fast");
            }
          })
      }
    } else {
      target.hide("fast");
    }
    state.toggleClass("icon-minus icon-plus");
    return false;
  });
</script>
<?php View::endpush() ?>
