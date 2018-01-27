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
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\I18N;

/**
 * Class YahrzeitModule
 */
class YahrzeitModule extends AbstractModule implements ModuleBlockInterface {
	// Default values for new blocks.
	const DEFAULT_CALENDAR = 'jewish';
	const DEFAULT_DAYS     = 7;
	const DEFAULT_STYLE    = 'table';

	// Can show this number of days into the future.
	const MAX_DAYS = 30;

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

		$days      = $this->getBlockSetting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);
		$calendar  = $this->getBlockSetting($block_id, 'calendar', self::DEFAULT_CALENDAR);

		extract($cfg, EXTR_OVERWRITE);

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
			if ($ctype === 'gedcom' && Auth::isManager($WT_TREE)) {
				$config_url = route('tree-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
			} elseif ($ctype === 'user' && Auth::check()) {
				$config_url = route('user-page-block-edit', ['block_id' => $block_id, 'ged' => $WT_TREE->getName()]);
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, self::MAX_DAYS, self::DEFAULT_DAYS));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', self::DEFAULT_STYLE));
			$this->setBlockSetting($block_id, 'calendar', Filter::post('calendar', 'jewish|gregorian', self::DEFAULT_CALENDAR));

			return;
		}

		$calendar  = $this->getBlockSetting($block_id, 'calendar', 'jewish');
		$days      = $this->getBlockSetting($block_id, 'days', 'self::DEFAULT_DAYS');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');

		$styles = [
			'list'  => /* I18N: An option in a list-box */ I18N::translate('list'),
			'table' => /* I18N: An option in a list-box */ I18N::translate('table'),
		];

		$calendars = [
			'jewish'    => I18N::translate('Jewish'),
			'gregorian' => I18N::translate('Gregorian'),
		];

		echo view('blocks/yahrzeit-config', [
			'calendar'  => $calendar,
			'calendars' => $calendars,
			'days'      => $days,
			'infoStyle' => $infoStyle,
			'max_days'  => self::MAX_DAYS,
			'styles'    => $styles,
		]);
	}
}
