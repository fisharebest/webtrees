<?php
// Update the database schema from version 26-27
// - delete unused settings and update indexes
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

// Earlier versions of webtrees put quote marks round soundex codes.
// These are harmless, but clean them up for consistency.
WT_DB::exec(
	"UPDATE `##name` SET" .
	" n_soundex_givn_std = TRIM('''' FROM n_soundex_givn_std)," .
	" n_soundex_surn_std = TRIM('''' FROM n_soundex_surn_std)," .
	" n_soundex_givn_dm  = TRIM('''' FROM n_soundex_givn_dm )," .
	" n_soundex_surn_dm  = TRIM('''' FROM n_soundex_surn_dm )"
);

// Earlier versions of webtrees added zero codes for names without phonetic content.
// These are harmless, but clean them up for consistency.
WT_DB::exec(
	"UPDATE `##name` SET" .
	" n_soundex_givn_std = REPLACE(n_soundex_givn_std, '0000:',   '')," .
	" n_soundex_surn_std = REPLACE(n_soundex_surn_std, '0000:',   '')," .
	" n_soundex_givn_dm  = REPLACE(n_soundex_givn_dm,  '000000:', '')," .
	" n_soundex_surn_dm  = REPLACE(n_soundex_surn_dm,  '000000:', '')"
);
WT_DB::exec(
	"UPDATE `##name` SET" .
	" n_soundex_givn_std = REPLACE(n_soundex_givn_std, ':0000',   '')," .
	" n_soundex_surn_std = REPLACE(n_soundex_surn_std, ':0000',   '')," .
	" n_soundex_givn_dm  = REPLACE(n_soundex_givn_dm,  ':000000', '')," .
	" n_soundex_surn_dm  = REPLACE(n_soundex_surn_dm,  ':000000', '')"
);
WT_DB::exec(
	"UPDATE `##name` SET" .
	" n_soundex_givn_std = NULLIF(n_soundex_givn_std, '0000'  )," .
	" n_soundex_surn_std = NULLIF(n_soundex_surn_std, '0000'  )," .
	" n_soundex_givn_dm  = NULLIF(n_soundex_givn_dm,  '000000')," .
	" n_soundex_surn_dm  = NULLIF(n_soundex_surn_dm,  '000000')"
);

WT_DB::exec(
	"UPDATE `##places` SET" .
	" p_std_soundex = REPLACE(p_std_soundex, '0000:',   '')," .
	" p_dm_soundex  = REPLACE(p_dm_soundex,  '000000:', '')"
);
WT_DB::exec(
	"UPDATE `##places` SET" .
	" p_std_soundex = REPLACE(p_std_soundex, ':0000',   '')," .
	" p_dm_soundex  = REPLACE(p_dm_soundex,  ':000000', '')"
);
WT_DB::exec(
	"UPDATE `##places` SET" .
	" p_std_soundex = NULLIF(p_std_soundex, '0000'  )," .
	" p_dm_soundex  = NULLIF(p_dm_soundex,  '000000')"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
