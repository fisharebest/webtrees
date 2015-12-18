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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Note;

/**
 * Controller for the shared note page
 */
class NoteController extends GedcomRecordController {
	/**
	 * get edit menu
	 */
	public function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-note');

		if (Auth::isEditor($this->record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit note'), '#', 'menu-note-edit', array(
				'onclick' => 'return edit_note("' . $this->record->getXref() . '");',
			)));
		}

		// delete
		if (Auth::isEditor($this->record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-note-del', array(
				'onclick' => 'return delete_note("' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . '", "' . $this->record->getXref() . '");',
			)));
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$menu->addSubmenu(new Menu(
			/* I18N: Menu option.  Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-note-addfav',
				array(
					'onclick' => 'jQuery.post("module.php?mod=user_favorites&mod_action=menu-add-favorite",{xref:"' . $this->record->getXref() . '"},function(){location.reload();})',
				)));
		}

		// Get the link for the first submenu and set it as the link for the main menu
		if ($menu->getSubmenus()) {
			$submenus = $menu->getSubmenus();
			$menu->setLink($submenus[0]->getLink());
			$menu->setAttrs($submenus[0]->getAttrs());
		}

		return $menu;
	}
}
