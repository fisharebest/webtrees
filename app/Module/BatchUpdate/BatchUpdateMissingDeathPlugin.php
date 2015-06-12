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
namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class BatchUpdateMissingDeathPlugin Batch Update plugin: add missing 1 BIRT/DEAT Y
 */
class BatchUpdateMissingDeathPlugin extends BatchUpdateBasePlugin {
	/**
	 * User-friendly name for this plugin.
	 *
	 * @return string
	 */
	public function getName() {
		return I18N::translate('Add missing death records');
	}

	/**
	 * Description / help-text for this plugin.
	 *
	 * @return string
	 */
	public function getDescription() {
		return I18N::translate('You can speed up the privacy calculations by adding a death record to individuals whose death can be inferred from other dates, but who do not have a record of death, burial, cremation, etc.');
	}

	/**
	 * Does this record need updating?
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return bool
	 */
	public function doesRecordNeedUpdate($xref, $gedrec) {
		global $WT_TREE;

		return !preg_match('/\n1 (' . WT_EVENTS_DEAT . ')/', $gedrec) && Individual::getInstance($xref, $WT_TREE)->isDead();
	}

	/**
	 * Apply any updates to this record
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return string
	 */
	public function updateRecord($xref, $gedrec) {
		return $gedrec . "\n1 DEAT Y";
	}
}
