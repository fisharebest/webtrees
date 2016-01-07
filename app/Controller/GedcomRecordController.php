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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;

/**
 * Base controller for all GedcomRecord controllers
 */
class GedcomRecordController extends PageController {
	/**
	 * A genealogy record
	 *
	 * @var GedcomRecord|Individual|Family|Source|Repository|Media|Note
	 */
	public $record;

	/**
	 * Startup activity
	 *
	 * @param GedcomRecord|null $record
	 */
	public function __construct(GedcomRecord $record = null) {
		$this->record = $record;

		// Automatically fix broken links
		if ($this->record && $this->record->canEdit()) {
			$broken_links = 0;
			foreach ($this->record->getFacts('HUSB|WIFE|CHIL|FAMS|FAMC|REPO') as $fact) {
				if (!$fact->isPendingDeletion() && $fact->getTarget() === null) {
					$this->record->deleteFact($fact->getFactId(), false);
					FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $this->record->getFullName(), $fact->getValue()));
					$broken_links = true;
				}
			}
			foreach ($this->record->getFacts('NOTE|SOUR|OBJE') as $fact) {
				// These can be links or inline.  Only delete links.
				if (!$fact->isPendingDeletion() && $fact->getTarget() === null && preg_match('/^@.*@$/', $fact->getValue())) {
					$this->record->deleteFact($fact->getFactId(), false);
					FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $this->record->getFullName(), $fact->getValue()));
					$broken_links = true;
				}
			}
			if ($broken_links) {
				// Reload the updated family
				$this->record = GedcomRecord::getInstance($this->record->getXref(), $this->record->getTree());
			}
		}

		parent::__construct();

		// We want robots to index this page
		$this->setMetaRobots('index,follow');

		// Set a page title
		if ($this->record) {
			if ($this->record->canShowName()) {
				// e.g. "John Doe" or "1881 Census of Wales"
				$this->setPageTitle($this->record->getFullName());
			} else {
				// e.g. "Individual" or "Source"
				$record = $this->record;
				$this->setPageTitle(GedcomTag::getLabel($record::RECORD_TYPE));
			}
		} else {
			// No such record
			$this->setPageTitle(I18N::translate('Private'));
		}
	}

	/**
	 * get edit menu
	 */
	public function getEditMenu() {
		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}

		// edit menu
		$menu = new Menu(I18N::translate('Edit'), '#', 'menu-record');

		// edit raw
		if (Auth::isAdmin() || Auth::isEditor($this->record->getTree()) && $this->record->getTree()->getPreference('SHOW_GEDCOM_RECORD')) {
			$menu->addSubmenu(new Menu(I18N::translate('Edit raw GEDCOM'), '#', 'menu-record-editraw', array(
				'onclick' => 'return edit_raw("' . $this->record->getXref() . '");',
			)));
		}

		// delete
		if (Auth::isEditor($this->record->getTree())) {
			$menu->addSubmenu(new Menu(I18N::translate('Delete'), '#', 'menu-record-del', array(
				'onclick' => 'return record("' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeJS(Filter::unescapeHtml($this->record->getFullName()))) . '", "' . $this->record->getXref() . '");',
			)));
		}

		// add to favorites
		if (Module::getModuleByName('user_favorites')) {
			$menu->addSubmenu(new Menu(
			/* I18N: Menu option.  Add [the current page] to the list of favorites */ I18N::translate('Add to favorites'),
				'#',
				'menu-record-addfav',
				array(
					'onclick' => 'jQuery.post("module.php?mod=user_favorites&mod_action=menu-add-favorite" ,{xref:"' . $this->record->getXref() . '"},function(){location.reload();})',
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
