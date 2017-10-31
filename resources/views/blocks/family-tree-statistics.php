<?php use Fisharebest\Webtrees\I18N; ?>

<?php if ($show_last_update): ?>
	<p class="card-text">
		<?= I18N::translate('This family tree was last updated on %s.', strip_tags($stats->gedcomUpdated())) ?>
	</p>
<?php endif ?>

<div class="row">
	<div class="col-sm-4">
		<table class="table wt-facts-table">
			<caption class="sr-only">
				<?= I18N::translate('Statistics') ?>
			</caption>
			<tbody>
			<?php if ($stat_indi): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Individuals') ?>
					</th>
					<td>
						<?= $stats->totalIndividuals() ?>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?= I18N::translate('Males') ?>
					</th>
					<td>
						<?= $stats->totalSexMales() ?>
						<br>
						<?= $stats->totalSexMalesPercentage() ?>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?= I18N::translate('Females') ?>
					</th>
					<td>
						<?= $stats->totalSexFemales() ?>
						<br>
						<?= $stats->totalSexFemalesPercentage() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_surname): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Surnames') ?>
					</th>
					<td>
						<?= $stats->totalSurnames() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_fam): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Families') ?>
					</th>
					<td>
						<?= $stats->totalFamilies() ?>
					</td>
				</tr>
			<?php endif ?>


			<?php if ($stat_sour): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Sources') ?>
					</th>
					<td>
						<?= $stats->totalSources() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_media): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Media objects') ?>
					</th>
					<td>
						<?= $stats->totalMedia() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_repo): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Repositories') ?>
					</th>
					<td>
						<?= $stats->totalRepositories() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_events): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Events') ?>
					</th>
					<td>
						<?= $stats->totalEvents() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_users): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Users') ?>
					</th>
					<td>
						<?= $stats->totalUsers() ?>
					</td>
				</tr>
			<?php endif ?>
			</tbody>
		</table>
	</div>

	<div class="col-sm-8">
		<table class="table wt-facts-table">
			<caption class="sr-only">
				<?= I18N::translate('Statistics') ?>
			</caption>

			<tbody>
			<?php if ($stat_first_birth): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Earliest birth') ?>
					</th>
					<td>
						<?= $stats->firstBirth() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_last_birth): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Latest birth') ?>
					</th>
					<td>
						<?= $stats->lastBirth() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_first_death): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Earliest death') ?>
					</th>
					<td>
						<?= $stats->firstDeath() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_last_death): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Latest death') ?>
					</th>
					<td>
						<?= $stats->lastDeath() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_long_life): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Individual who lived the longest') ?>
					</th>
					<td>
						<?= $stats->longestLife() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_avg_life): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Average age at death') ?>
					</th>
					<td>
						<?= $stats->averageLifespan() ?>
						<br>
						<?= I18N::translate('Males') ?>:&nbsp;<?= $stats->averageLifespanMale() ?>
						<br>
						<?= I18N::translate('Females') ?>&nbsp;<?= $stats->averageLifespanFemale() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_most_chil): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Family with the most children') ?>
					</th>
					<td>
						<?= I18N::plural('%s child', '%s children', $stats->largestFamilySize(), I18N::number($stats->largestFamilySize())) ?>
						<br>
						<?= $stats->largestFamily() ?>
					</td>
				</tr>
			<?php endif ?>

			<?php if ($stat_avg_chil): ?>
				<tr>
					<th scope="row">
						<?= I18N::translate('Average number of children per family') ?>
					</th>
					<td>
						<?= $stats->averageChildren() ?>
					</td>
				</tr>
			<?php endif ?>
			</tbody>
		</table>
	</div>
</div>

<?php if (!empty($surnames)): ?>
	<div class="clearfloat">
		<p>
			<strong>
				<?= I18N::translate('Most common surnames') ?>
			</strong>
			<br>
			<span class="common_surnames">
				<?= $surnames ?>
			</span>
		</p>
	</div>
<?php endif ?>
