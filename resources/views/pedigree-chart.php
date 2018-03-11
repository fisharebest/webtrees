<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<div id="pedigree-page">
	<div id="pedigree_chart" class="layout<?= e($orientation) ?>">
		<?php foreach ($nodes as $i => $node): ?>
			<div id="sosa_<?= ($i + 1) ?>" class="shadow d-flex align-items-center<?= e($flex_direction) ?>" style="<?= e($posn) ?>:<?= e($node['x']) ?>px; top:<?= e($node['y']) ?>px; position:absolute;">
				<?php if ($orientation === $oldest_at_top): ?>
					<?php if ($i >= $last_gen_start): ?>
						<?= $node['previous_gen'] ?>
					<?php endif ?>
				<?php else: ?>
					<?php if ($i === 0): ?>
						<?= $child_menu ?>
					<?php endif ?>
				<?php endif ?>

				<?php FunctionsPrint::printPedigreePerson($nodes[$i]['indi']) ?>

				<?php if ($orientation === $oldest_at_top): ?>
					<?php if ($i === 0): ?>
						<?= $child_menu ?>
					<?php endif ?>
				<?php else: ?>
					<?php if ($i >= $last_gen_start): ?>
						<?= $node['previous_gen'] ?>
					<?php endif ?>
				<?php endif ?>
			</div>
		<?php endforeach ?>
		<canvas id="pedigree_canvas" width="<?= e($canvas_width) ?>" height="<?= e($canvas_height) ?>">
		</canvas>
	</div>
</div>

<script>
  (function() {
    $("#childarrow").on("click", ".menuselect", function(e) {
      e.preventDefault();
      $("#childbox-pedigree").slideToggle("fast");
    });

    $("#pedigree_chart")
      .width(<?= json_encode($canvas_width) ?>)
  .height(<?= json_encode($canvas_height) ?>);

    // Set variables
    var p0, p1, p2,  // Holds the ids of the boxes used in the join calculations
        canvas       = $("#pedigree_canvas"),
        ctx          = canvas[0].getContext("2d"),
        nodes        = $(".shadow").length,
        gen1Start    = Math.ceil(nodes / 2),
        boxWidth     = $(".person_box_template").first().outerWidth(),
        boxHeight    = $(".person_box_template").first().outerHeight(),
        useOffset    = true,
        extraOffsetX = Math.floor(boxWidth / 15), // set offsets to be sensible fractions of the box size
        extraOffsetY = Math.floor(boxHeight / 10),
        addOffset;

    // Draw joining lines on the <canvas>
    function drawLines(context, x1, y1, x2, y2) {
      x1 = Math.floor(x1);
      y1 = Math.floor(y1);
      x2 = Math.floor(x2);
      y2 = Math.floor(y2);
      if (<?= json_encode($orientation < $oldest_at_top) ?>) {
        context.moveTo(x1, y1);
        context.lineTo(x2, y1);
        context.lineTo(x2, y2);
        context.lineTo(x1, y2);
      } else {
        context.moveTo(x1, y1);
        context.lineTo(x1, y2);
        context.lineTo(x2, y2);
        context.lineTo(x2, y1);
      }
    }

    //Plot the lines
    switch (<?= json_encode($orientation) ?>) {
      case <?= json_encode($portrait) ?>:
        useOffset = false;
      // Drop through
      case <?= json_encode($landscape) ?>:
        for (var i = 2; i < nodes; i+=2) {
          p0 = $("#sosa_" + i);
          p1 = $("#sosa_" + (i+1));
          // change line y position if within 10% of box top/bottom
          addOffset = boxHeight / (p1.position().top - p0.position().top) > 0.9 ? extraOffsetY: 0;
          if (<?= json_encode(I18N::direction() === 'rtl') ?>) {
            drawLines(
              ctx,
              p0.position().left + p0.width(),
              p0.position().top + (boxHeight / 2) + addOffset,
              p0.position().left + p0.width() + extraOffsetX,
              p1.position().top + (boxHeight / 2) - addOffset
            );
          } else {
            drawLines(
              ctx,
              p0.position().left,
              p0.position().top + (boxHeight / 2) + addOffset,
              p0.position().left - extraOffsetX,
              p1.position().top + (boxHeight / 2) - addOffset
            );
          }
        }
        break;
      case <?= json_encode($oldest_at_top) ?>:
        useOffset = false;
      // Drop through
      case <?= json_encode($oldest_at_bottom) ?>:
        for (var i = 1; i < gen1Start; i++) {
          p0 = $("#sosa_" + i);
          p1 = $("#sosa_" + (i*2));
          p2 = $("#sosa_" + (i*2+1));
          addOffset = i*2 >= gen1Start ? extraOffsetX : 0;
          var templateHeight = p0.children(".person_box_template").outerHeight(),
              // bHeight taks account of offset when root person has a menu icon
              bHeight = useOffset ? (p0.outerHeight() - templateHeight) + (templateHeight / 2) : templateHeight / 2;
          drawLines(
            ctx,
            p1.position().left + (boxWidth / 2) + addOffset,
            p1.position().top + boxHeight,
            p2.position().left + (boxWidth / 2) - addOffset,
            p0.position().top + bHeight
          );
        }
        break;
    }

    // Set line styles & draw them
    ctx.strokeStyle   = canvas.css("color");
    ctx.lineWidth     = <?= json_encode(Theme::theme()->parameter('line-width')) ?>;
    ctx.shadowColor   = <?= json_encode(Theme::theme()->parameter('shadow-color')) ?>;
    ctx.shadowBlur    = <?= json_encode(Theme::theme()->parameter('shadow-blur')) ?>;
    ctx.shadowOffsetX = <?= json_encode(Theme::theme()->parameter('shadow-offset-x')) ?>;
    ctx.shadowOffsetY = <?= json_encode(Theme::theme()->parameter('shadow-offset-y')) ?>;
    ctx.stroke();
  })();
</script>
