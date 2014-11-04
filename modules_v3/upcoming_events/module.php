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

class upcoming_events_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Upcoming events');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Upcoming events” module */ WT_I18N::translate('A list of the anniversaries that will occur in the near future.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype;

		require_once WT_ROOT.'includes/functions/functions_print_lists.php';

		$days      = get_block_setting($block_id, 'days',      7);
		$filter    = get_block_setting($block_id, 'filter',    true);
		$onlyBDM   = get_block_setting($block_id, 'onlyBDM',   false);
		$infoStyle = get_block_setting($block_id, 'infoStyle', 'table');
		$sortStyle = get_block_setting($block_id, 'sortStyle', 'alpha');
		$block     = get_block_setting($block_id, 'block',     true);
		if ($cfg) {
			foreach (array('days', 'filter', 'onlyBDM', 'infoStyle', 'sortStyle', 'block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}

		$startjd = WT_CLIENT_JD + 1;
		$endjd   = WT_CLIENT_JD + $days;

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . WT_I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = '';
		switch ($infoStyle) {
		case 'list':
			// Output style 1:  Old format, no visible tables, much smaller text.  Better suited to right side of page.
			$content .= print_events_list($startjd, $endjd, $onlyBDM ? 'BIRT MARR DEAT' : '', $filter, $sortStyle);
			break;
		case 'table':
			// Style 2: New format, tables, big text, etc.  Not too good on right side of page
			ob_start();
			$content .= print_events_table($startjd, $endjd, $onlyBDM ? 'BIRT MARR DEAT' : '', $filter, $sortStyle);
			$content .= ob_get_clean();
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

	// Implement class WT_Module_Block
	public function loadAjax() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'days',      WT_Filter::postInteger('days', 1, 30, 7));
			set_block_setting($block_id, 'filter',    WT_Filter::postBool('filter'));
			set_block_setting($block_id, 'onlyBDM',   WT_Filter::postBool('onlyBDM'));
			set_block_setting($block_id, 'infoStyle', WT_Filter::post('infoStyle', 'list|table', 'table'));
			set_block_setting($block_id, 'sortStyle', WT_Filter::post('sortStyle', 'alpha|anniv', 'alpha'));
			set_block_setting($block_id, 'block',     WT_Filter::postBool('block'));
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$days=get_block_setting($block_id, 'days', 7);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Number of days to show');
		echo '</td><td class="optionbox">';
		echo '<input type="text" name="days" size="2" value="', $days, '">';
		echo ' <em>', WT_I18N::plural('maximum %d day', 'maximum %d days', 30, 30) ,'</em>';
		echo '</td></tr>';

		$filter=get_block_setting($block_id, 'filter', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show only events of living individuals?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('filter', $filter);
		echo '</td></tr>';

		$onlyBDM=get_block_setting($block_id, 'onlyBDM', false);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Show only births, deaths, and marriages?');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('onlyBDM', $onlyBDM);
		echo '</td></tr>';

		$infoStyle=get_block_setting($block_id, 'infoStyle', 'table');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Presentation style');
		echo '</td><td class="optionbox">';
		echo select_edit_control('infoStyle', array('list'=>WT_I18N::translate('list'), 'table'=>WT_I18N::translate('table')), null, $infoStyle, '');
		echo '</td></tr>';

		$sortStyle=get_block_setting($block_id, 'sortStyle',  'alpha');
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Sort order');
		echo '</td><td class="optionbox">';
		echo select_edit_control('sortStyle', array(
			/* I18N: An option in a list-box */ 'alpha'=>WT_I18N::translate('sort by name'),
			/* I18N: An option in a list-box */ 'anniv'=>WT_I18N::translate('sort by date'),
		), null, $sortStyle, '');
		echo '</td></tr>';

		$block=get_block_setting($block_id, 'block', true);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo /* I18N: label for a yes/no option */ WT_I18N::translate('Add a scrollbar when block contents grow');
		echo '</td><td class="optionbox">';
		echo edit_field_yes_no('block', $block);
		echo '</td></tr>';
	}
}
