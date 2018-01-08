<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Controller for the family page
 */
class FamilyController extends GedcomRecordController {
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
			$menu->addSubmenu(new Menu(I18N::translate('Change family members'), 'edit_interface.php?action=changefamily&ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref(), 'menu-fam-change'));

			// edit_fam / add child
			$menu->addSubmenu(new Menu(I18N::translate('Add a child to this family'), 'edit_interface.php?action=add_child_to_family&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref() . '&amp;gender=U', 'menu-fam-addchil'));

			// edit_fam / reorder-children
			if ($this->record->getNumberOfChildren() > 1) {
				$menu->addSubmenu(new Menu(I18N::translate('Re-order children'), 'edit_interface.php?action=reorder-children&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref(), 'menu-fam-orderchil'));
			}

			// delete
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-fam-del', [
				'onclick' => 'return delete_record("' . I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place. Are you sure you want to delete this family?') . '", "' . $this->record->getXref() . '");',
			]));
		}

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit the raw GEDCOM'), 'edit_interface.php?action=editraw&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref(), 'menu-fam-editraw'));
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
			list($surn) = explode(',', $this->record->getHusband()->getSortName());

			return $surn;
		} else {
			return '';
		}
	}

	/**
	 * Print the facts
	 */
	public function familyFacts() {
		$html = '';

		$indifacts = $this->record->getFacts();
		if ($indifacts) {
			Functions::sortFacts($indifacts);
			foreach ($indifacts as $fact) {
				ob_start();
				FunctionsPrintFacts::printFact($fact, $this->record);
				$html .= ob_get_clean();
			}
		} else {
			$html .= '<tr><td class="messagebox" colspan="2">' . I18N::translate('No facts exist for this family.') . '</td></tr>';
		}

		if (Auth::isEditor($this->record->getTree())) {
			ob_start();
			FunctionsPrint::printAddNewFact($this->record->getXref(), $indifacts, 'FAM');
			$html .= ob_get_clean();

			$html .= '<tr><th scope="row">';
			$html .= I18N::translate('Note');
			$html .= '</th><td>';
			$html .= '<a href="edit_interface.php?action=add&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref() . '&amp;fact=NOTE">' . I18N::translate('Add a note') . '</a>';
			$html .= '</td></tr>';

			$html .= '<tr><th scope="row">';
			$html .= I18N::translate('Shared note');
			$html .= '</th><td class="optionbox">';
			$html .= '<a href="edit_interface.php?action=add&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref() . '&amp;fact=SHARED_NOTE">' . I18N::translate('Add a shared note') . '</a>';
			$html .= '</td></tr>';

			if ($this->record->getTree()->getPreference('MEDIA_UPLOAD') >= Auth::accessLevel($this->record->getTree())) {
				$html .= '<tr><th scope="row">';
				$html .= I18N::translate('Media object');
				$html .= '</th><td class="optionbox">';
				$html .=  '<a href="edit_interface.php?action=add-media-link&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref() . '">' . I18N::translate('Add a media object') . '</a>';
				$html .= '</td></tr>';
			}

			$html .= '<tr><th scope="row">';
			$html .= I18N::translate('Source');
			$html .= '</th><td>';
			$html .= '<a href="edit_interface.php?action=add&amp;ged=' . $this->record->getTree()->getNameHtml() . '&amp;xref=' . $this->record->getXref() . '&amp;fact=SOUR">' . I18N::translate('Add a source citation') . '</a>';
			$html .= '</td></tr>';
		}

		return $html;
	}
}
