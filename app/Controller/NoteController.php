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
 * Class NoteController - Controller for the shared note page
 */
class NoteController extends GedcomRecordController {
	/**
	 * Startup activity
	 */
	public function __construct() {
		$xref         = Filter::get('nid', WT_REGEX_XREF);
		$this->record = Note::getInstance($xref);

		parent::__construct();
	}

	/**
	 * get edit menu
	 */
	function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-note');

		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(I18N::translate('Edit note'), '#', 'menu-note-edit');
			$submenu->setOnclick('return edit_note(\'' . $this->record->getXref() . '\');');
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(I18N::translate('Delete'), '#', 'menu-note-del');
			$submenu->setOnclick("return delete_note('" . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$submenu = new Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-note-addfav'
			);
			$submenu->setOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'" . $this->record->getXref() . "'},function(){location.reload();})");
			$menu->addSubmenu($submenu);
		}

		// Get the link for the first submenu and set it as the link for the main menu
		if ($menu->getSubmenus()) {
			$submenus = $menu->getSubmenus();
			$menu->setLink($submenus[0]->getLink());
			$menu->setOnClick($submenus[0]->getOnClick());
		}

		return $menu;
	}
}
