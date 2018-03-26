<?php use Fisharebest\Webtrees\I18N; ?>

<p>
	<?= I18N::translate('Dates are stored using English abbreviations and keywords. Shortcuts are available as alternatives to these abbreviations and keywords.') ?>
</p>

<table class="table table-bordered table-sm">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Date') ?>
			</th>
			<th>
				<?= I18N::translate('Format') ?>
			</th>
			<th>
				<?= I18N::translate('Shortcut') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($date_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
				<td>
					<?php foreach ($date_shortcuts[$code] as $shortcut): ?>
						<kbd dir="ltr" lang="en"><?= $shortcut ?></kbd>
						<br>
					<?php endforeach ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<p>
	<?= I18N::translate('Date ranges are used to indicate that an event, such as a birth, happened on an unknown date within a possible range.') ?>
</p>

<table class="table table-bordered table-sm">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Date range') ?>
			</th>
			<th>
				<?= I18N::translate('Format') ?>
			</th>
			<th>
				<?= I18N::translate('Shortcut') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($date_range_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
				<td>
					<?php foreach ($date_range_shortcuts[$code] as $shortcut): ?>
						<kbd dir="ltr" lang="en"><?= $shortcut ?></kbd>
						<br>
					<?php endforeach ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<p>
	<?=I18N::translate('Date periods are used to indicate that a fact, such as an occupation, continued for a period of time.') ?>
</p>

<table class="table table-bordered table-sm">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Date period') ?>
			</th>
			<th>
				<?= I18N::translate('Format') ?>
			</th>
			<th>
				<?= I18N::translate('Shortcut') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($date_period_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
				<td>
					<?php foreach ($date_period_shortcuts[$code] as $shortcut): ?>
						<kbd dir="ltr" lang="en"><?= $shortcut ?></kbd>
						<br>
					<?php endforeach ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<p>
	<?= I18N::translate('Simple dates are assumed to be in the gregorian calendar. To specify a date in another calendar, add a keyword before the date. This keyword is optional if the month or year format make the date unambiguous.') ?>
</p>

<table class="table table-bordered table-sm">
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Date') ?>
			</th>
			<th>
				<?= I18N::translate('Format') ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="2">
				<?= I18N::translate('Julian') ?>
			</th>
		</tr>
		<?php foreach ($julian_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
			</tr>
		<?php endforeach ?>

		<tr>
			<th colspan="2">
				<?= I18N::translate('Jewish') ?>
			</th>
		</tr>
		<?php foreach ($jewish_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
			</tr>
		<?php endforeach ?>

		<tr>
			<th colspan="2">
				<?= I18N::translate('Hijri') ?>
			</th>
		</tr>
		<?php foreach ($hijri_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
			</tr>
		<?php endforeach ?>

		<tr>
			<th colspan="2">
				<?= I18N::translate('French') ?>
			</th>
		</tr>
		<?php foreach ($french_dates as $code => $date): ?>
			<tr>
				<td>
					<?= $date ?>
				</td>
				<td>
					<kbd dir="ltr" lang="en"><?= $code ?></kbd>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
