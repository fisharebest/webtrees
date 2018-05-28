<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), $title]]) ?>

<h1><?= $title ?></h1>

<p>
	<?= /* I18N: The www.sitemaps.org site is translated into many languages (e.g. http://www.sitemaps.org/fr/) - choose an appropriate URL. */ I18N::translate('Sitemaps are a way for webmasters to tell search engines about the pages on a website that are available for crawling. All major search engines support sitemaps. For more information, see <a href="http://www.sitemaps.org/">www.sitemaps.org</a>.') ?>
</p>

<p>
	<?= /* I18N: Label for a configuration option */ I18N::translate('Which family trees should be included in the sitemaps') ?>
</p>

<form action="<?= e(route('module', ['module' => 'sitemap', 'action' => 'Admin'])) ?>" method="post">
	<?= csrf_field() ?>

	<?php foreach ($all_trees as $tree): ?>
		<?= Bootstrap4::checkbox($tree->getTitle(), false, ['name' => 'sitemap' . $tree->getTreeId(), 'checked' => (bool) $tree->getPreference('include_in_sitemap')]) ?>
<?php endforeach ?>

<button type="submit" class="btn btn-primary">
	<?= I18N::translate('save') ?>
</button>

</form>

<hr>

<p>
	<?= I18N::translate('To tell search engines that sitemaps are available, you should add the following line to your robots.txt file.') ?>
</p>

<pre>Sitemap: <?= e($sitemap_url) ?></pre>

<hr>

<p>
	<?= I18N::translate('To tell search engines that sitemaps are available, you can use the following links.') ?>
</p>

<ul>
	<?php foreach ($submit_urls as $search_engine => $url): ?>
	<li>
		<a href="<?= e($url) ?>">
			<?= e($search_engine) ?>
		</a>
	</li>
	<?php endforeach ?>
</ul>
