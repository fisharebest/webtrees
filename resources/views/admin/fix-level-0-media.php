<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= Bootstrap4::breadcrumbs([Html::url('admin.php', ['route' => 'admin-control-panel']) => I18N::translate('Control panel')], $title) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('If you have linked a media object to an individual, instead of linking it to one of the facts or events, then you can move it to the correct location.') ?>
</p>

<table class="table table-bordered table-sm table-hover datatables" data-ajax="<?= HTML::escape(json_encode(['url' => Html::url('admin.php', ['route' => 'admin-fix-level-0-media-data'])])) ?>" data-state-save="true">
	<caption class="sr-only">
		<?= I18N::translate('Media objects') ?>
	</caption>
	<thead class="thead-dark">
		<tr>
			<th><?= I18N::translate('Tree') ?></th>
			<th data-sortable="false"><?= I18N::translate('Media object') ?></th>
			<th><?= I18N::translate('Title') ?></th>
			<th><?= I18N::translate('Individual') ?></th>
			<th data-sortable="false"><?= I18N::translate('Facts and events') ?></th>
		</tr>
	</thead>
</table>
