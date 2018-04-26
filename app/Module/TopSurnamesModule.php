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
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Query\QueryName;

/**
 * Class TopSurnamesModule
 */
class TopSurnamesModule extends AbstractModule implements ModuleBlockInterface {
	// Default values for new blocks.
	const DEFAULT_NUMBER = 10;
	const DEFAULT_STYLE  = 'table';

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module. Top=Most common */ I18N::translate('Top surnames');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Top surnames” module */ I18N::translate('A list of the most popular surnames.');
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
		global $WT_TREE, $ctype;

		$num       = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

		extract($cfg, EXTR_OVERWRITE);

		// This next function is a bit out of date, and doesn't cope well with surname variants
		$top_surnames = FunctionsDb::getTopSurnames($WT_TREE->getTreeId(), 0, $num);

		$all_surnames = [];
		$i            = 0;
		foreach (array_keys($top_surnames) as $top_surname) {
			$all_surnames = array_merge($all_surnames, QueryName::surnames($WT_TREE, $top_surname, '', false, false));
			if (++$i == $num) {
				break;
			}
		}
		if ($i < $num) {
			$num = $i;
		}

		switch ($infoStyle) {
			case 'tagcloud':
				uksort($all_surnames, '\Fisharebest\Webtrees\I18N::strcasecmp');
				$content = FunctionsPrintLists::surnameTagCloud($all_surnames, 'individual-list', true, $WT_TREE);
				break;
			case 'list':
				uasort($all_surnames, '\Fisharebest\Webtrees\Module\TopSurnamesModule::surnameCountSort');
				$content = FunctionsPrintLists::surnameList($all_surnames, 1, true, 'individual-list', $WT_TREE);
				break;
			case 'array':
				uasort($all_surnames, '\Fisharebest\Webtrees\Module\TopSurnamesModule::surnameCountSort');
				$content = FunctionsPrintLists::surnameList($all_surnames, 2, true, 'individual-list', $WT_TREE);
				break;
			case 'table':
			default:
				uasort($all_surnames, '\Fisharebest\Webtrees\Module\TopSurnamesModule::surnameCountSort');
				$content = view('tables/surnames', [
					'surnames' => $all_surnames,
					'route'    => 'individual-list',
					'tree'     => $WT_TREE,
				]);
				break;
		}

		if ($template) {
			if ($num == 1) {
				// I18N: i.e. most popular surname.
				$title = I18N::translate('Top surname');
			} else {
				// I18N: Title for a list of the most common surnames, %s is a number. Note that a separate translation exists when %s is 1
				$title = I18N::plural('Top %s surname', 'Top %s surnames', $num, I18N::number($num));
			}

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
				'title'      => $title,
				'content'    => $content,
			]);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax(): bool {
		return false;
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
			$this->setBlockSetting($block_id, 'num', Filter::postInteger('num', 1, 10000, 10));
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|array|table|tagcloud', self::DEFAULT_STYLE));

			return;
		}

		$num       = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

		$info_styles = [
			'list'     => /* I18N: An option in a list-box */ I18N::translate('bullet list'),
			'array'    => /* I18N: An option in a list-box */ I18N::translate('compact list'),
			'table'    => /* I18N: An option in a list-box */ I18N::translate('table'),
			'tagcloud' => /* I18N: An option in a list-box */ I18N::translate('tag cloud'),
		];

		echo view('blocks/top-surnames-config', [
			'num'         => $num,
			'infoStyle'   => $infoStyle,
			'info_styles' => $info_styles,
		]);
	}

	/**
	 * Sort (lists of counts of similar) surname by total count.
	 *
	 * @param string[][] $a
	 * @param string[][] $b
	 *
	 * @return int
	 */
	private static function surnameCountSort($a, $b) {
		$counta = 0;
		foreach ($a as $x) {
			$counta += count($x);
		}
		$countb = 0;
		foreach ($b as $x) {
			$countb += count($x);
		}

		return $countb - $counta;
	}
}
