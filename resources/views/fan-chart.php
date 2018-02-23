<?= $html ?>

<map id="fan-chart-map" name="fan-chart-map">
	<?= $areas ?>
</map>

<div class="text-center">
	<img class="wt-fan-chart-img" src="data:image/png;base64,<?= base64_encode($png) ?>" width="<?= $fanw ?>" height="<?= $fanh ?>" alt="<?= strip_tags($title) ?>" usemap="#fan-chart-map">
</div>
