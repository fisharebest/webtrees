<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Date; ?>
<?php use Fisharebest\Webtrees\Date\JewishDate; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsPrint; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<form name="dateform">
	<input type="hidden" name="route" value="calendar">
	<input type="hidden" name="cal" value="<?= e($cal) ?>">
	<input type="hidden" name="day" value="<?= e($cal_date->d) ?>">
	<input type="hidden" name="month" value="<?= e($cal_month) ?>">
	<input type="hidden" name="year" value="<?= e($cal_date->y) ?>">
	<input type="hidden" name="view" value="<?= e($view) ?>">
	<input type="hidden" name="filterev" value="<?= e($filterev) ?>">
	<input type="hidden" name="filtersx" value="<?= e($filtersx) ?>">
	<input type="hidden" name="filterof" value="<?= e($filterof) ?>">

	<table class="table-sm wt-page-options w-100" role="presentation">
		<tr>
			<th class="wt-page-options-label">
				<?= I18N::translate('Day') ?>
			</th>
			<td class="wt-page-options-value" colspan="3">
				<?php for ($d = 1; $d <= $days_in_month; $d++): ?>
					<a <?= $d === $cal_date->d ? 'class="error"' : '' ?> href="<?= e(route('calendar', ['cal' => $cal, 'day' => $d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'day', 'ged' => $tree->getName()])) ?>" rel="nofollow">
						<?= (new Date($cal_date->format("%@ {$d} %O %E")))->minimumDate()->format('%j') ?>
					</a>
					|
					<?php endfor ?>
				<a href="<?= e(route('calendar', ['cal' => $cal, 'day' => $today->d, 'month' => $today_month, 'year' => $today->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'day', 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<b><?php $tmp = new Date($today->format('%@ %A %O %E')); echo $tmp->display() ?></b>
				</a>
			</td>
		</tr>
		<tr>
			<th class="wt-page-options-label">
				<?= I18N::translate('Month') ?>
			</th>
			<td class="wt-page-options-value" colspan="3">
				<?php
					for ($n = 1, $months_in_year = $cal_date->monthsInYear(); $n <= $months_in_year; ++$n) {
						$month_name = $cal_date->monthNameNominativeCase($n, $cal_date->isLeapYear());
						$m          = array_search($n, $cal_date::$MONTH_ABBREV);
						if ($n === 6 && $cal_date instanceof JewishDate && !$cal_date->isLeapYear()) {
							// No month 6 in Jewish non-leap years.
							continue;
						}
						if ($n === 7 && $cal_date instanceof JewishDate && !$cal_date->isLeapYear()) {
							// Month 7 is ADR in Jewish non-leap years.
							$m = 'ADR';
						}
						if ($n === $cal_date->m) {
							$month_name = '<span class="error">' . $month_name . '</span>';
						}
						echo '<a href="' . e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $m, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'month', 'ged' => $tree->getName()])) . '" rel="nofollow">', $month_name, '</a>';
						echo ' | ';
					}
				?>
				<a href="<?= e(route('calendar', ['cal' => $cal, 'day' => min($cal_date->d, $today->daysInMonth()), 'month' => $today_month, 'year' => $today->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'month', 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<b><?= $today->format('%F %Y') ?></b>
				</a>
			</td>
		</tr>
		<tr>
			<th class="wt-page-options-label">
				<label for="year"><?= I18N::translate('Year') ?></label>
			</th>
			<td class="wt-page-options-value">
				<a href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y === 1 ? -1 : $cal_date->y - 1, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					-1
				</a>
				<input type="text" id="year" name="year" value="<?= $year ?>" size="4">
				<a href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y === -1 ? 1 : $cal_date->y + 1, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					+1
				</a>
				|
				<a href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $today->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<?= $today->format('%Y') ?>
				</a>
				<?= FunctionsPrint::helpLink('annivers_year_select') ?>
			</td>

			<th class="wt-page-options-label">
				<?= I18N::translate('Show') ?>
			</th>

			<td class="wt-page-options-value">
				<?php if (!$tree->getPreference('HIDE_LIVE_PEOPLE') || Auth::check()): ?>
					<select class="list_value" name="filterof" onchange="document.dateform.submit();">
						<option value="all" <?= $filterof === 'all' ? 'selected' : '' ?>>
							<?= I18N::translate('All individuals') ?>
						</option>
						<option value="living" <?= $filterof === 'living' ? 'selected' : '' ?>>
							<?= I18N::translate('Living individuals') ?>
						</option>
						<option value="recent" <?= $filterof === 'recent' ? 'selected' : '' ?>>
							<?= I18N::translate('Recent years (&lt; 100 yrs)') ?>
						</option>
					</select>
				<?php endif ?>

				<a title="<?= I18N::translate('All individuals') ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => '', 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<i class="<?= $filtersx === '' ? 'icon-sex_m_15x15' : 'icon-sex_m_9x9' ?>"></i>
					<i class="<?= $filtersx === '' ? 'icon-sex_f_15x15' : 'icon-sex_f_9x9' ?>"></i>
				</a>
				|
				<a title="<?= I18N::translate('Males') ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => 'M', 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<i class="<?= $filtersx === 'M' ? 'icon-sex_m_15x15' : 'icon-sex_m_9x9' ?>"></i>
				</a>
				|
				<a title="<?= I18N::translate('Females') ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => 'F', 'view' => $view, 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<i class="<?= $filtersx === 'F' ? 'icon-sex_f_15x15' : 'icon-sex_f_9x9' ?>"></i>
				</a>

				<select class="list_value" name="filterev" onchange="document.dateform.submit();">
					<option value="BIRT-MARR-DEAT" <?= $filterev === 'BIRT-MARR-DEAT' ? 'selected' : '' ?>>
						<?= I18N::translate('Vital records') ?>
					</option>
					<option value="" <?= $filterev === '' ? 'selected' : '' ?>>
						<?= I18N::translate('All') ?>
					</option>
					<option value="BIRT" <?= $filterev === 'BIRT' ? 'selected' : '' ?>>
						<?= I18N::translate('Birth') ?>
					</option>
					<option value="BAPM-CHR-CHRA" <?= $filterev === 'BAPM-CHR-CHRA' ? 'selected' : '' ?>>
						<?= I18N::translate('Baptism') ?>
					</option>
					<option value="MARR-_COML-_NMR" <?= $filterev === 'MARR-_COML-_NMR' ? 'selected' : '' ?>>
						<?= I18N::translate('Marriage') ?>
					</option>
					<option value="DIV-_SEPR" <?= $filterev === 'DIV-_SEPR' ? 'selected' : '' ?>>
						<?= I18N::translate('Divorce') ?>
					</option>
					<option value="DEAT" <?= $filterev === 'DEAT' ? 'selected' : '' ?>>
						<?= I18N::translate('Death') ?>
					</option>
					<option value="BURI" <?= $filterev === 'BURI' ? 'selected' : '' ?>>
						<?= I18N::translate('Burial') ?>
					</option>
					<option value="IMMI,EMIG" <?= $filterev === 'IMMI,EMIG' ? 'selected' : '' ?>>
						<?= I18N::translate('Emigration') ?>
					</option>
					<option value="EVEN" <?= $filterev === 'EVEN' ? 'selected' : '' ?>>
						<?= I18N::translate('Custom event') ?>
					</option>
				</select>
			</td>
		</tr>
	</table>

	<table class="width100">
		<tr>
			<td class="topbottombar width50">
				<a class="<?= $view === 'day' ? 'error' : '' ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'day', 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<?= I18N::translate('View this day') ?>
				</a>
				|
				<a class="<?= $view === 'month' ? 'error' : '' ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'month', 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<?= I18N::translate('View this month') ?>
				</a>
				|
				<a class="<?= $view === 'year' ? 'error' : '' ?>" href="<?= e(route('calendar', ['cal' => $cal, 'day' => $cal_date->d, 'month' => $cal_month, 'year' => $cal_date->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => 'year', 'ged' => $tree->getName()])) ?>" rel="nofollow">
					<?= I18N::translate('View this year') ?>
				</a>
			</td>
			<td class="topbottombar width50">
				<?php
					$n = 0;
					foreach (Date::calendarNames() as $newcal => $cal_name) {
						$tmp = $cal_date->convertToCalendar($newcal);
						if ($tmp->inValidRange()) {
							if ($n++) {
								echo ' | ';
							}
							echo '<a ' . (get_class($tmp) === get_class($cal_date) ? 'class="error"' : '') . 'href="' . e(route('calendar', ['cal' => $tmp->format('%@'), 'day' => $tmp->d, 'month' => $tmp->format('%O'), 'year' => $tmp->y, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx, 'view' => $view, 'ged' => $tree->getName()])) . '" rel="nofollow">', $cal_name, '</a>';
						}
					} ?>
			</td>
		</tr>
	</table>
</form>

<div class="wt-ajax-load wt-page-content" data-ajax-url="<?= e(route('calendar-events', ['ged' => $tree->getName(), 'cal' => $cal, 'day' => $day, 'month' => $month, 'year' => $year, 'view' => $view, 'filterev' => $filterev, 'filterof' => $filterof, 'filtersx' => $filtersx,])) ?>"></div>

<?= view('modals/ajax') ?>
