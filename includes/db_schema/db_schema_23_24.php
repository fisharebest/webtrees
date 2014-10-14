<?php
// Update the database schema from version 23-24
// - media table columns should be not null, so we can find
// media objects with missing files
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

WT_DB::exec(
	"ALTER IGNORE TABLE `##media`" .
	" CHANGE m_ext      m_ext      VARCHAR(6)   COLLATE utf8_unicode_ci NOT NULL," .
	" CHANGE m_type     m_type     VARCHAR(20)  COLLATE utf8_unicode_ci NOT NULL," .
	" CHANGE m_filename m_filename VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL," .
	" CHANGE m_titl     m_titl     VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL," .
	" CHANGE m_gedcom   m_gedcom   MEDIUMTEXT   COLLATE utf8_unicode_ci NOT NULL"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
