<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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

require_once WT_ROOT.'includes/functions/functions_print_facts.php';

/**
 * Class WT_Controller_Family - Controller for the family page
 */
class WT_Controller_Family extends WT_Controller_GedcomRecord {
	/**
	 * Startup activity
	 */
	public function __construct() {
		global $Dbwidth, $bwidth, $pbwidth, $pbheight, $bheight;
		$bwidth   = $Dbwidth;
		$pbwidth  = $bwidth + 12;
		$pbheight = $bheight + 14;

		$xref         = WT_Filter::get('famid', WT_REGEX_XREF);
		$this->record = WT_Family::getInstance($xref);

		parent::__construct();
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Individual
	 */
	public function getSignificantIndividual() {
		if ($this->record) {
			foreach ($this->record->getSpouses() as $individual) {
				return $individual;
			}
			foreach ($this->record->getChildren() as $individual) {
				return $individual;
			}
		}
		return parent::getSignificantIndividual();
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Family
	 */
	public function getSignificantFamily() {
		if ($this->record) {
			return $this->record;
		}
		return parent::getSignificantFamily();
	}

	/**
	 * @param string[] $tags an array of HUSB/WIFE/CHIL
	 *
	 * @return string
	 */
	function getTimelineIndis($tags) {
		preg_match_all('/\n1 (?:'.implode('|', $tags).') @('.WT_REGEX_XREF.')@/', $this->record->getGedcom(), $matches);
		foreach ($matches[1] as &$match) {
			$match='pids%5B%5D='.$match;
		}
		return implode('&amp;', $matches[1]);
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
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-fam');
		$menu->addLabel($menu->label, 'down');

		if (WT_USER_CAN_EDIT) {
			// edit_fam / members
			$submenu = new WT_Menu(WT_I18N::translate('Change family members'), '#', 'menu-fam-change');
			$submenu->addOnclick("return change_family_members('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);

			// edit_fam / add child
			$submenu = new WT_Menu(WT_I18N::translate('Add a child to this family'), '#', 'menu-fam-addchil');
			$submenu->addOnclick("return add_child_to_family('".$this->record->getXref()."', 'U');");
			$menu->addSubmenu($submenu);

			// edit_fam / reorder_children
			if ($this->record->getNumberOfChildren() > 1) {
				$submenu = new WT_Menu(WT_I18N::translate('Re-order children'), '#', 'menu-fam-orderchil');
				$submenu->addOnclick("return reorder_children('".$this->record->getXref()."');");
				$menu->addSubmenu($submenu);
			}
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-fam-del');
			$submenu->addOnclick("return delete_family('" . WT_I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?') . "', '".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || WT_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM'), '#', 'menu-fam-editraw');
			$submenu->addOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$submenu = new WT_Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ WT_I18N::translate('Add to favorites'),
				'#',
				'menu-fam-addfav'
			);
			$submenu->addOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'".$this->record->getXref()."'},function(){location.reload();})");
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
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return string
	 */
	public function getSignificantSurname() {
		if ($this->record && $this->record->getHusband()) {
			list($surn) = explode(',', $this->record->getHusband()->getSortname());
			return $surn;
		} else {
			return '';
		}
	}

	/**
	 * Print the facts
	 */
	public function printFamilyFacts() {
		global $linkToID, $WT_TREE;

		$linkToID = $this->record->getXref(); // -- Tell addmedia.php what to link to

		$indifacts = $this->record->getFacts();
		if ($indifacts) {
			sort_facts($indifacts);
			foreach ($indifacts as $fact) {
				print_fact($fact, $this->record);
			}
		} else {
			echo '<tr><td class="messagebox" colspan="2">', WT_I18N::translate('No facts for this family.'), '</td></tr>';
		}

		if (WT_USER_CAN_EDIT) {
			print_add_new_fact($this->record->getXref(), $indifacts, 'FAM');

			echo '<tr><td class="descriptionbox">';
			echo WT_Gedcom_Tag::getLabel('NOTE');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('".$this->record->getXref()."','NOTE');\">", WT_I18N::translate('Add a new note'), '</a>';
			echo help_link('add_note');
			echo '</td></tr>';

			echo '<tr><td class="descriptionbox">';
			echo WT_Gedcom_Tag::getLabel('SHARED_NOTE');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('".$this->record->getXref()."','SHARED_NOTE');\">", WT_I18N::translate('Add a new shared note'), '</a>';
			echo help_link('add_shared_note');
			echo '</td></tr>';

			if ($WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
				echo '<tr><td class="descriptionbox">';
				echo WT_Gedcom_Tag::getLabel('OBJE');
				echo '</td><td class="optionbox">';
				echo "<a href=\"#\" onclick=\"window.open('addmedia.php?action=showmediaform&amp;linktoid=".$this->record->getXref()."', '_blank', edit_window_specs); return false;\">", WT_I18N::translate('Add a new media object'), '</a>';
				echo help_link('OBJE');
				echo '<br>';
				echo "<a href=\"#\" onclick=\"window.open('inverselink.php?linktoid=".$this->record->getXref()."&amp;linkto=family', '_blank', find_window_specs); return false;\">", WT_I18N::translate('Link to an existing media object'), '</a>';
				echo '</td></tr>';
			}

			echo '<tr><td class="descriptionbox">';
			echo WT_Gedcom_Tag::getLabel('SOUR');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('".$this->record->getXref()."','SOUR');\">", WT_I18N::translate('Add a new source citation'), '</a>';
			echo help_link('add_source');
			echo '</td></tr>';
		}
	}
}
