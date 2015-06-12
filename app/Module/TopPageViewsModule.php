<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

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
	public function getBlock($block_id, $template = true, $cfg = array()) {
		global $ctype, $WT_TREE;

		$num             = $this->getBlockSetting($block_id, 'num', '10');
		$count_placement = $this->getBlockSetting($block_id, 'count_placement', 'before');
		$block           = $this->getBlockSetting($block_id, 'block', '0');

		foreach (array('count_placement', 'num', 'block') as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = '<a class="icon-admin" title="' . I18N::translate('Configure') . '" href="block_edit.php?block_id=' . $block_id . '&amp;ged=' . $WT_TREE->getNameHtml() . '&amp;ctype=' . $ctype . '"></a>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = "";
		// load the lines from the file
		$top10 = Database::prepare(
			"SELECT page_parameter, page_count" .
			" FROM `##hit_counter`" .
			" WHERE gedcom_id = :tree_id AND page_name IN ('individual.php','family.php','source.php','repo.php','note.php','mediaviewer.php')" .
			" ORDER BY page_count DESC LIMIT :limit"
		)->execute(array(
			'tree_id' => $WT_TREE->getTreeId(),
			'limit'   => (int) $num,
		))->FetchAssoc();

		if ($block) {
			$content .= "<table width=\"90%\">";
		} else {
			$content .= "<table>";
		}
		foreach ($top10 as $id => $count) {
			$record = GedcomRecord::getInstance($id, $WT_TREE);
			if ($record && $record->canShow()) {
				$content .= '<tr valign="top">';
				if ($count_placement == 'before') {
					$content .= '<td dir="ltr" align="right">[' . $count . ']</td>';
				}
				$content .= '<td class="name2" ><a href="' . $record->getHtmlUrl() . '">' . $record->getFullName() . '</a></td>';
				if ($count_placement == 'after') {
					$content .= '<td dir="ltr" align="right">[' . $count . ']</td>';
				}
				$content .= '</tr>';
			}
		}
		$content .= "</table>";

		if ($template) {
			if ($block) {
				$class .= ' small_inner_block';
			}

			return Theme::theme()->formatBlock($id, $title, $class, $content);
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
	public function loadAjax() {
		return true;
	}

	/**
	 * Can this block be shown on the user’s home page?
	 *
	 * @return bool
	 */
	public function isUserBlock() {
		return false;
	}

	/**
	 * Can this block be shown on the tree’s home page?
	 *
	 * @return bool
	 */
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
			$this->setBlockSetting($block_id, 'num', Filter::postInteger('num', 1, 10000, 10));
			$this->setBlockSetting($block_id, 'count_placement', Filter::post('count_placement', 'before|after', 'before'));
			$this->setBlockSetting($block_id, 'block', Filter::postBool('block'));
		}

		$num             = $this->getBlockSetting($block_id, 'num', '10');
		$count_placement = $this->getBlockSetting($block_id, 'count_placement', 'before');
		$block           = $this->getBlockSetting($block_id, 'block', '0');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '">';
		echo '</td></tr>';

		echo "<tr><td class=\"descriptionbox wrap width33\">";
		echo I18N::translate('Place counts before or after name?');
		echo "</td><td class=\"optionbox\">";
		echo FunctionsEdit::selectEditControl('count_placement', array('before' => I18N::translate('before'), 'after' => I18N::translate('after')), null, $count_placement, '');
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo FunctionsEdit::editFieldYesNo('block', $block);
		echo '</td></tr>';
	}
}
