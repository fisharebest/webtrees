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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;

/**
 * Class TopGivenNamesModule
 */
class TopGivenNamesModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. Top=Most common */ I18N::translate('Top given names');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Top given names” module */ I18N::translate('A list of the most popular given names.');
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

		$num       = $this->getBlockSetting($block_id, 'num', '10');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');

		extract($cfg, EXTR_OVERWRITE);

		$stats = new Stats($WT_TREE);

		switch ($infoStyle) {
			case 'list':
				$males   = $stats->commonGivenMaleTotals([1, $num, 'rcount']);
				$females = $stats->commonGivenFemaleTotals([1, $num, 'rcount']);
				$content = view('blocks/top-given-names-list', [
					'males'   => $males,
					'females' => $females,
				]);
				break;
			default:
			case 'table':
				$males   = $stats->commonGivenMaleTable([1, $num, 'rcount']);
				$females = $stats->commonGivenFemaleTable([1, $num, 'rcount']);
				$content = view('blocks/top-given-names-table', [
					'males'   => $males,
					'females' => $females,
				]);
				break;
		}

		if ($template) {
			if ($num == 1) {
				// I18N: i.e. most popular given name.
				$title = I18N::translate('Top given name');
			} else {
				// I18N: Title for a list of the most common given names, %s is a number. Note that a separate translation exists when %s is 1
				$title = I18N::plural('Top %s given name', 'Top %s given names', $num, I18N::number($num));
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
			$this->setBlockSetting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));

			return;
		}

		$num       = $this->getBlockSetting($block_id, 'num', '10');
		$infoStyle = $this->getBlockSetting($block_id, 'infoStyle', 'table');

		$info_styles = [
			'list'  => /* I18N: An option in a list-box */ I18N::translate('list'),
			'table' => /* I18N: An option in a list-box */ I18N::translate('table'),
		];

		echo view('blocks/top-given-names-config', [
			'infoStyle'   => $infoStyle,
			'info_styles' => $info_styles,
			'num'         => $num,
		]);
	}
}
