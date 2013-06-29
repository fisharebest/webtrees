<?php
// Base controller for all GedcomRecord controllers
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_GedcomRecord extends WT_Controller_Page {
	public $record; // individual, source, repository, etc.

	public function __construct() {
		// Automatically fix broken links
		if ($this->record && $this->record->canEdit()) {
			$broken_links=0;
			foreach ($this->record->getFacts('HUSB|WIFE|CHIL|FAMS|FAMC|SOUR|REPO|OBJE') as $fact) { // Not NOTE!
				if (!$fact->isOld() && $fact->getTarget() === null) {
					$this->record->updateFact($fact->getFactId(), null, false);
					WT_FlashMessages::addMessage(/* I18N: %s are names of records, such as sources, repositories or individuals */ WT_I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $this->record->getFullName(), $fact->getValue()));
					$broken_links = true;
				}
			}
			if ($broken_links) {
				// Reload the updated family
				$this->record = WT_GedcomRecord::getInstance($this->record->getXref());
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
				$this->setPageTitle(WT_Gedcom_Tag::getLabel($record::RECORD_TYPE));
			}
		} else {
			// No such record
			$this->setPageTitle(WT_I18N::translate('Private'));
		}
	}
}
