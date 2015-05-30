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
 * Class FamilyController - Controller for the family page
 */
class FamilyController extends GedcomRecordController {
	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		$xref         = Filter::get('famid', WT_REGEX_XREF);
		$this->record = Family::getInstance($xref, $WT_TREE);

		parent::__construct();
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return Individual
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
	 * @return Family
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
	public function getTimelineIndis($tags) {
		preg_match_all('/\n1 (?:' . implode('|', $tags) . ') @(' . WT_REGEX_XREF . ')@/', $this->record->getGedcom(), $matches);
		foreach ($matches[1] as &$match) {
			$match = 'pids%5B%5D=' . $match;
		}

		return implode('&amp;', $matches[1]);
	}

	/**
	 * get edit menu
	 */
	public function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-fam');

		if (Auth::isEditor($this->record->getTree())) {
			// edit_fam / members
			$submenu = new Menu(I18N::translate('Change family members'), '#', 'menu-fam-change');
			$submenu->setOnclick("return change_family_members('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);

			// edit_fam / add child
			$submenu = new Menu(I18N::translate('Add a child to this family'), '#', 'menu-fam-addchil');
			$submenu->setOnclick("return add_child_to_family('" . $this->record->getXref() . "', 'U');");
			$menu->addSubmenu($submenu);

			// edit_fam / reorder_children
			if ($this->record->getNumberOfChildren() > 1) {
				$submenu = new Menu(I18N::translate('Re-order children'), '#', 'menu-fam-orderchil');
				$submenu->setOnclick("return reorder_children('" . $this->record->getXref() . "');");
				$menu->addSubmenu($submenu);
			}
		}

		// delete
		if (Auth::isEditor($this->record->getTree())) {
			$submenu = new Menu(I18N::translate('Delete'), '#', 'menu-fam-del');
			$submenu->setOnclick("return delete_family('" . I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place.  Are you sure you want to delete this family?') . "', '" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$submenu = new Menu(I18N::translate('Edit raw GEDCOM'), '#', 'menu-fam-editraw');
			$submenu->setOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$submenu = new Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-fam-addfav'
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
		global $linkToID;

		$linkToID = $this->record->getXref(); // -- Tell addmedia.php what to link to

		$indifacts = $this->record->getFacts();
		if ($indifacts) {
			sort_facts($indifacts);
			foreach ($indifacts as $fact) {
				print_fact($fact, $this->record);
			}
		} else {
			echo '<tr><td class="messagebox" colspan="2">', I18N::translate('No facts for this family.'), '</td></tr>';
		}

		if (Auth::isEditor($this->record->getTree())) {
			print_add_new_fact($this->record->getXref(), $indifacts, 'FAM');

			echo '<tr><td class="descriptionbox">';
			echo I18N::translate('Note');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('" . $this->record->getXref() . "','NOTE');\">", I18N::translate('Add a new note'), '</a>';
			echo '</td></tr>';

			echo '<tr><td class="descriptionbox">';
			echo I18N::translate('Shared note');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('" . $this->record->getXref() . "','SHARED_NOTE');\">", I18N::translate('Add a new shared note'), '</a>';
			echo '</td></tr>';

			if ($this->record->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($this->record->getTree())) {
				echo '<tr><td class="descriptionbox">';
				echo I18N::translate('Media object');
				echo '</td><td class="optionbox">';
				echo "<a href=\"#\" onclick=\"window.open('addmedia.php?action=showmediaform&amp;linktoid=" . $this->record->getXref() . "', '_blank', edit_window_specs); return false;\">", I18N::translate('Add a new media object'), '</a>';
				echo help_link('OBJE');
				echo '<br>';
				echo "<a href=\"#\" onclick=\"window.open('inverselink.php?linktoid=" . $this->record->getXref() . "&amp;linkto=family', '_blank', find_window_specs); return false;\">", I18N::translate('Link to an existing media object'), '</a>';
				echo '</td></tr>';
			}

			echo '<tr><td class="descriptionbox">';
			echo I18N::translate('Source');
			echo '</td><td class="optionbox">';
			echo "<a href=\"#\" onclick=\"return add_new_record('" . $this->record->getXref() . "','SOUR');\">", I18N::translate('Add a new source citation'), '</a>';
			echo '</td></tr>';
		}
	}
}
