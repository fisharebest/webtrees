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
 * Class BatchUpdateNameFormatPlugin Batch Update plugin: fix spacing in names, particularly that before/after the surname slashes
 */
class BatchUpdateNameFormatPlugin extends BatchUpdateBasePlugin {
	/**
	 * User-friendly name for this plugin.
	 *
	 * @return string
	 */
	public function getName() {
		return I18N::translate('Fix name slashes and spaces');
	}

	/**
	 * Description / help-text for this plugin.
	 *
	 * @return string
	 */
	public function getDescription() {
		return I18N::translate('Correct NAME records of the form “John/DOE/” or “John /DOE”, as produced by older genealogy programs.');
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
			preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*$/m', $gedrec) ||
			preg_match('/^(?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ]\//m', $gedrec);
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
				'/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*\/[^\/\n]*)$/m',
				'/^((?:1 NAME|2 (?:FONE|ROMN|_MARNM|_AKA|_HEB)) [^\/\n]*[^\/ ])(\/)/m',
			),
			array(
				'$1/',
				'$1 $2',
			),
			$gedrec
		);
	}
}
