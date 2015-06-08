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
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Tree;

/**
 * Upgrade the database schema from version 5 to version 5.
 */
class Migration5 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
// - changes to the values for the gedcom setting SHOW_RELATIVES_EVENTS

		$settings = Database::prepare(
			"SELECT gedcom_id, setting_value FROM `##gedcom_setting` WHERE setting_name='SHOW_RELATIVES_EVENTS'"
		)->fetchAssoc();

		foreach ($settings as $gedcom_id => $setting) {
			// Delete old settings
			$setting = preg_replace('/_(BIRT|MARR|DEAT)_(COUS|MSIB|FSIB|GGCH|NEPH|GGPA)/', '', $setting);
			$setting = preg_replace('/_FAMC_(RESI_EMIG)/', '', $setting);
			// Rename settings
			$setting = preg_replace('/_MARR_(MOTH|FATH|FAMC)/', '_MARR_PARE', $setting);
			$setting = preg_replace('/_DEAT_(MOTH|FATH)/', '_DEAT_PARE', $setting);
			// Remove duplicates
			preg_match_all('/[_A-Z]+/', $setting, $match);
			// And save
			Tree::findById($gedcom_id)->setPreference('SHOW_RELATIVES_EVENTS', implode(',', array_unique($match[0])));
		}
	}
}
