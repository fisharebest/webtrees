<?php use Fisharebest\Webtrees\I18N; ?>

<h3>
	<?= I18N::translate('Records') ?>: <?= $stats->totalRecords() ?>
</h3>

<table>
	<tr>
		<td><?= I18N::translate('Media objects') ?></td>
		<td><?= I18N::translate('Sources') ?></td>
		<td><?= I18N::translate('Notes') ?></td>
		<td><?= I18N::translate('Repositories') ?></td>
	</tr>
	<tr>
		<td><?= $stats->totalMedia() ?></td>
		<td><?= $stats->totalSources() ?></td>
		<td><?= $stats->totalNotes() ?></td>
		<td><?= $stats->totalRepositories() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Total events'), ': ', $stats->totalEvents() ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('First event'), ' - ', $stats->firstEventType() ?></td>
		<td><?= I18N::translate('Last event'), ' - ', $stats->lastEventType() ?></td>
	</tr>
	<tr>
		<td><?= $stats->firstEvent() ?></td>
		<td><?= $stats->lastEvent() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Media objects'), ': ', $stats->totalMedia() ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Media objects') ?></td>
	</tr>
	<tr>
		<td><?= $stats->chartMedia() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Sources'), ': ', $stats->totalSources() ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Individuals with sources') ?></td>
		<td><?= I18N::translate('Families with sources') ?></td>
	</tr>
	<tr>
		<td><?= $stats->totalIndisWithSources() ?></td>
		<td><?= $stats->totalFamsWithSources() ?></td>
	</tr>
	<tr>
		<td><?= $stats->chartIndisWithSources() ?></td>
		<td><?= $stats->chartFamsWithSources() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Places'), ': ', $stats->totalPlaces() ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Birth places') ?></td>
		<td><?= I18N::translate('Death places') ?></td>
	</tr>
	<tr>
		<td><?= $stats->commonBirthPlacesList() ?></td>
		<td><?= $stats->commonDeathPlacesList() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Marriage places') ?></td>
		<td><?= I18N::translate('Events in countries') ?></td>
	</tr>
	<tr>
		<td><?= $stats->commonMarriagePlacesList() ?></td>
		<td><?= $stats->commonCountriesList() ?></td>
	</tr>
	<tr>
		<td colspan="2"><?= $stats->chartDistribution() ?></td>
	</tr>
</table>
