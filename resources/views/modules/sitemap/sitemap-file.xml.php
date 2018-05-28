<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
	<?php foreach ($records as $record): ?>
		<url>
			<loc><?= e($record->url()) ?></loc>
			<?php if ($record->getFirstFact('CHAN') !== null): ?>
				<lastmod><?= $record->getFirstFact('CHAN')->getDate()->minimumDate()->Format('%Y-%m-%d') ?></lastmod>
			<?php endif ?>
		</url>
	<?php endforeach ?>
</urlset>


