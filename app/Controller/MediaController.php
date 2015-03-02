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
 * Class MediaController - Controller for the media page
 */
class MediaController extends GedcomRecordController {
	/**
	 * Startup activity
	 */
	public function __construct() {
		$xref = Filter::get('mid', WT_REGEX_XREF);
		$this->record = Media::getInstance($xref);

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
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-obje');

		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(I18N::translate('Edit media object'), '#', 'menu-obje-edit');
			$submenu->setOnclick("window.open('addmedia.php?action=editmedia&amp;pid={$this->record->getXref()}', '_blank', edit_window_specs)");
			$menu->addSubmenu($submenu);

			// main link displayed on page
			if (Module::getModuleByName('GEDFact_assistant')) {
				$submenu = new Menu(I18N::translate('Manage links'), '#', 'menu-obje-link');
				$submenu->setOnclick("return ilinkitem('" . $this->record->getXref() . "','manage');");
				$menu->addSubmenu($submenu);
			} else {
				$submenu = new Menu(I18N::translate('Link this media object to an individual'), '#', 'menu-obje-link-indi');
				$submenu->setOnclick("return ilinkitem('" . $this->record->getXref() . "','person');");
				$menu->addSubmenu($submenu);

				$submenu = new Menu(I18N::translate('Link this media object to a family'), '#', 'menu-obje-link-fam');
				$submenu->setOnclick("return ilinkitem('" . $this->record->getXref() . "','family');");
				$menu->addSubmenu($submenu);

				$submenu = new Menu(I18N::translate('Link this media object to a source'), '#', 'menu-obje-link-sour');
				$submenu->setOnclick("return ilinkitem('" . $this->record->getXref() . "','source');");
				$menu->addSubmenu($submenu);
			}
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new Menu(I18N::translate('Delete'), '#', 'menu-obje-del');
			$submenu->setOnclick("return delete_media('" . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || WT_USER_CAN_EDIT && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$submenu = new Menu(I18N::translate('Edit raw GEDCOM'), '#', 'menu-obje-editraw');
			$submenu->setOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$submenu = new Menu(
			/* I18N: Menu option.  Add [the current page] to the list of favorites */
				I18N::translate('Add to favorites'),
				'#',
				'menu-obje-addfav'
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

	/**
	 * Return a list of facts
	 *
	 * @return Fact[]
	 */
	function getFacts() {
		$facts = $this->record->getFacts();

		// Add some dummy facts to show additional information
		if ($this->record->fileExists()) {
			// get height and width of image, when available
			$imgsize = $this->record->getImageAttributes();
			if (!empty($imgsize['WxH'])) {
				$facts[] = new Fact('1 __IMAGE_SIZE__ ' . $imgsize['WxH'], $this->record, 0);
			}
			//Prints the file size
			$facts[] = new Fact('1 __FILE_SIZE__ ' . $this->record->getFilesize(), $this->record, 0);
		}

		sort_facts($facts);

		return $facts;
	}

	/**
	 * Edit menu items used in media list
	 *
	 * @param Media $mediaobject
	 *
	 * @return string
	 */
	static function getMediaListMenu(Media $mediaobject) {
		$html = '<div class="lightbox-menu"><ul class="makeMenu lb-menu">';
		$menu = new Menu(I18N::translate('Edit details'));
		$menu->addClass('', '', 'lb-image_edit');
		$menu->setOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=" . $mediaobject->getXref() . "', '_blank', edit_window_specs);");
		$html .= $menu->getMenuAsList();
		$menu = new Menu(I18N::translate('Manage links'));
		$menu->addClass('', '', 'lb-image_link');
		$menu->setOnclick("return ilinkitem('" . $mediaobject->getXref() . "','person')");
		$submenu = new Menu(I18N::translate('Link this media object to an individual'), '#');
		$submenu->setOnclick("return ilinkitem('" . $mediaobject->getXref() . "','person')");
		$menu->addSubmenu($submenu);
		$submenu = new Menu(I18N::translate('Link this media object to a family'), '#');
		$submenu->setOnclick("return ilinkitem('" . $mediaobject->getXref() . "','family')");
		$menu->addSubmenu($submenu);
		$submenu = new Menu(I18N::translate('Link this media object to a source'), '#');
		$submenu->setOnclick("return ilinkitem('" . $mediaobject->getXref() . "','source')");
		$menu->addSubmenu($submenu);
		$html .= $menu->getMenuAsList();
		$menu = new Menu(I18N::translate('View details'), $mediaobject->getHtmlUrl());
		$menu->addClass('', '', 'lb-image_view');
		$html .= $menu->getMenuAsList();
		$html .= '</ul></div>';

		return $html;
	}
}
