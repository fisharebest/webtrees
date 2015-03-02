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
 * Class GedcomRecordController - Base controller for all GedcomRecord controllers
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
	 */
	public function __construct() {
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
				$this->record = GedcomRecord::getInstance($this->record->getXref());
			}
		}

		parent::__construct();

		// We want robots to index this page
		$this->setMetaRobots('index,follow');

		// Set a page title
		if ($this->record) {
			$this->setCanonicalUrl($this->record->getHtmlUrl());
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
}
