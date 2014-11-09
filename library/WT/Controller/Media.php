<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

require_once WT_ROOT . 'includes/functions/functions_print_facts.php';

/**
 * Class WT_Controller_Media - Controller for the media page
 */
class WT_Controller_Media extends WT_Controller_GedcomRecord {
	/**
	 * Startup activity
	 */
	public function __construct() {
		$xref = WT_Filter::get('mid', WT_REGEX_XREF);
		$this->record = WT_Media::getInstance($xref);

		parent::__construct();
	}

	/**
	 * get edit menu
	 */
	function getEditMenu() {
		global $WT_TREE;

		$SHOW_GEDCOM_RECORD = $WT_TREE->getPreference('SHOW_GEDCOM_RECORD');

		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-obje');

		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit media object'), '#', 'menu-obje-edit');
			$submenu->addOnclick("window.open('addmedia.php?action=editmedia&pid={$this->record->getXref()}', '_blank', edit_window_specs)");
			$menu->addSubmenu($submenu);

			// main link displayed on page
			if (array_key_exists('GEDFact_assistant', WT_Module::getActiveModules())) {
				$submenu = new WT_Menu(WT_I18N::translate('Manage links'), '#', 'menu-obje-link');
				$submenu->addOnclick("return ilinkitem('" . $this->record->getXref() . "','manage');");
			} else {
				$submenu = new WT_Menu(WT_I18N::translate('Set link'), '#', 'menu-obje-link');
				$ssubmenu = new WT_Menu(WT_I18N::translate('To individual'), '#', 'menu-obje-link-indi');
				$ssubmenu->addOnclick("return ilinkitem('" . $this->record->getXref() . "','person');");
				$submenu->addSubMenu($ssubmenu);

				$ssubmenu = new WT_Menu(WT_I18N::translate('To family'), '#', 'menu-obje-link-fam');
				$ssubmenu->addOnclick("return ilinkitem('" . $this->record->getXref() . "','family');");
				$submenu->addSubMenu($ssubmenu);

				$ssubmenu = new WT_Menu(WT_I18N::translate('To source'), '#', 'menu-obje-link-sour');
				$ssubmenu->addOnclick("return ilinkitem('" . $this->record->getXref() . "','source');");
				$submenu->addSubMenu($ssubmenu);
			}

			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-obje-del');
			$submenu->addOnclick("return delete_media('" . WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($this->record->getFullName())) . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || WT_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM'), '#', 'menu-obje-editraw');
			$submenu->addOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$submenu = new WT_Menu(
			/* I18N: Menu option.  Add [the current page] to the list of favorites */
				WT_I18N::translate('Add to favorites'),
				'#',
				'menu-obje-addfav'
			);
			$submenu->addOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'" . $this->record->getXref() . "'},function(){location.reload();})");
			$menu->addSubmenu($submenu);
		}

		//-- get the link for the first submenu and set it as the link for the main menu
		if (isset($menu->submenus[0])) {
			$link = $menu->submenus[0]->onclick;
			$menu->addOnclick($link);
		}

		return $menu;
	}

	/**
	 * Return a list of facts
	 *
	 * @return WT_Fact[]
	 */
	function getFacts() {
		$facts = $this->record->getFacts();

		// Add some dummy facts to show additional information
		if ($this->record->fileExists()) {
			// get height and width of image, when available
			$imgsize = $this->record->getImageAttributes();
			if (!empty($imgsize['WxH'])) {
				$facts[] = new WT_Fact('1 __IMAGE_SIZE__ ' . $imgsize['WxH'], $this->record, 0);
			}
			//Prints the file size
			$facts[] = new WT_Fact('1 __FILE_SIZE__ ' . $this->record->getFilesize(), $this->record, 0);
		}

		sort_facts($facts);

		return $facts;
	}

	/**
	 * Edit menu items used in media list
	 *
	 * @param WT_Media $mediaobject
	 *
	 * @return string
	 */
	static function getMediaListMenu(WT_Media $mediaobject) {
		$html = '<div class="lightbox-menu"><ul class="makeMenu lb-menu">';
		$menu = new WT_Menu(WT_I18N::translate('Edit details'));
		$menu->addClass('', '', 'lb-image_edit');
		$menu->addOnclick("return window.open('addmedia.php?action=editmedia&amp;pid=" . $mediaobject->getXref() . "', '_blank', edit_window_specs);");
		$html .= $menu->getMenuAsList();
		$menu = new WT_Menu(WT_I18N::translate('Set link'));
		$menu->addClass('', '', 'lb-image_link');
		$menu->addOnclick("return ilinkitem('" . $mediaobject->getXref() . "','person')");
		$submenu = new WT_Menu(WT_I18N::translate('To individual'), '#');
		$submenu->addOnclick("return ilinkitem('" . $mediaobject->getXref() . "','person')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To family'), '#');
		$submenu->addOnclick("return ilinkitem('" . $mediaobject->getXref() . "','family')");
		$menu->addSubMenu($submenu);
		$submenu = new WT_Menu(WT_I18N::translate('To source'), '#');
		$submenu->addOnclick("return ilinkitem('" . $mediaobject->getXref() . "','source')");
		$menu->addSubMenu($submenu);
		$html .= $menu->getMenuAsList();
		$menu = new WT_Menu(WT_I18N::translate('View details'), $mediaobject->getHtmlUrl());
		$menu->addClass('', '', 'lb-image_view');
		$html .= $menu->getMenuAsList();
		$html .= '</ul></div>';

		return $html;
	}
}
