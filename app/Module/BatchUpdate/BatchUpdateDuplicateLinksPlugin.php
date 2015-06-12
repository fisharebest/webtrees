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

/**
 * Class BatchUpdateDuplicateLinksPlugin Batch Update plugin: remove duplicate links in records
 */
class BatchUpdateDuplicateLinksPlugin extends BatchUpdateBasePlugin {
	/**
	 * User-friendly name for this plugin.
	 *
	 * @return string
	 */
	public function getName() {
		return I18N::translate('Remove duplicate links');
	}

	/**
	 * Description / help-text for this plugin.
	 *
	 * @return string
	 */
	public function getDescription() {
		return I18N::translate('A common error is to have multiple links to the same record, for example listing the same child more than once in a family record.');
	}

	/**
	 * This plugin will update all types of record.
	 *
	 * @return string[]
	 */
	public function getRecordTypesToUpdate() {
		return array('INDI', 'FAM', 'SOUR', 'REPO', 'NOTE', 'OBJE');
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
		return
			preg_match('/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))(?:\n1.*(?:\n[2-9].*)*)*\1/', $gedrec) ||
			preg_match('/(\n2.*@.+@.*(?:(?:\n[3-9].*)*))(?:\n2.*(?:\n[3-9].*)*)*\1/', $gedrec) ||
			preg_match('/(\n3.*@.+@.*(?:(?:\n[4-9].*)*))(?:\n3.*(?:\n[4-9].*)*)*\1/', $gedrec);
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
		return preg_replace(
			array(
				'/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
				'/(\n2.*@.+@.*(?:(?:\n[3-9].*)*))((?:\n2.*(?:\n[3-9].*)*)*\1)/',
				'/(\n3.*@.+@.*(?:(?:\n[4-9].*)*))((?:\n3.*(?:\n[4-9].*)*)*\1)/',
			),
			'$2',
			$gedrec
		);
	}
}
