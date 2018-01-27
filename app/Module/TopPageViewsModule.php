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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;

/**
 * Class TopPageViewsModule
 */
class TopPageViewsModule extends AbstractModule implements ModuleBlockInterface {
	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Most viewed pages');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Most visited pages” module */ I18N::translate('A list of the pages that have been viewed the most number of times.');
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

		$num             = $this->getBlockSetting($block_id, 'num', '10');
		$count_placement = $this->getBlockSetting($block_id, 'count_placement', 'before');

		extract($cfg, EXTR_OVERWRITE);

		// load the lines from the file
		$top10 = Database::prepare(
			"SELECT page_parameter, page_count" .
			" FROM `##hit_counter`" .
			" WHERE gedcom_id = :tree_id AND page_name IN ('individual.php','family.php','source.php','repo.php','note.php','mediaviewer.php')" .
			" ORDER BY page_count DESC LIMIT :limit"
		)->execute([
			'tree_id' => $WT_TREE->getTreeId(),
			'limit'   => (int) $num,
		])->fetchAssoc();

		$content = '<table>';
		foreach ($top10 as $id => $count) {
			$record = GedcomRecord::getInstance($id, $WT_TREE);
			if ($record && $record->canShow()) {
				$content .= '<tr>';
				if ($count_placement == 'before') {
					$content .= '<td dir="ltr" style="text-align:right">[' . $count . ']</td>';
				}
				$content .= '<td class="name2" ><a href="' . e($record->url()) . '">' . $record->getFullName() . '</a></td>';
				if ($count_placement == 'after') {
					$content .= '<td dir="ltr" style="text-align:right">[' . $count . ']</td>';
				}
				$content .= '</tr>';
			}
		}
		$content .= '</table>';

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

	/**
	 * Should this block load asynchronously using AJAX?
	 *
	 * Simple blocks are faster in-line, more comples ones
	 * can be loaded later.
	 *
	 * @return bool
	 */
	public function loadAjax(): bool {
		return true;
	}

	/**
	 * Can this block be shown on the user’s home page?
	 *
	 * @return bool
	 */
	public function isUserBlock(): bool {
		return false;
	}

	/**
	 * Can this block be shown on the tree’s home page?
	 *
	 * @return bool
	 */
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
			$this->setBlockSetting($block_id, 'count_placement', Filter::post('count_placement', 'before|after', 'before'));

			return;
		}

		$num             = $this->getBlockSetting($block_id, 'num', '10');
		$count_placement = $this->getBlockSetting($block_id, 'count_placement', 'before');

		$options = [
			'before' => /* I18N: An option in a list-box */ I18N::translate('before'),
			'after'  => /* I18N: An option in a list-box */ I18N::translate('after'),
		];

		echo view('blocks/top-page-views-config', [
			'count_placement' => $count_placement,
			'num'             => $num,
			'options'         => $options,
		]);
	}
}
