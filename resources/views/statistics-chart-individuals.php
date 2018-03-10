<?php use Fisharebest\Webtrees\I18N; ?>

<h3>
	<?= I18N::translate('Total individuals: %s', $stats->totalIndividuals()) ?>
</h3>

<table class="table table-sm table-bordered">
	<thead>
		<tr>
			<th><?= I18N::translate('Total males') ?></th>
			<th><?= I18N::translate('Total females') ?></th>
			<th><?= I18N::translate('Total living') ?></th>
			<th><?= I18N::translate('Total dead') ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?= $stats->totalSexMales() ?></td>
			<td><?= $stats->totalSexFemales() ?></td>
			<td><?= $stats->totalLiving() ?></td>
			<td><?= $stats->totalDeceased() ?></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"><?= $stats->chartSex() ?></td>
			<td colspan="2"><?= $stats->chartMortality() ?></td>
		</tr>
	</tfoot>
</table>

<h3><?= I18N::translate('Events') ?></h3>

<table class="table table-sm table-bordered">
	<tbody>
		<tr>
			<th><?= I18N::translate('Total births') ?></th>
			<th><?= I18N::translate('Total deaths') ?></th>
		</tr>
		<tr>
			<td><?= $stats->totalBirths() ?></td>
			<td><?= $stats->totalDeaths() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Births by century') ?></td>
			<td><?= I18N::translate('Deaths by century') ?></td>
		</tr>
		<tr>
			<td><?= $stats->statsBirth() ?></td>
			<td><?= $stats->statsDeath() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Earliest birth') ?></td>
			<td><?= I18N::translate('Earliest death') ?></td>
		</tr>
		<tr>
			<td><?= $stats->firstBirth() ?></td>
			<td><?= $stats->firstDeath() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Latest birth') ?></td>
			<td><?= I18N::translate('Latest death') ?></td>
		</tr>
		<tr>
			<td><?= $stats->lastBirth() ?></td>
			<td><?= $stats->lastDeath() ?></td>
		</tr>
	</tbody>
</table>

<h3><?= I18N::translate('Lifespan') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Average age at death') ?></td>
		<td><?= I18N::translate('Males') ?></td>
		<td><?= I18N::translate('Females') ?></td>
	</tr>
	<tr>
		<td><?= $stats->averageLifespan(true) ?></td>
		<td><?= $stats->averageLifespanMale(true) ?></td>
		<td><?= $stats->averageLifespanFemale(true) ?></td>
	</tr>
	<tr>
		<td colspan="3"><?= $stats->statsAge() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Greatest age at death') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Males') ?></td>
		<td><?= I18N::translate('Females') ?></td>
	</tr>
	<tr>
		<td><?= $stats->topTenOldestMaleList() ?></td>
		<td><?= $stats->topTenOldestFemaleList() ?></td>
	</tr>
</table>

<?php if ($show_oldest_living): ?>
	<h3><?= I18N::translate('Oldest living individuals') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Males') ?></td>
			<td><?= I18N::translate('Females') ?></td>
		</tr>
		<tr>
			<td><?= $stats->topTenOldestMaleListAlive() ?></td>
			<td><?= $stats->topTenOldestFemaleListAlive() ?></td>
		</tr>
	</table>
<?php endif ?>

<h3><?= I18N::translate('Names') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Total surnames') ?></td>
		<td><?= I18N::translate('Total given names') ?></td>
	</tr>
	<tr>
		<td><?= $stats->totalSurnames() ?></td>
		<td><?= $stats->totalGivennames() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Top surnames') ?></td>
		<td><?= I18N::translate('Top given names') ?></td>
	</tr>
	<tr>
		<td><?= $stats->chartCommonSurnames() ?></td>
		<td><?= $stats->chartCommonGiven() ?></td>
	</tr>
</table>
