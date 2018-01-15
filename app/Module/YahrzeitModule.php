<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Module;

use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;

/**
 * Class YahrzeitModule
 */
class YahrzeitModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. Yahrzeiten (the plural of Yahrzeit) are special anniversaries of deaths in the Hebrew faith/calendar. */ I18N::translate('Yahrzeiten');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Yahrzeiten” module. A “Hebrew death” is a death where the date is recorded in the Hebrew calendar. */ I18N::translate('A list of the Hebrew death anniversaries that will occur in the near future.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = []): string {
		global $ctype, $WT_TREE;

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$calendar  = $this->getBlockSetting($block_id, 'calendar', 'jewish');

		foreach (['days', 'infoStyle', 'calendar'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$jewish_calendar = new JewishCalendar;
		$startjd         = WT_CLIENT_JD;
		$endjd           = WT_CLIENT_JD + $days - 1;

		// The standard anniversary rules cover most of the Yahrzeit rules, we just
		// need to handle a few special cases.
		// Fetch normal anniversaries, with an extra day before/after
		$yahrzeits = [];
		for ($jd = $startjd - 1; $jd <= $endjd + $days; ++$jd) {
			foreach (FunctionsDb::getAnniversaryEvents($jd, 'DEAT _YART', $WT_TREE) as $fact) {
				// Exact hebrew dates only
				$date = $fact->getDate();
				if ($date->minimumDate() instanceof JewishDate && $date->minimumJulianDay() === $date->maximumJulianDay()) {

					// ...then adjust DEAT dates (but not _YART)
					if ($fact->getTag() === 'DEAT') {
						$today = new JewishDate($jd);
						$hd    = $fact->getDate()->minimumDate();
						$hd1   = new JewishDate($hd);
						$hd1->y += 1;
						$hd1->setJdFromYmd();
						// Special rules. See http://www.hebcal.com/help/anniv.html
						// Everything else is taken care of by our standard anniversary rules.
						if ($hd->d == 30 && $hd->m == 2 && $hd->y != 0 && $hd1->daysInMonth() < 30) {
							// 30 CSH - Last day in CSH
							$jd = $jewish_calendar->ymdToJd($today->y, 3, 1) - 1;
						} elseif ($hd->d == 30 && $hd->m == 3 && $hd->y != 0 && $hd1->daysInMonth() < 30) {
							// 30 KSL - Last day in KSL
							$jd = $jewish_calendar->ymdToJd($today->y, 4, 1) - 1;
						} elseif ($hd->d == 30 && $hd->m == 6 && $hd->y != 0 && $today->daysInMonth() < 30 && !$today->isLeapYear()) {
							// 30 ADR - Last day in SHV
							$jd = $jewish_calendar->ymdToJd($today->y, 6, 1) - 1;
						}
					}

					// Filter adjusted dates to our date range
					if ($jd >= $startjd && $jd < $startjd + $days) {
						// upcomming yahrzeit dates
						switch ($calendar) {
							case 'gregorian':
								$yahrzeit_date = new GregorianDate($jd);
								break;
							case 'jewish':
							default:
								$yahrzeit_date = new JewishDate($jd);
								break;
						}
						$yahrzeit_date = new Date($yahrzeit_date->format('%@ %A %O %E'));

						$yahrzeits[] = (object) [
							'individual' => $fact->getParent(),
							'fact_date'  => $fact->getDate(),
							'fact'       => $fact,
							'jd'            => $jd,
							'yahrzeit_date' => $yahrzeit_date,
						];
					}
				}
			}
		}

		switch ($infoStyle) {
			case 'list':
				$content = view('blocks/yahrzeit-list', [
					'yahrzeits' => $yahrzeits,
				]);
				break;
			case 'table':
			default:
				$content = view('blocks/yahrzeit-table', [
					'yahrzeits' => $yahrzeits,
				]);
			break;
		}

		if ($template) {
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
				$config_url = Html::url('block_edit.php', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} else {
				$config_url = '';
			}

			return view('blocks/template', [
				'block'      => str_replace('_', '-', $this->getName()),
				'id'         => $block_id,
				'config_url' => $config_url,
				'title'      => $this->getTitle(),
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock(): bool {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock(): bool {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 *
	 * @return void
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, 30, 7));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			$this->setBlockSetting($block_id, 'calendar', Filter::post('calendar', 'jewish|gregorian', 'jewish'));
		}

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$calendar  = $this->getBlockSetting($block_id, 'calendar', 'jewish');

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="days">';
		echo I18N::translate('Number of days to show');
		echo '</label><div class="col-sm-9">';
		echo '<input type="text" name="days" size="2" value="' . $days . '">';
		echo ' <em>', I18N::plural('maximum %s day', 'maximum %s days', 30, I18N::number(30)), '</em>';
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="infoStyle">';
		echo I18N::translate('Presentation style');
		echo '</label><div class="col-sm-9">';
		echo Bootstrap4::select(['list' => I18N::translate('list'), 'table' => I18N::translate('table')], $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']);
		echo '</div></div>';

		echo '<div class="form-group row"><label class="col-sm-3 col-form-label" for="calendar">';
		echo I18N::translate('Calendar');
		echo '</label><div class="col-sm-9">';
		echo Bootstrap4::select(['jewish' => I18N::translate('Jewish'), 'gregorian' => I18N::translate('Gregorian')], $calendar, ['id' => 'calendar', 'name' => 'calendar']);
		echo '</div></div>';
	}
}
