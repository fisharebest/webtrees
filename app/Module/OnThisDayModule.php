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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;

/**
 * Class OnThisDayModule
 */
class OnThisDayModule extends AbstractModule implements ModuleBlockInterface {
	// All standard GEDCOM 5.5.1 events except CENS, RESI and EVEN
	const ALL_EVENTS = [
		'ADOP',
		'ANUL',
		'BAPM',
		'BARM',
		'BASM',
		'BIRT',
		'BLES',
		'BURI',
		'CHR',
		'CHRA',
		'CONF',
		'CREM',
		'DEAT',
		'DIV',
		'DIVF',
		'EMIG',
		'ENGA',
		'FCOM',
		'GRAD',
		'IMMI',
		'MARB',
		'MARC',
		'MARL',
		'MARR',
		'MARS',
		'NATU',
		'ORDN',
		'PROB',
		'RETI',
		'WILL',
	];

	const DEFAULT_EVENTS = [
		'BIRT',
		'MARR',
		'DEAT',
	];

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('On this day');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “On this day” module */ I18N::translate('A list of the anniversaries that occur today.');
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

		$default_events = implode(',', self::DEFAULT_EVENTS);

		$filter    = (bool) $this->getBlockSetting($block_id, 'filter', '1');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$events    = $this->getBlockSetting($block_id, 'events', $default_events);

		extract($cfg, EXTR_OVERWRITE);

		$event_array = explode(',', $events);

		// If we are only showing living individuals, then we don't need to search for DEAT events.
		if ($filter) {
			$death_events = explode('|', WT_EVENTS_DEAT);
			$event_array = array_diff($event_array, $death_events);
		}

		$events_filter = implode('|', $event_array);

		$startjd = WT_CLIENT_JD;
		$endjd   = WT_CLIENT_JD;

		$facts = FunctionsDb::getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $WT_TREE);

		if (empty($facts)) {
			$content = view('blocks/events-empty', [
				'message' => I18N::translate('No events exist for today.')
			]);
		} else {
			$content = view('blocks/events-' . $infoStyle, [
					'facts'   => $facts,
				]);
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
			$this->setBlockSetting($block_id, 'filter', Filter::postBool('filter'));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'alpha|anniv', 'alpha'));
			$this->setBlockSetting($block_id, 'events', implode(',', Filter::postArray('events')));

			return;
		}

		$default_events = implode(',', self::DEFAULT_EVENTS);

		$filter    = $this->getBlockSetting($block_id, 'filter', '1');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$events    = $this->getBlockSetting($block_id, 'events', $default_events);

		$event_array = explode(',', $events);

		$all_events = [];
		foreach (self::ALL_EVENTS as $event) {
			$all_events[$event] = GedcomTag::getLabel($event);
		}

		$info_styles = [
			'list'  => /* I18N: An option in a list-box */ I18N::translate('list'),
			'table' => /* I18N: An option in a list-box */ I18N::translate('table'),
		];

		$sort_styles = [
			'alpha' => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
			'anniv' => /* I18N: An option in a list-box */ I18N::translate('sort by date'),
		];

		echo view('blocks/on-this-day-config', [
			'all_events'  => $all_events,
			'event_array' => $event_array,
			'filter'      => $filter,
			'infoStyle'   => $infoStyle,
			'info_styles' => $info_styles,
			'sortStyle'   => $sortStyle,
			'sort_styles' => $sort_styles,
		]);
	}
}
