<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

/**
 * Class extra_info_WT_Module
 * A sidebar to show non-genealogical information about an individual
 */
class extra_info_WT_Module extends WT_Module implements WT_Module_Sidebar {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ WT_I18N::translate('Extra information');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Extra information” module */ WT_I18N::translate('A sidebar showing non-genealogical information about an individual.');
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 10;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		return true;
	}

	/** {@inheritdoc} */
	public function getSidebarContent() {
		global $WT_TREE, $controller;

		$indifacts = array();
		// The individual’s own facts
		foreach ($controller->record->getFacts() as $fact) {
			if (self::showFact($fact)) {
				$indifacts[] = $fact;
			}
		}

		ob_start();
		if (!$indifacts) {
			echo WT_I18N::translate('There are no facts for this individual.');
		} else {
			foreach ($indifacts as $fact) {
				print_fact($fact, $controller->record);
			}
		}
		if ($WT_TREE->getPreference('SHOW_COUNTER')) {
			$hitCount = 0;
			require WT_ROOT . 'includes/hitcount.php';
			echo '<div id="hitcounter">', WT_I18N::translate('Hit count:'), ' ', $hitCount, '</div>';
		}
		return strip_tags(ob_get_clean(), '<a><div><span>');
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		return '';
	}

	/**
	 * Does this module display a particular fact
	 *
	 * @param WT_Fact $fact
	 *
	 * @return boolean
	 */
	public static function showFact(WT_Fact $fact) {
		switch ($fact->getTag()) {
		case 'AFN':
		case 'CHAN':
		case 'IDNO':
		case 'REFN':
		case 'RFN':
		case 'RIN':
		case 'SSN':
		case '_UID':
			return true;
		default:
			return false;
		}
	}
}
