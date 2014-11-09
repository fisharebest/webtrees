<?php
// Perform an incremental import of a gedcom file.
//
// For each gedcom that needs importing, admin_trees_manage.php will create
// a <div id="importNNN"></div>, where NNN is the gedcom ID.
// It will then call import.php to load the div elements contents using AJAX.
//
// We import small blocks of data from wt_gedcom_chunks, working for
// a couple of seconds.  When each block is loaded, we set its status
// flag.
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

define('WT_SCRIPT_NAME', 'import.php');
require './includes/session.php';

if (!WT_USER_GEDCOM_ADMIN) {
	header('HTTP/1.1 403 Access Denied');
	exit;
}

$controller = new WT_Controller_Ajax();
$controller
	->pageHeader();

// Don't use ged=XX as we want to be able to run without changing the current gedcom.
// This will let us load several gedcoms together, or to edit one while loading another.
$gedcom_id = WT_Filter::getInteger('gedcom_id');

// Don't allow the user to cancel the request.  We do not want to be left
// with an incomplete transaction.
ignore_user_abort(true);

// Run in a transaction
WT_DB::exec("START TRANSACTION");

// Only allow one process to import each gedcom at a time
WT_DB::prepare("SELECT * FROM `##gedcom_chunk` WHERE gedcom_id=? FOR UPDATE")->execute(array($gedcom_id));

// What is the current import status?
$row=WT_DB::prepare(
	"SELECT".
	" SUM(IF(imported, LENGTH(chunk_data), 0)) AS import_offset,".
	" SUM(LENGTH(chunk_data))                  AS import_total".
	" FROM `##gedcom_chunk` WHERE gedcom_id=?"
)->execute(array($gedcom_id))->fetchOneRow();

if ($row->import_offset==$row->import_total) {
	WT_Tree::get($gedcom_id)->setPreference('imported', '1');
	// Finished?  Show the maintenance links, similar to admin_trees_manage.php
	WT_DB::exec("COMMIT");
	$controller->addInlineJavascript(
		'jQuery("#import'. $gedcom_id.'").toggle();'.
		'jQuery("#actions'.$gedcom_id.'").toggle();'
	);
	exit;
}

// Calculate progress so far
$percent=100*(($row->import_offset) / $row->import_total);
$status=WT_I18N::translate('Loading data from GEDCOM file: %.1f%%', $percent);

echo '<div id="progressbar', $gedcom_id, '"><div style="position:absolute;">', $status, '</div></div>';
$controller->addInlineJavascript(
	'jQuery("#progressbar' . $gedcom_id . '").progressbar({value: ' . round($percent, 1) . '});'
);

$first_time = ($row->import_offset == 0);
// Run for one second.  This keeps the resource requirements low.
for ($end_time = microtime(true) + 1.0; microtime(true) < $end_time;) {
	$data = WT_DB::prepare(
		"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data".
		" FROM `##gedcom_chunk`".
		" WHERE gedcom_id=? AND NOT imported".
		" ORDER BY gedcom_chunk_id".
		" LIMIT 1"
	)->execute(array($gedcom_id))->fetchOneRow();
	// If we are at the start position, do some tidying up
	if ($first_time) {
		$keep_media = WT_Filter::getBool('keep_media'.$gedcom_id);
		WT_Tree::get($gedcom_id)->deleteGenealogyData($keep_media);
		WT_Tree::get($gedcom_id)->setPreference('imported', '0');
		// Remove any byte-order-mark
		WT_DB::prepare(
			"UPDATE `##gedcom_chunk`".
			" SET chunk_data=TRIM(LEADING ? FROM chunk_data)".
			" WHERE gedcom_chunk_id=?"
		)->execute(array(WT_UTF8_BOM, $data->gedcom_chunk_id));
		// Re-fetch the data, now that we have removed the BOM
		$data = WT_DB::prepare(
			"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data".
			" FROM `##gedcom_chunk`".
			" WHERE gedcom_chunk_id=?"
		)->execute(array($data->gedcom_chunk_id))->fetchOneRow();
		if (substr($data->chunk_data, 0, 6)!='0 HEAD') {
			WT_DB::exec("ROLLBACK");
			echo WT_I18N::translate('Invalid GEDCOM file - no header record found.');
			$controller->addInlineJavascript('jQuery("#actions'.$gedcom_id.'").toggle();');
			exit;
		}
		// What character set is this?  Need to convert it to UTF8
		if (preg_match('/\n[ \t]*1 CHAR(?:ACTER)? (.+)/', $data->chunk_data, $match)) {
			$charset = strtoupper($match[1]);
		} else {
			$charset = 'ASCII';
		}
		// MySQL supports a wide range of collation conversions.  These are ones that
		// have been encountered "in the wild".
		switch ($charset) {
		case 'ASCII':
			WT_DB::prepare(
				"UPDATE `##gedcom_chunk`".
				" SET chunk_data=CONVERT(CONVERT(chunk_data USING ascii) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;
		case 'IBMPC':   // IBMPC, IBM WINDOWS and MS-DOS could be anything.  Mostly it means CP850.
		case 'IBM WINDOWS':
		case 'MS-DOS':
		case 'CP437':
		case 'CP850':
			// CP850 has extra letters with diacritics to replace box-drawing chars in CP437.
			WT_DB::prepare(
				"UPDATE `##gedcom_chunk`".
				" SET chunk_data=CONVERT(CONVERT(chunk_data USING cp850) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;
		case 'ANSI': // ANSI could be anything.  Most applications seem to treat it as latin1.
			$controller->addInlineJavascript(
				'alert("'. /* I18N: %1$s and %2$s are the names of character encodings, such as ISO-8859-1 or ASCII */ WT_I18N::translate('This GEDCOM file is encoded using %1$s.  Assume this to mean %2$s.', $charset, 'ISO-8859-1'). '");'
			);
		case 'WINDOWS':
		case 'CP1252':
		case 'ISO8859-1':
		case 'ISO-8859-1':
		case 'LATIN1':
		case 'LATIN-1':
			// Convert from ISO-8859-1 (western european) to UTF8.
			WT_DB::prepare(
				"UPDATE `##gedcom_chunk`".
				" SET chunk_data=CONVERT(CONVERT(chunk_data USING latin1) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;
		case 'CP1250':
		case 'ISO8859-2':
		case 'ISO-8859-2':
		case 'LATIN2':
		case 'LATIN-2':
			// Convert from ISO-8859-2 (eastern european) to UTF8.
			WT_DB::prepare(
				"UPDATE `##gedcom_chunk`".
				" SET chunk_data=CONVERT(CONVERT(chunk_data USING latin2) USING utf8)".
				" WHERE gedcom_id=?"
			)->execute(array($gedcom_id));
			break;
		case 'MACINTOSH':
			// Convert from MAC Roman to UTF8.
			WT_DB::prepare(
				"UPDATE `##gedcom_chunk`".
				" SET chunk_data=CONVERT(CONVERT(chunk_data USING macroman) USING utf8)".
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
			WT_DB::exec("ROLLBACK");
			echo '<span class="error">',  WT_I18N::translate('Error: converting GEDCOM files from %s encoding to UTF-8 encoding not currently supported.', $charset), '</span>';
			$controller->addInlineJavascript('jQuery("#actions'.$gedcom_id.'").toggle();');
			exit;
		}
		$first_time=false;

		// Re-fetch the data, now that we have performed character set conversion.
		$data=WT_DB::prepare(
			"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data".
			" FROM `##gedcom_chunk`".
			" WHERE gedcom_chunk_id=?"
		)->execute(array($data->gedcom_chunk_id))->fetchOneRow();
	}

	if (!$data) {
		break;
	}
	try {
		// Import all the records in this chunk of data
		foreach (preg_split('/\n+(?=0)/', $data->chunk_data) as $rec) {
			import_record($rec, $gedcom_id, false);
		}
		// Mark the chunk as imported
		WT_DB::prepare(
			"UPDATE `##gedcom_chunk` SET imported=TRUE WHERE gedcom_chunk_id=?"
		)->execute(array($data->gedcom_chunk_id));
	} catch (PDOException $ex) {
		WT_DB::exec("ROLLBACK");
		if ($ex->getCode() === '40001') {
			// "SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction"
			// The documentation says that if you get this error, wait and try again.....
			$controller->addInlineJavascript('jQuery("#import' . $gedcom_id . '").load("import.php?gedcom_id=' . $gedcom_id . '&u=' . uniqid() . '");');
		} else {
			// A fatal error.  Nothing we can do?
			echo '<span class="error">', $ex->getMessage(), '</span>';
			$controller->addInlineJavascript('jQuery("#actions' . $gedcom_id . '").toggle();');
		}
		exit;
	}
}

WT_DB::exec("COMMIT");

// Reload.....
// Use uniqid() to prevent jQuery caching the previous response.
$controller->addInlineJavascript('jQuery("#import' . $gedcom_id . '").load("import.php?gedcom_id=' . $gedcom_id . '&u=' . uniqid() . '");');
