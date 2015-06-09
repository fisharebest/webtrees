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

/**
 * Upgrade the database schema from version 26 to version 27.
 */
class Migration26 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// Earlier versions of webtrees put quote marks round soundex codes.
		// These are harmless, but clean them up for consistency.
		Database::exec(
			"UPDATE `##name` SET" .
			" n_soundex_givn_std = TRIM('''' FROM n_soundex_givn_std)," .
			" n_soundex_surn_std = TRIM('''' FROM n_soundex_surn_std)," .
			" n_soundex_givn_dm  = TRIM('''' FROM n_soundex_givn_dm )," .
			" n_soundex_surn_dm  = TRIM('''' FROM n_soundex_surn_dm )"
		);

		// Earlier versions of webtrees added zero codes for names without phonetic content.
		// These are harmless, but clean them up for consistency.
		Database::exec(
			"UPDATE `##name` SET" .
			" n_soundex_givn_std = REPLACE(n_soundex_givn_std, '0000:',   '')," .
			" n_soundex_surn_std = REPLACE(n_soundex_surn_std, '0000:',   '')," .
			" n_soundex_givn_dm  = REPLACE(n_soundex_givn_dm,  '000000:', '')," .
			" n_soundex_surn_dm  = REPLACE(n_soundex_surn_dm,  '000000:', '')"
		);
		Database::exec(
			"UPDATE `##name` SET" .
			" n_soundex_givn_std = REPLACE(n_soundex_givn_std, ':0000',   '')," .
			" n_soundex_surn_std = REPLACE(n_soundex_surn_std, ':0000',   '')," .
			" n_soundex_givn_dm  = REPLACE(n_soundex_givn_dm,  ':000000', '')," .
			" n_soundex_surn_dm  = REPLACE(n_soundex_surn_dm,  ':000000', '')"
		);
		Database::exec(
			"UPDATE `##name` SET" .
			" n_soundex_givn_std = NULLIF(n_soundex_givn_std, '0000'  )," .
			" n_soundex_surn_std = NULLIF(n_soundex_surn_std, '0000'  )," .
			" n_soundex_givn_dm  = NULLIF(n_soundex_givn_dm,  '000000')," .
			" n_soundex_surn_dm  = NULLIF(n_soundex_surn_dm,  '000000')"
		);

		Database::exec(
			"UPDATE `##places` SET" .
			" p_std_soundex = REPLACE(p_std_soundex, '0000:',   '')," .
			" p_dm_soundex  = REPLACE(p_dm_soundex,  '000000:', '')"
		);
		Database::exec(
			"UPDATE `##places` SET" .
			" p_std_soundex = REPLACE(p_std_soundex, ':0000',   '')," .
			" p_dm_soundex  = REPLACE(p_dm_soundex,  ':000000', '')"
		);
		Database::exec(
			"UPDATE `##places` SET" .
			" p_std_soundex = NULLIF(p_std_soundex, '0000'  )," .
			" p_dm_soundex  = NULLIF(p_dm_soundex,  '000000')"
		);
	}
}
