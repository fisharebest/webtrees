<?php
namespace Fisharebest\Webtrees\Controller;

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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Source;

/**
 * Class SourceController - Controller for the source page
 */
class SourceController extends GedcomRecordController {
	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		$xref         = Filter::get('sid', WT_REGEX_XREF);
		$this->record = Source::getInstance($xref, $WT_TREE);

		parent::__construct();
	}

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
			$fact    = $this->record->getFirstFact('TITL');
			$submenu = new Menu(I18N::translate('Edit source'), '#', 'menu-sour-edit');
			if ($fact) {
				// Edit existing name
				$submenu->setOnclick('return edit_record(\'' . $this->record->getXref() . '\', \'' . $fact->getFactId() . '\');');
			} else {
				// Add new name
				$submenu->setOnclick('return add_fact(\'' . $this->record->getXref() . '\', \'TITL\');');
			}
			$menu->addSubmenu($submenu);
		}

		// delete
		if (Auth::isEditor($this->record->getTree())) {
			$submenu = new Menu(I18N::translate('Delete'), '#', 'menu-sour-del');
			$submenu->setOnclick("return delete_source('" . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$submenu = new Menu(I18N::translate('Edit raw GEDCOM'), '#', 'menu-sour-editraw');
			$submenu->setOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$submenu = new Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-sour-addfav'
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
