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
 * Class RecentChangesModule
 */
class RecentChangesModule extends Module implements ModuleBlockInterface {
	const DEFAULT_DAYS = 7;
	const MAX_DAYS = 90;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Recent changes');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Recent changes” module */ I18N::translate('A list of records that have been updated recently.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype;

		$days       = get_block_setting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle  = get_block_setting($block_id, 'infoStyle', 'table');
		$sortStyle  = get_block_setting($block_id, 'sortStyle', 'date_desc');
		$block      = get_block_setting($block_id, 'block', '1');
		$hide_empty = get_block_setting($block_id, 'hide_empty', '0');


		if ($cfg) {
			foreach (array('days', 'infoStyle', 'sortStyle', 'hide_empty', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}

		$found_facts = get_recent_changes(WT_CLIENT_JD - $days);

		if (!$found_facts && $hide_empty) {
			return '';
		}
		// Print block header
		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}
		$title .= /* I18N: title for list of recent changes */ I18N::plural('Changes in the last %s day', 'Changes in the last %s days', $days, I18N::number($days));

		$content = '';
		// Print block content
		if (count($found_facts) == 0) {
			$content .= I18N::plural('There have been no changes within the last %s day.', 'There have been no changes within the last %s days.', $days, I18N::number($days));
		} else {
			ob_start();
			switch ($infoStyle) {
			case 'list':
				$content .= print_changes_list($found_facts, $sortStyle);
				break;
			case 'table':
				// sortable table
				$content .= print_changes_table($found_facts, $sortStyle);
				break;
			}
			$content .= ob_get_clean();
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
			set_block_setting($block_id, 'days', Filter::postInteger('days', 1, self::MAX_DAYS, self::DEFAULT_DAYS));
			set_block_setting($block_id, 'infoStyle', Filter::post('infoStyle', 'list|table', 'table'));
			set_block_setting($block_id, 'sortStyle', Filter::post('sortStyle', 'name|date_asc|date_desc', 'date_desc'));
			set_block_setting($block_id, 'hide_empty', Filter::postBool('hide_empty'));
			set_block_setting($block_id, 'block', Filter::postBool('block'));
		}

		$days       = get_block_setting($block_id, 'days', self::DEFAULT_DAYS);
		$infoStyle  = get_block_setting($block_id, 'infoStyle', 'table');
		$sortStyle  = get_block_setting($block_id, 'sortStyle', 'date_desc');
		$block      = get_block_setting($block_id, 'block', '1');
		$hide_empty = get_block_setting($block_id, 'hide_empty', '0');

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Number of days to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="', $days, '">';
		echo ' <em>', I18N::plural('maximum %d day', 'maximum %d days', self::MAX_DAYS, self::MAX_DAYS), '</em>';
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list' => I18N::translate('list'), 'table' => I18N::translate('table')), null, $infoStyle, '');
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Sort order');
		echo '</td><td class="optionbox">';
		echo select_edit_control('sortStyle', array(
			'name'      => /* I18N: An option in a list-box */ I18N::translate('sort by name'),
			'date_asc'  => /* I18N: An option in a list-box */ I18N::translate('sort by date, oldest first'),
			'date_desc' => /* I18N: An option in a list-box */ I18N::translate('sort by date, newest first')
		), null, $sortStyle, '');
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Should this block be hidden when it is empty?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('hide_empty', $hide_empty);
		echo '</td></tr>';
		echo '<tr><td colspan="2" class="optionbox wrap">';
		echo '<span class="error">', I18N::translate('If you hide an empty block, you will not be able to change its configuration until it becomes visible by no longer being empty.'), '</span>';
		echo '</td></tr>';
	}

}
