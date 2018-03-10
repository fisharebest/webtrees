<?php use Fisharebest\Webtrees\I18N; ?>

<h3>
	<?= I18N::translate('Total families: %s', $stats->totalFamilies()) ?>
</h3>

<table>
	<tr>
		<td><?= I18N::translate('Total marriages') ?></td>
		<td><?= I18N::translate('Total divorces') ?></td>
	</tr>
	<tr>
		<td><?= $stats->totalMarriages() ?></td>
		<td><?= $stats->totalDivorces() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Marriages by century') ?></td>
		<td><?= I18N::translate('Divorces by century') ?></td>
	</tr>
	<tr>
		<td><?= $stats->statsMarr() ?></td>
		<td><?= $stats->statsDiv() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Earliest marriage') ?></td>
		<td><?= I18N::translate('Earliest divorce') ?></td>
	</tr>
	<tr>
		<td><?= $stats->firstMarriage() ?></td>
		<td><?= $stats->firstDivorce() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Latest marriage') ?></td>
		<td><?= I18N::translate('Latest divorce') ?></td>
	</tr>
	<tr>
		<td><?= $stats->lastMarriage() ?></td>
		<td><?= $stats->lastDivorce() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Length of marriage') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Longest marriage'), ' - ', $stats->topAgeOfMarriage() ?></td>
		<td><?= I18N::translate('Shortest marriage'), ' - ', $stats->minAgeOfMarriage() ?></td>
	</tr>
	<tr>
		<td><?= $stats->topAgeOfMarriageFamily() ?></td>
		<td><?= $stats->minAgeOfMarriageFamily() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Age in year of marriage') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Youngest male'), ' - ', $stats->youngestMarriageMaleAge(true) ?></td>
		<td><?= I18N::translate('Youngest female'), ' - ', $stats->youngestMarriageFemaleAge(true) ?></td>
	</tr>
	<tr>
		<td><?= $stats->youngestMarriageMale() ?></td>
		<td><?= $stats->youngestMarriageFemale() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Oldest male'), ' - ', $stats->oldestMarriageMaleAge(true) ?></td>
		<td><?= I18N::translate('Oldest female'), ' - ', $stats->oldestMarriageFemaleAge(true) ?></td>
	</tr>
	<tr>
		<td><?= $stats->oldestMarriageMale() ?></td>
		<td><?= $stats->oldestMarriageFemale() ?></td>
	</tr>
	<tr>
		<td colspan="2"><?= $stats->statsMarrAge() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Age at birth of child') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Youngest father'), ' - ', $stats->youngestFatherAge(true) ?></td>
		<td><?= I18N::translate('Youngest mother'), ' - ', $stats->youngestMotherAge(true) ?></td>
	</tr>
	<tr>
		<td><?= $stats->youngestFather() ?></td>
		<td><?= $stats->youngestMother() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Oldest father'), ' - ', $stats->oldestFatherAge(true) ?></td>
		<td><?= I18N::translate('Oldest mother'), ' - ', $stats->oldestMotherAge(true) ?></td>
	</tr>
	<tr>
		<td><?= $stats->oldestFather() ?></td>
		<td><?= $stats->oldestMother() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Children in family') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Average number of children per family') ?></td>
		<td><?= I18N::translate('Number of families without children') ?></td>
	</tr>
	<tr>
		<td><?= $stats->averageChildren() ?></td>
		<td><?= $stats->noChildrenFamilies() ?></td>
	</tr>
	<tr>
		<td><?= $stats->statsChildren() ?></td>
		<td><?= $stats->chartNoChildrenFamilies() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Largest families') ?></td>
		<td><?= I18N::translate('Largest number of grandchildren') ?></td>
	</tr>
	<tr>
		<td><?= $stats->topTenLargestFamilyList() ?></td>
		<td><?= $stats->topTenLargestGrandFamilyList() ?></td>
	</tr>
</table>

<h3><?= I18N::translate('Age difference') ?></h3>

<table>
	<tr>
		<td><?= I18N::translate('Age between siblings') ?></td>
		<td><?= I18N::translate('Greatest age between siblings') ?></td>
	</tr>
	<tr>
		<td><?= $stats->topAgeBetweenSiblingsList() ?></td>
		<td><?= $stats->topAgeBetweenSiblingsFullName() ?></td>
	</tr>
	<tr>
		<td><?= I18N::translate('Age between husband and wife') ?></td>
		<td><?= I18N::translate('Age between wife and husband') ?></td>
	</tr>
	<tr>
		<td><?= $stats->ageBetweenSpousesMFList() ?></td>
		<td><?= $stats->ageBetweenSpousesFMList() ?></td>
	</tr>
</table>
