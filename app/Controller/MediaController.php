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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;

/**
 * Class MediaController - Controller for the media page
 */
class MediaController extends GedcomRecordController {
	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		$xref         = Filter::get('mid', WT_REGEX_XREF);
		$this->record = Media::getInstance($xref, $WT_TREE);

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
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-obje');

		if (Auth::isEditor($this->record->getTree())) {
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
		if (Auth::isEditor($this->record->getTree())) {
			$submenu = new Menu(I18N::translate('Delete'), '#', 'menu-obje-del');
			$submenu->setOnclick("return delete_media('" . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
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
	public function getFacts() {
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

		Functions::sortFacts($facts);

		return $facts;
	}

	/**
	 * Edit menu items used in media list
	 *
	 * @param Media $mediaobject
	 *
	 * @return string
	 */
	public static function getMediaListMenu(Media $mediaobject) {
		$html = '';

		$menu = new Menu(I18N::translate('Edit details'), '#', 'lb-image_edit', "return window.open('addmedia.php?action=editmedia&amp;pid=" . $mediaobject->getXref() . "', '_blank', edit_window_specs);");
		$html .= '<ul class="makeMenu lb-menu">' . $menu->getMenuAsList() . '</ul>';

		$menu = new Menu(I18N::translate('Manage links'), '#', 'lb-image_link', "return ilinkitem('" . $mediaobject->getXref() . "','person')", array(
			new Menu(I18N::translate('Link this media object to an individual'), '#', '', "return ilinkitem('" . $mediaobject->getXref() . "','person')"),
			new Menu(I18N::translate('Link this media object to a family'), '#', '', "return ilinkitem('" . $mediaobject->getXref() . "','family')"),
			new Menu(I18N::translate('Link this media object to a source'), '#', '', "return ilinkitem('" . $mediaobject->getXref() . "','source')"),
		));
		$html .= '<ul class="makeMenu lb-menu">' . $menu->getMenuAsList() . '</ul>';

		$menu = new Menu(I18N::translate('View details'), $mediaobject->getHtmlUrl(), 'lb-image_view');
		$html .= '<ul class="makeMenu lb-menu">' . $menu->getMenuAsList() . '</ul>';

		return '<div class="lightbox-menu">' . $html . '</div>';
	}
}
