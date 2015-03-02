<?php
namespace Fisharebest\Webtrees;

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

/**
 * Class TopSurnamesModule
 */
class TopSurnamesModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module.  Top=Most common */ I18N::translate('Top surnames');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Top surnames” module */ I18N::translate('A list of the most popular surnames.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $WT_TREE, $ctype;

		$COMMON_NAMES_REMOVE    = $WT_TREE->getPreference('COMMON_NAMES_REMOVE');
		$COMMON_NAMES_THRESHOLD = $WT_TREE->getPreference('COMMON_NAMES_THRESHOLD');

		$num       = get_block_setting($block_id, 'num', '10');
		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		$block     = get_block_setting($block_id, 'block', '0');

		if ($cfg) {
			foreach (array('num', 'infoStyle', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		// This next function is a bit out of date, and doesn't cope well with surname variants
		$top_surnames = get_top_surnames(WT_GED_ID, $COMMON_NAMES_THRESHOLD, $num);

		// Remove names found in the "Remove Names" list
		if ($COMMON_NAMES_REMOVE) {
			foreach (preg_split("/[,; ]+/", $COMMON_NAMES_REMOVE) as $delname) {
				unset($top_surnames[$delname]);
				unset($top_surnames[I18N::strtoupper($delname)]);
			}
		}

		$all_surnames = array();
		$i            = 0;
		foreach (array_keys($top_surnames) as $top_surname) {
			$all_surnames = array_merge($all_surnames, QueryName::surnames($top_surname, '', false, false, WT_GED_ID));
			if (++$i == $num) {
				break;
			}
		}
		if ($i < $num) {
			$num = $i;
		}
		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}

		if ($num == 1) {
			// I18N: i.e. most popular surname.
			$title .= I18N::translate('Top surname');
		} else {
			// I18N: Title for a list of the most common surnames, %s is a number.  Note that a separate translation exists when %s is 1
			$title .= I18N::plural('Top %s surname', 'Top %s surnames', $num, I18N::number($num));
		}

		switch ($infoStyle) {
		case 'tagcloud':
			uksort($all_surnames, __NAMESPACE__ . '\I18N::strcasecmp');
			$content = format_surname_tagcloud($all_surnames, 'indilist.php', true);
			break;
		case 'list':
			uasort($all_surnames, __NAMESPACE__ . '\\TopSurnamesModule::surnameCountSort');
			$content = format_surname_list($all_surnames, '1', true, 'indilist.php', $WT_TREE);
			break;
		case 'array':
			uasort($all_surnames, __NAMESPACE__ . '\\TopSurnamesModule::surnameCountSort');
			$content = format_surname_list($all_surnames, '2', true, 'indilist.php', $WT_TREE);
			break;
		case 'table':
		default:
			uasort($all_surnames, __NAMESPACE__ . '\\TopSurnamesModule::surnameCountSort');
			$content = format_surname_table($all_surnames, 'indilist.php');
			break;
		}

		if ($template) {
			if ($block) {
				$class .= ' small_inner_block';
			}
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

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			set_block_setting($block_id, 'num', Filter::postInteger('num', 1, 10000, 10));
			set_block_setting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|array|table|tagcloud', 'table'));
			set_block_setting($block_id, 'block', Filter::postBool('block'));
		}

		$num       = get_block_setting($block_id, 'num', '10');
		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		$block     = get_block_setting($block_id, 'block', '0');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '">';
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list' => I18N::translate('bullet list'), 'array' => I18N::translate('compact list'), 'table' => I18N::translate('table'), 'tagcloud' => I18N::translate('tag cloud')), null, $infoStyle, '');
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}

	/**
	 * Sort (lists of counts of similar) surname by total count.
	 *
	 * @param string[] $a
	 * @param string[] $b
	 *
	 * @return integer
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
