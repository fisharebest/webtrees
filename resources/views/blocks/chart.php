<div id="chart-block-<?= e($block_id) ?>"></div>

<script>
	$(<?= json_encode('#chart-block-' . $block_id) ?>).load(<?= json_encode($chart_url) ?>);
</script>
