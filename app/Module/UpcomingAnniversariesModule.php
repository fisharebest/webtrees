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
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDB;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;

/**
 * Class UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModule extends AbstractModule implements ModuleBlockInterface {
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
		return /* I18N: Name of a module */ I18N::translate('Upcoming events');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Upcoming events” module */ I18N::translate('A list of the anniversaries that will occur in the near future.');
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

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$filter    = (bool) $this->getBlockSetting($block_id, 'filter', '1');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$events    = $this->getBlockSetting($block_id, 'events', $default_events);

		foreach (['days', 'events', 'filter', 'infoStyle', 'sortStyle'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$event_array = explode(',', $events);

		// If we are only showing living individuals, then we don't need to search for DEAT events.
		if ($filter) {
			$death_events = explode('|', WT_EVENTS_DEAT);
			$event_array = array_diff($event_array, $death_events);
		}

		$events_filter = implode('|', $event_array);

		$startjd = WT_CLIENT_JD + 1;
		$endjd   = WT_CLIENT_JD + $days;

		$facts = FunctionsDB::getEventsList($startjd, $endjd, $events_filter, $filter, $sortStyle, $WT_TREE);

		$summary = '';

		if (empty($facts)) {
			if ($filter) {
				if ($endjd == $startjd) {
					$summary = I18N::translate('No events for living individuals exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = I18N::plural('No events for living people exist for the next %s day.', 'No events for living people exist for the next %s days.', $endjd - $startjd + 1, I18N::number($endjd - $startjd + 1));
				}
			} else {
				if ($endjd == $startjd) {
					$summary = I18N::translate('No events exist for tomorrow.');
				} else {
					// I18N: translation for %s==1 is unused; it is translated separately as “tomorrow”
					$summary = I18N::plural('No events exist for the next %s day.', 'No events exist for the next %s days.', $endjd - $startjd + 1, I18N::number($endjd - $startjd + 1));
				}
			}
		}

		$content = view('blocks/events-' . $infoStyle, [
				'facts'   => $facts,
				'summary' => $summary,
			]
		);

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
			$this->setBlockSetting($block_id, 'filter', Filter::postBool('filter'));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'alpha|anniv', 'alpha'));
			$this->setBlockSetting($block_id, 'events', implode(',', Filter::postArray('events')));
		}

		$default_events = implode(',', self::DEFAULT_EVENTS);

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$filter    = $this->getBlockSetting($block_id, 'filter', '1');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$events    = $this->getBlockSetting($block_id, 'events', $default_events);

		$event_array = explode(',', $events);

		$all_events = [];
		foreach (self::ALL_EVENTS as $event) {
			$all_events[$event] = GedcomTag::getLabel($event);
		}
		?>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="days">
				<?= I18N::translate('Number of days to show') ?>
			</label>
			<div class="col-sm-9">
				<input type="text" name="days" id="days" size="2" value="<?= $days ?>">
				<?= I18N::plural('maximum %s day', 'maximum %s days', 30, I18N::number(30)) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="filter">
				<?= I18N::translate('Show only events of living individuals') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('filter', FunctionsEdit::optionsNoYes(), $filter, true) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="events">
				<?= I18N::translate('Events') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::multiSelect($all_events, $event_array, ['id' => 'events', 'name' => 'events[]', 'class' => 'select2']) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="infoStyle">
				<?= /* I18N: Label for a configuration option */ I18N::translate('Presentation style') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['list' => I18N::translate('list'), 'table' => I18N::translate('table')], $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="sortStyle">
				<?= /* I18N: Label for a configuration option */ I18N::translate('Sort order') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['alpha' => /* I18N: An option in a list-box */ I18N::translate('sort by name'), 'anniv' => /* I18N: An option in a list-box */ I18N::translate('sort by date')], $sortStyle, ['id' => 'sortStyle', 'name' => 'sortStyle']) ?>
			</div>
		</div>
		<?php
	}
}
