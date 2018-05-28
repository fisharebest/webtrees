<?xml version="1.0" encoding="UTF-8" ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<?php foreach ($count_individuals as $tree_id => $count): ?>
	<?php for ($i = 0; $i <= $count / $records_per_volume; ++$i): ?>
	<sitemap>
		<loc>
			<?= e(route('module', ['module' => 'sitemap', 'action' => 'File', 'file' => $tree_id . '-i-' . $i])) ?>
		</loc>
		<lastmod>
			<?= $last_mod ?>
		</lastmod>
	</sitemap>
	<?php endfor ?>
	<?php endforeach ?>
	
	<?php foreach ($count_media as $tree_id => $count): ?>
	<?php for ($i = 0; $i <= $count / $records_per_volume; ++$i): ?>
	<sitemap>
		<loc>
			<?= e(route('module', ['module' => 'sitemap', 'action' => 'File', 'file' => $tree_id . '-m-' . $i])) ?>
		</loc>
		<lastmod>
			<?= $last_mod ?>
		</lastmod>
	</sitemap>
	<?php endfor ?>
	<?php endforeach ?>

	<?php foreach ($count_notes as $tree_id => $count): ?>
	<?php for ($i = 0; $i <= $count / $records_per_volume; ++$i): ?>
	<sitemap>
		<loc>
			<?= e(route('module', ['module' => 'sitemap', 'action' => 'File', 'file' => $tree_id . '-i-' . $n])) ?>
		</loc>
		<lastmod>
			<?= $last_mod ?>
		</lastmod>
	</sitemap>
	<?php endfor ?>
	<?php endforeach ?>
	
	<?php foreach ($count_repositories as $tree_id => $count): ?>
	<?php for ($i = 0; $i <= $count / $records_per_volume; ++$i): ?>
	<sitemap>
		<loc>
			<?= e(route('module', ['module' => 'sitemap', 'action' => 'File', 'file' => $tree_id . '-r-' . $i])) ?>
		</loc>
		<lastmod>
			<?= $last_mod ?>
		</lastmod>
	</sitemap>
	<?php endfor ?>
	<?php endforeach ?>

	<?php foreach ($count_sources as $tree_id => $count): ?>
	<?php for ($i = 0; $i <= $count / $records_per_volume; ++$i): ?>
	<sitemap>
		<loc>
			<?= e(route('module', ['module' => 'sitemap', 'action' => 'File', 'file' => $tree_id . '-s-' . $i])) ?>
		</loc>
		<lastmod>
			<?= $last_mod ?>
		</lastmod>
	</sitemap>
	<?php endfor ?>
	<?php endforeach ?>
</sitemapindex>
