<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

/**
 * Class UpcomingAnniversariesModule
 */
class UpcomingAnniversariesModule extends AbstractModule implements ModuleBlockInterface {
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
	public function getBlock($block_id, $template = true, $cfg = []) {
		global $ctype, $WT_TREE;

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$filter    = $this->getBlockSetting($block_id, 'filter', '1');
		$onlyBDM   = $this->getBlockSetting($block_id, 'onlyBDM', '0');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');

		foreach (['days', 'filter', 'onlyBDM', 'infoStyle', 'sortStyle'] as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$startjd = WT_CLIENT_JD + 1;
		$endjd   = WT_CLIENT_JD + $days;

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = FontAwesome::linkIcon('preferences', I18N::translate('Preferences'), ['class' => 'btn btn-link', 'href' => 'block_edit.php?block_id=' . $block_id . '&ged=' . $WT_TREE->getNameHtml() . '&ctype=' . $ctype]) . ' ';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = '';
		switch ($infoStyle) {
		case 'list':
			// Output style 1:  Old format, no visible tables, much smaller text. Better suited to right side of page.
			$content .= FunctionsPrintLists::eventsList($startjd, $endjd, $onlyBDM ? 'BIRT MARR DEAT' : '', $filter, $sortStyle);
			break;
		case 'table':
			// Style 2: New format, tables, big text, etc. Not too good on right side of page
			ob_start();
			$content .= FunctionsPrintLists::eventsTable($startjd, $endjd, $onlyBDM ? 'BIRT MARR DEAT' : '', $filter, $sortStyle);
			$content .= ob_get_clean();
			break;
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'days', Filter::postInteger('days', 1, 30, 7));
			$this->setBlockSetting($block_id, 'filter', Filter::postBool('filter'));
			$this->setBlockSetting($block_id, 'onlyBDM', Filter::postBool('onlyBDM'));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			$this->setBlockSetting($block_id, 'sortStyle', Filter::post('sortStyle', 'alpha|anniv', 'alpha'));
		}

		$days      = $this->getBlockSetting($block_id, 'days', '7');
		$filter    = $this->getBlockSetting($block_id, 'filter', '1');
		$onlyBDM   = $this->getBlockSetting($block_id, 'onlyBDM', '0');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');
		$sortStyle = $this->getBlockSetting($block_id, 'sortStyle', 'alpha');

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
			<label class="col-sm-3 col-form-label" for="onlyBDM">
				<?= I18N::translate('Show only births, deaths, and marriages') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::radioButtons('onlyBDM', FunctionsEdit::optionsNoYes(), $onlyBDM, true) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="infoStyle">
				<?= I18N::translate('Presentation style') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select(['list' => I18N::translate('list'), 'table' => I18N::translate('table')], $infoStyle, ['id' => 'infoStyle', 'name' => 'infoStyle']) ?>
			</div>
		</div>

		<div class="form-group row">
			<label class="col-sm-3 col-form-label" for="sortStyle">
				<?= I18N::translate('Sort order') ?>
			</label>
			<div class="col-sm-9">
				<?= Bootstrap4::select([/* I18N: An option in a list-box */ 'alpha' => I18N::translate('sort by name'), /* I18N: An option in a list-box */ 'anniv' => I18N::translate('sort by date')], $sortStyle, ['id' => 'sortStyle', 'name' => 'sortStyle']) ?>
			</div>
		</div>
		<?php
	}
}
