<?php
/**
 * Perform an incremental import of a gedcom file.
 *
 * For each gedcom that needs importing, editgedcoms.php will create
 * a <div id="importNNN"></div>, where NNN is the gedcom ID.
 * It will then call import.php to load the div's contents using AJAX.
 *
 * We start importing at position wt_gedcom.import_offset and continue
 * for a couple of seconds.  When we've finished we set import_offset to
 * zero to indicate success.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'import.php');
require './includes/session.php';

require_once WT_ROOT.'includes/functions/functions_import.php';

// Don't use ged=XX as we want to be able to run without changing the current gedcom.
// This will let us load several gedcoms together, or to edit one while loading another.
$gedcom_id=safe_GET('gedcom_id');

if (!userGedcomAdmin(WT_USER_ID, $gedcom_id)) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Don't allow the user to cancel the request.  We do not want
// to be left with an incomplete transaction, as this could cause a
// timeout error in another session.
ignore_user_abort(true);

// Run in a transaction, and make sure we are the only ones importing this gedcom
WT_DB::exec("START TRANSACTION");

// What is the current import status?
$row=WT_DB::prepare(
	"SELECT import_offset, LENGTH(import_gedcom) AS import_total FROM {$TBLPREFIX}gedcom WHERE gedcom_id=? FOR UPDATE"
)->execute(array($gedcom_id))->fetchOneRow();

if (!$row) {
	// No such gedcom?  Deleted in another session?  die quietly
	WT_DB::exec("COMMIT");
	exit;
}

header('Content-type: text/html; charset=UTF-8');

if ($row->import_offset==0 || $row->import_total==0) {
	// Finished?  Show the maintenance links, similar to editgedcoms.php
	WT_DB::exec("COMMIT");
	echo "DONE";
	exit;	
}

// Calculate progress so far
$percent=100*(($row->import_offset-1) / $row->import_total);
$status=i18n::translate('Loading data from GEDCOM: %.1f%%', $percent);

echo
	'<div id="progressbar', $gedcom_id, '"><div style="position:absolute;">', htmlspecialchars($status), '</div></div>',
	WT_JS_START,
	'$("#progressbar', $gedcom_id, '").progressbar({value: ', round($percent, 1), '});',
	WT_JS_END,
flush();

// Run for one second.  This keeps the resource requirements low.
for ($end_time=microtime(true)+1.0; microtime(true)<$end_time; ) {
	// If we are at the start position, do some tidying up
	if ($row->import_offset==1) {
		$keep_media=safe_GET_bool('keep_media');
		// Delete any existing genealogical data
		empty_database($gedcom_id, $keep_media);
		set_gedcom_setting($gedcom_id, 'imported', false);
		// Remove any byte-order-mark
		WT_DB::prepare(
			"UPDATE {$TBLPREFIX}gedcom".
			" SET import_gedcom=TRIM(LEADING ? FROM import_gedcom)".
			" WHERE gedcom_id=?"
		)->execute(array(WT_UTF8_BOM, $gedcom_id));
		// Convert line endings.  Don't convert \r\n - it is very slow.  Just deal
		// with empty records later.
		WT_DB::prepare(
			"UPDATE {$TBLPREFIX}gedcom".
			" SET import_gedcom=REPLACE(import_gedcom, '\r', '\n')".
			" WHERE gedcom_id=?"
		)->execute(array($gedcom_id));
		// Fetch the header record
		$data=WT_DB::prepare(
			"SELECT LEFT(import_gedcom, CASE LOCATE('\n0', import_gedcom, 2) WHEN 0 THEN LENGTH(import_gedcom) ELSE LOCATE('\n0', import_gedcom, 2) END)".
			" FROM {$TBLPREFIX}gedcom".
			" WHERE gedcom_id=?"
		)->execute(array($gedcom_id))->fetchOne();
		WT_DB::prepare(
			"UPDATE {$TBLPREFIX}gedcom".
			" SET import_offset=?".
			" WHERE gedcom_id=?"
		)->execute(array(strlen($data)+1, $gedcom_id));
		if (substr($data, 0, 6)!='0 HEAD') {
			WT_DB::exec("ROLLBACK");
			echo i18n::translate('Invalid GEDCOM file - no header record found.');
			exit;
		}
		// What character set is this?  Need to convert it to UTF8
		if (preg_match('/\n1\s*CHAR(?:ACTER)?\s+(.+)/', $data, $match)) {
			$charset=strtoupper($match[1]);
		} else {
			$charset='ASCII';
		}
		// MySQL supports a wide range of collation conversions.  These are ones that
		// have been encountered "in the wild".
		switch ($charset) {
		case 'ANSI':
		case 'ASCII':
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}gedcom".
				" SET import_gedcom=CONVERT(CONVERT(import_gedcom USING ascii) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;				
		case 'IBMPC': // IBMPC could be anything.  Mostly it means CP850.
		case 'CP437':
		case 'CP850':
			// CP850 has extra letters with diacritics to replace box-drawing chars in CP437.
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}gedcom".
				" SET import_gedcom=CONVERT(CONVERT(import_gedcom USING cp850) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;				
		case 'MACINTOSH':
			// Convert from MAC Roman to UTF8.
			WT_DB::prepare(
				"UPDATE {$TBLPREFIX}gedcom".
				" SET import_gedcom=CONVERT(CONVERT(import_gedcom USING macroman) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;				
		case 'UTF8':
		case 'UTF-8':
			// Already UTF-8 so nothing to do!
			break;
		case 'ANSEL':
			// TODO: fisharebest has written a mysql stored procedure that converts ANSEL to UTF-8
		default:
			echo
				WT_JS_START,
				'alert(\'', htmlspecialchars(i18n::translate('Error: cannot convert GEDCOM file from %s encoding to UTF-8 encoding.', $charset)), '\');',
				WT_JS_END;
			break;
		}
		$data=preg_replace('/\n1 CHAR.*(\n[2-9].+)*/', '', $data)."\n1 CHAR UTF-8";
		import_record(trim($data), $gedcom_id, false);
	} else {
		// Fetch the next block of data. At least 64KB, and ending on a record boundary.
		$data=WT_DB::prepare(
			"SELECT".
			"  CASE LOCATE('\n0', import_gedcom, import_offset+65536)".
			"   WHEN 0 THEN SUBSTR(import_gedcom FROM import_offset)".
			"   ELSE SUBSTR(import_gedcom FROM import_offset FOR LOCATE('\n0', import_gedcom, import_offset+65536)-import_offset)".
			"  END".
			" FROM {$TBLPREFIX}gedcom".
			" WHERE gedcom_id=?"
		)->execute(array($gedcom_id))->fetchOne();
		WT_DB::prepare(
			"UPDATE {$TBLPREFIX}gedcom".
			" SET import_offset=import_offset+?".
			" WHERE gedcom_id=?"
		)->execute(array(strlen($data), $gedcom_id));
		echo WT_JS_START, WT_JS_END;
		foreach (preg_split('/\n(?=0)/', $data) as $rec) {
			if ($rec) {
				import_record(trim($rec), $gedcom_id, false);
			}
		}
	}
}

if ($row->import_offset>$row->import_total) {
	// Done
	set_gedcom_setting($gedcom_id, 'imported', true);
	WT_DB::prepare(
		"UPDATE {$TBLPREFIX}gedcom".
		" SET import_offset=0".
		" WHERE gedcom_id=?"
	)->execute(array($gedcom_id));
	echo
		WT_JS_START,
		'$("#import',  $gedcom_id, '").toggle();',
		'$("#actions', $gedcom_id, '").toggle();',
		WT_JS_END;
} else {
	// Reload.....
	echo
		WT_JS_START,
		'$("#import', $gedcom_id, '").load("import.php?gedcom_id=', $gedcom_id, '");',
		WT_JS_END;
}

WT_DB::exec("COMMIT");
