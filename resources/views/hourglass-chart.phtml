<div class="position-relative" id="hourglass_chart" style="width:98%; z-index:1;">
	<table>
		<tr>
			<td style="vertical-align:middle">
				<?= $descendants ?>
			</td>
			<td style="vertical-align:middle">
				<?= $ancestors ?>
			</td>
		</tr>
	</table>
</div>

<script>
(function() {
  function sizeLines() {
    $('.tvertline').each(function(i,e) {
      var pid = e.id.split('_').pop();
      e.style.height = Math.abs($('#table_' + pid)[0].offsetHeight - ($('#table2_' + pid)[0].offsetTop + <?= $bhalfheight ?>)) + 'px';
    });

    $('.bvertline').each(function(i,e) {
      var pid = e.id.split('_').pop();
      e.style.height = $('#table_' + pid)[0].offsetTop + $('#table2_' + pid)[0].offsetTop + <?= $bhalfheight ?> + 'px';
    });

    $('.pvline').each(function(i,e) {
      var el = $(e);
      el.height(Math.floor(el.parent().height()/2));
    });
  }

  $('#spouse-child-links').on('click', function(e) {
    e.preventDefault();
    $('#childbox').slideToggle('fast');
  });
  $('.hourglassChart').on('click', '.wt-icon-arrow-start, .wt-icon-arrow-end', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var route   = this.parentNode.dataset.route;
    var spouses = this.parentNode.dataset.spouses;
    var tree    = this.parentNode.dataset.tree;
    var xref    = this.parentNode.dataset.xref;

    $('#td_' + xref).load('index.php', {
      generations:  1,
      route:        route,
      show_spouses: spouses,
      ged:          tree,
      xref:         xref
    }, function() {
      sizeLines();
    });
  });

  sizeLines();
})();
</script>
