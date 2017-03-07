<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Fisharebest\Webtrees\Module;

/**
 * Controller for the repository page
 */
class RepositoryController extends GedcomRecordController {
	/**
	 * get edit menu
	 */
	public function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-repo');

		if (Auth::isEditor($this->record->getTree())) {
			$fact = $this->record->getFirstFact('NAME');
			if ($fact) {
				// Edit existing name
				$menu->addSubmenu(new Menu(I18N::translate('Edit the repository'), 'edit_interface.php?action=edit&amp;xref=' . $this->record->getXref() . '&amp;fact_id=' . $fact->getFactId() . '&amp;ged=' . $this->record->getTree()->getNameHtml(), 'menu-repo-edit'));
			} else {
				// Add new name
				$menu->addSubmenu(new Menu(I18N::translate('Edit the repository'), 'edit_interface.php?action=add&amp;fact=NAME&amp;xref=' . $this->record->getXref() . '&amp;ged=' . $this->record->getTree()->getNameHtml(), 'menu-repo-edit'));
			}
		}

		// delete
		if (Auth::isEditor($this->record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-repo-del', [
				'onclick' => 'return delete_record("' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJs(Filter::unescapeHtml($this->record->getFullName()))) . '", "' . $this->record->getXref() . '");',
			]));
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit the raw GEDCOM'), 'edit_interface.php?action=editraw&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref(), 'menu-repo-editraw'));
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$menu->addSubmenu(new Menu(
				/* I18N: Menu option. Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-repo-addfav',
				[
					'onclick' => 'jQuery.post("module.php?mod=user_favorites&mod_action=menu-add-favorite" ,{xref:"' . $this->record->getXref() . '"},function(){location.reload();})',
				]
			));
		}

		return $menu;
	}
}
