<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

class top10_surnames_WT_Module extends WT_Module implements WT_Module_Block {
	/**
	 * {@inheritdoc}
	 */
	public function getTitle() {
		return /* I18N: Name of a module.  Top=Most common */ WT_I18N::translate('Top surnames');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription() {
		return /* I18N: Description of the “Top surnames” module */ WT_I18N::translate('A list of the most popular surnames.');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $WT_TREE, $ctype;

		require_once WT_ROOT . 'includes/functions/functions_print_lists.php';

		$COMMON_NAMES_REMOVE    = $WT_TREE->getPreference('COMMON_NAMES_REMOVE');
		$COMMON_NAMES_THRESHOLD = $WT_TREE->getPreference('COMMON_NAMES_THRESHOLD');

		$num       = get_block_setting($block_id, 'num', 10);
		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		$block     = get_block_setting($block_id, 'block', false);
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
				unset($top_surnames[WT_I18N::strtoupper($delname)]);
			}
		}

		$all_surnames = array();
		$i            = 0;
		foreach (array_keys($top_surnames) as $top_surname) {
			$all_surnames = array_merge($all_surnames, WT_Query_Name::surnames($top_surname, '', false, false, WT_GED_ID));
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
			$title = '<i class="icon-admin" title="' . WT_I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}

		if ($num == 1) {
			// I18N: i.e. most popular surname.
			$title .= WT_I18N::translate('Top surname');
		} else {
			// I18N: Title for a list of the most common surnames, %s is a number.  Note that a separate translation exists when %s is 1
			$title .= WT_I18N::plural('Top %s surname', 'Top %s surnames', $num, WT_I18N::number($num));
		}

		switch ($infoStyle) {
		case 'tagcloud':
			uksort($all_surnames, array('WT_I18N', 'strcasecmp'));
			$content = format_surname_tagcloud($all_surnames, 'indilist.php', true);
			break;
		case 'list':
			uasort($all_surnames, array('top10_surnames_WT_Module', 'surnameCountSort'));
			$content = format_surname_list($all_surnames, '1', true, 'indilist.php');
			break;
		case 'array':
			uasort($all_surnames, array('top10_surnames_WT_Module', 'surnameCountSort'));
			$content = format_surname_list($all_surnames, '2', true, 'indilist.php');
			break;
		case 'table':
		default:
			uasort($all_surnames, array('top10_surnames_WT_Module', 'surnameCountSort'));
			$content = format_surname_table($all_surnames, 'indilist.php');
			break;
		}

		if ($template) {
			if ($block) {
				require WT_THEME_DIR . 'templates/block_small_temp.php';
			} else {
				require WT_THEME_DIR . 'templates/block_main_temp.php';
			}
		} else {

			return $content;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadAjax() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isUserBlock() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'num', WT_Filter::postInteger('num', 1, 10000, 10));
			set_block_setting($block_id, 'infoStyle', WT_Filter::post('infoStyle', 'list|array|table|tagcloud', 'table'));
			set_block_setting($block_id, 'block', WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT . 'includes/functions/functions_edit.php';

		$num = get_block_setting($block_id, 'num', 10);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Number of items to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="num" size="2" value="', $num, '">';
		echo '</td></tr>';

		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list' => WT_I18N::translate('bullet list'), 'array' => WT_I18N::translate('compact list'), 'table' => WT_I18N::translate('table'), 'tagcloud' => WT_I18N::translate('tag cloud')), null, $infoStyle, '');
		echo '</td></tr>';

		$block = get_block_setting($block_id, 'block', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
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
