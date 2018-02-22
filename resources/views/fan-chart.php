<?= $html ?>
<?= $imagemap ?>

<div id="fan_chart_img">
	<img src="data:image/png;base64,<?= base64_encode($png) ?>" width="<?= $fanw ?>" height="<?= $fanh ?>" alt="<?= strip_tags($title) ?>" usemap="#fanmap">
</div>
