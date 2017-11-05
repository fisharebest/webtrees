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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDB;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class OnThisDayModule
 */
class OnThisDayModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('On this day');
	}

	/** {@inheritdoc} */
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

		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$filter    = (bool) $this->getBlockSetting($block_id, 'filter', '1');
		$onlyBDM   = (bool) $this->getBlockSetting($block_id, 'onlyBDM', '0');

		foreach (['filter', 'infoStyle', 'onlyBDM', 'sortStyle'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$facts   = [];
		$summary = '';

		if ($onlyBDM) {
			$tags = $filter ? 'BIRT|MARR' : 'BIRT|MARR|DEAT';
		} else {
			$tags = WT_EVENTS_BIRT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV;
			// If we are only showing living individuals, then we don't need to search for DEAT events.
			if (!$filter) {
				$tags .= '|' . WT_EVENTS_DEAT;
			}
		}

		foreach (FunctionsDB::getEventsList(WT_CLIENT_JD, WT_CLIENT_JD, $tags, $WT_TREE) as $fact) {
			$record = $fact->getParent();
			// only living people ?
			if ($filter) {
				if ($record instanceof Individual && $record->isDead()) {
					continue;
				}
				if ($record instanceof Family) {
					$husb = $record->getHusband();
					if (is_null($husb) || $husb->isDead()) {
						continue;
					}
					$wife = $record->getWife();
					if (is_null($wife) || $wife->isDead()) {
						continue;
					}
				}
			}
			$facts[] = $fact;
		}

		// Now we have the list, we can sort by event
		// only need to sort if displaying in a list
		if ($infoStyle === 'list') {
			switch ($sortStyle) {
				case 'anniv':
					uasort($facts, function (Fact $x, Fact $y) {
							return Fact::compareDate($y, $x);
						}
					);
					break;
				case 'alpha':
					uasort($facts, function (Fact $x, Fact $y) {
							return GedcomRecord::compare($x->getParent(), $y->getParent());
						}
					);
					break;
			}
		}

		if (count($facts) === 0) {
			if ($filter) {
				$summary = I18N::translate('No events for living individuals exist for today.');
			} else {
				$summary = I18N::translate('No events exist for today.');
			}
		}

		$content = view('blocks/on-this-day-' . $infoStyle, [
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
				]
			);
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
			$this->setBlockSetting($block_id, 'filter', Filter::postBool('filter'));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'alpha|anniv', 'alpha'));
			$this->setBlockSetting($block_id, 'onlyBDM', Filter::postBool('onlyBDM'));
		}

		$filter    = $this->getBlockSetting($block_id, 'filter', '1');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');
		$onlyBDM   = $this->getBlockSetting($block_id, 'onlyBDM', '0');

		?>
		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="filter">
				<?= /* I18N: Label for a configuration option */ I18N::translate('Show only events of living individuals') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('filter', FunctionsEdit::optionsNoYes(), $filter, true) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="onlyBDM">
				<?= I18N::translate('Show only births, deaths, and marriages') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('onlyBDM', FunctionsEdit::optionsNoYes(), $onlyBDM, true) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="infoStyle">
				<?= /* I18N: Label for a configuration option */ I18N::translate('Presentation style') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['list'  => I18N::translate('list'),
										'table' => I18N::translate('table')],
										$infoStyle,
										['id' => 'infoStyle', 'name' => 'infoStyle']
								) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="sortStyle">
				<?= /* I18N: Label for a configuration option */ I18N::translate('Sort order') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['alpha' => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
				                        'anniv' => /* I18N: An option in a list-box */ I18N::translate('sort by date')],
										$sortStyle,
										['id' => 'sortStyle', 'name' => 'sortStyle']
								) ?>
			</div>
		</div>
		<?php
	}
}
