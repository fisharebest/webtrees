<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;

/**
 * Controller for the source page
 */
class SourceController extends GedcomRecordController {
	/**
	 * get edit menu
	 */
	public function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-sour');

		if (Auth::isEditor($this->record->getTree())) {
			$fact = $this->record->getFirstFact('TITL');
			if ($fact) {
				// Edit existing name
				$menu->addSubmenu(new Menu(I18N::translate('Edit the source'), '#', 'menu-sour-edit', array(
					'onclick' => 'return edit_record("' . $this->record->getXref() . '", "' . $fact->getFactId() . '");',
				)));
			} else {
				// Add new name
				$menu->addSubmenu(new Menu(I18N::translate('Edit the source'), '#', 'menu-sour-edit', array(
					'onclick' => 'return add_fact("' . $this->record->getXref() . '", "TITL");',
				)));
			}

			// delete
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-sour-del', array(
				'onclick' => "return delete_record('" . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs(Filter::unescapeHtml($this->record->getFullName()))) . "', '" . $this->record->getXref() . "');",
			)));
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit the raw GEDCOM'), '#', 'menu-sour-editraw', array(
				'onclick' => 'return edit_raw("' . $this->record->getXref() . '");',
			)));
		}

		return $menu;
	}
}
