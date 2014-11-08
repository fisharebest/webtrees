<?php
// Functions for exporting data
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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
use WT\Auth;

/**
 * Tidy up a gedcom record on export, for compatibility/portability.
 *
 * @param string $rec
 *
 * @return string
 */
function reformat_record_export($rec) {
	global $WORD_WRAPPED_NOTES;

	$newrec='';
	foreach (preg_split('/[\r\n]+/', $rec, -1, PREG_SPLIT_NO_EMPTY) as $line) {
		// Split long lines
		// The total length of a GEDCOM line, including level number, cross-reference number,
		// tag, value, delimiters, and terminator, must not exceed 255 (wide) characters.
		if (mb_strlen($line) > WT_GEDCOM_LINE_LENGTH) {
			list($level, $tag)=explode(' ', $line, 3);
			if ($tag != 'CONT' && $tag != 'CONC') {
				$level++;
			}
			do {
				// Split after $pos chars
				$pos=WT_GEDCOM_LINE_LENGTH;
				if ($WORD_WRAPPED_NOTES) {
					// Split on a space, and remove it (for compatibility with some desktop apps)
					while ($pos && mb_substr($line, $pos-1, 1)!=' ') {
						--$pos;
					}
					if ($pos == strpos($line, ' ', 3) + 1) {
						// No spaces in the data! Can’t split it :-(
						break;
					} else {
						$newrec .= mb_substr($line, 0, $pos - 1).WT_EOL;
						$line=$level.' CONC ' . mb_substr($line, $pos);
					}
				} else {
					// Split on a non-space (standard gedcom behaviour)
					while ($pos && mb_substr($line, $pos-1, 1) == ' ') {
						--$pos;
					}
					if ($pos == strpos($line, ' ', 3)) {
						// No non-spaces in the data! Can’t split it :-(
						break;
					}
					$newrec .= mb_substr($line, 0, $pos) . WT_EOL;
					$line = $level . ' CONC ' . mb_substr($line, $pos);
				}
			} while (mb_strlen($line) > WT_GEDCOM_LINE_LENGTH);
		}
		$newrec .= $line . WT_EOL;
	}
	return $newrec;
}

/**
 * Create a header for a (newly-created or already-imported) gedcom file.
 *
 * @param string $gedfile
 *
 * @return string
 */
function gedcom_header($gedfile) {
	$ged_id = get_id_from_gedcom($gedfile);

	// Default values for a new header
	$HEAD = "0 HEAD";
	$SOUR = "\n1 SOUR " . WT_WEBTREES . "\n2 NAME " . WT_WEBTREES . "\n2 VERS " . WT_VERSION;
	$DEST = "\n1 DEST DISKETTE";
	$DATE = "\n1 DATE " . strtoupper(date("d M Y")) . "\n2 TIME " . date("H:i:s");
	$GEDC = "\n1 GEDC\n2 VERS 5.5.1\n2 FORM Lineage-Linked";
	$CHAR = "\n1 CHAR UTF-8";
	$FILE = "\n1 FILE {$gedfile}";
	$LANG = "";
	$PLAC = "\n1 PLAC\n2 FORM City, County, State/Province, Country";
	$COPR = "";
	$SUBN = "";
	$SUBM = "\n1 SUBM @SUBM@\n0 @SUBM@ SUBM\n1 NAME " . Auth::user()->getUserName(); // The SUBM record is mandatory

	// Preserve some values from the original header
	$record = WT_GedcomRecord::getInstance('HEAD');
	if ($fact = $record->getFirstFact('PLAC')) {
		$PLAC = "\n1 PLAC\n2 FORM " . $fact->getAttribute('FORM');
	}
	if ($fact = $record->getFirstFact('LANG')) {
		$LANG = $fact->getValue();
	}
	if ($fact = $record->getFirstFact('SUBN')) {
		$SUBN = $fact->getValue();
	}
	if ($fact = $record->getFirstFact('COPR')) {
		$COPR = $fact->getValue();
	}
	// Link to actual SUBM/SUBN records, if they exist
	$subn=
		WT_DB::prepare("SELECT o_id FROM `##other` WHERE o_type=? AND o_file=?")
		->execute(array('SUBN', $ged_id))
		->fetchOne();
	if ($subn) {
		$SUBN="\n1 SUBN @{$subn}@";
	}
	$subm=
		WT_DB::prepare("SELECT o_id FROM `##other` WHERE o_type=? AND o_file=?")
		->execute(array('SUBM', $ged_id))
		->fetchOne();
	if ($subm) {
		$SUBM="\n1 SUBM @{$subm}@";
	}

	return $HEAD . $SOUR . $DEST . $DATE . $GEDC . $CHAR . $FILE . $COPR . $LANG . $PLAC . $SUBN . $SUBM . "\n";
}

/**
 * Prepend the GEDCOM_MEDIA_PATH to media filenames.
 *
 * @param string $rec
 * @param string $path
 *
 * @return string
 */
function convert_media_path($rec, $path) {
	if ($path && preg_match('/\n1 FILE (.+)/', $rec, $match)) {
		$old_file_name=$match[1];
		if (!preg_match('~^(https?|ftp):~', $old_file_name)) { // Don’t modify external links
			// Adding a windows path?  Convert the slashes.
			if (strpos($path, '\\')!==false) {
				$new_file_name=preg_replace('~/+~', '\\', $old_file_name);
			} else {
				$new_file_name=$old_file_name;
			}
			// Path not present - add it.
			if (strpos($new_file_name, $path)===false) {
				$new_file_name=$path . $new_file_name;
			}
			$rec=str_replace("\n1 FILE ".$old_file_name, "\n1 FILE ".$new_file_name, $rec);
		}
	}

	return $rec;
}

/**
 * Export the database in GEDCOM format
 *
 * @param string   $gedcom
 * @param resource $gedout        Handle to a writable stream
 * @param string[] $exportOptions Export options are as follows:
 *                                'privatize':    which Privacy rules apply?  (none, visitor, user, manager)
 *                                'toANSI':       should the output be produced in ANSI instead of UTF-8?  (yes, no)
 *                                'path':         what constant should prefix all media file paths?  (eg: media/  or c:\my pictures\my family
 *                                'slashes':      what folder separators apply to media file paths?  (forward, backward)
 *
 */
function export_gedcom($gedcom, $gedout, $exportOptions) {
	global $GEDCOM;

	// Temporarily switch to the specified GEDCOM
	$oldGEDCOM = $GEDCOM;
	$GEDCOM = $gedcom;
	$ged_id = get_id_from_gedcom($gedcom);

	switch($exportOptions['privatize']) {
	case 'gedadmin':
		$access_level = WT_PRIV_NONE;
		break;
	case 'user':
		$access_level = WT_PRIV_USER;
		break;
	case 'visitor':
		$access_level = WT_PRIV_PUBLIC;
		break;
	case 'none':
		$access_level = WT_PRIV_HIDE;
		break;
	}

	$head = gedcom_header($gedcom);
	if ($exportOptions['toANSI'] == 'yes') {
		$head = str_replace('UTF-8', 'ANSI', $head);
		$head = utf8_decode($head);
	}
	$head = reformat_record_export($head);
	fwrite($gedout, $head);

	// Buffer the output.  Lots of small fwrite() calls can be very slow when writing large gedcoms.
	$buffer = '';

	// Generate the OBJE/SOUR/REPO/NOTE records first, as their privacy calcualations involve
	// database queries, and we wish to avoid large gaps between queries due to MySQL connection timeouts.
	$tmp_gedcom = '';
	$rows = WT_DB::prepare(
		"SELECT 'OBJE' AS type, m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom".
		" FROM `##media` WHERE m_file=? ORDER BY m_id"
	)->execute(array($ged_id))->fetchAll();
	foreach ($rows as $row) {
		$rec = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom)->privatizeGedcom($access_level);
		$rec = convert_media_path($rec, $exportOptions['path']);
		if ($exportOptions['toANSI'] == 'yes') {
			$rec = utf8_decode($rec);
		}
		$tmp_gedcom .= reformat_record_export($rec);
	}

	$rows = WT_DB::prepare(
		"SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom".
		" FROM `##sources` WHERE s_file=? ORDER BY s_id"
	)->execute(array($ged_id))->fetchAll();
	foreach ($rows as $row) {
		$rec = WT_Source::getInstance($row->xref, $row->gedcom_id, $row->gedcom)->privatizeGedcom($access_level);
		if ($exportOptions['toANSI'] == 'yes') {
			$rec = utf8_decode($rec);
		}
		$tmp_gedcom .= reformat_record_export($rec);
	}

	$rows = WT_DB::prepare(
		"SELECT o_type AS type, o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom".
		" FROM `##other` WHERE o_file=? AND o_type!='HEAD' AND o_type!='TRLR' ORDER BY o_id"
	)->execute(array($ged_id))->fetchAll();
	foreach ($rows as $row) {
		switch ($row->type) {
		case 'NOTE':
			$record = WT_Note::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			break;
		case 'REPO':
			$record = WT_Repository::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			break;
		default:
			$record = WT_GedcomRecord::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			break;
		}

		$rec = $record->privatizeGedcom($access_level);
		if ($exportOptions['toANSI'] == 'yes') {
			$rec = utf8_decode($rec);
		}
		$tmp_gedcom .= reformat_record_export($rec);
	}

	$rows = WT_DB::prepare(
		"SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom".
		" FROM `##individuals` WHERE i_file=? ORDER BY i_id"
	)->execute(array($ged_id))->fetchAll();
	foreach ($rows as $row) {
		$rec = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom)->privatizeGedcom($access_level);
		if ($exportOptions['toANSI'] ==  'yes') {
			$rec = utf8_decode($rec);
		}
		$buffer .= reformat_record_export($rec);
		if (strlen($buffer) > 65536) {
			fwrite($gedout, $buffer);
			$buffer = '';
		}
	}

	$rows = WT_DB::prepare(
		"SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom".
		" FROM `##families` WHERE f_file=? ORDER BY f_id"
	)->execute(array($ged_id))->fetchAll();
	foreach ($rows as $row) {
		$rec = WT_Family::getInstance($row->xref, $row->gedcom_id, $row->gedcom)->privatizeGedcom($access_level);
		if ($exportOptions['toANSI'] == 'yes') {
			$rec = utf8_decode($rec);
		}
		$buffer .= reformat_record_export($rec);
		if (strlen($buffer) > 65536) {
			fwrite($gedout, $buffer);
			$buffer = '';
		}
	}

	fwrite($gedout, $buffer);
	fwrite($gedout, $tmp_gedcom);
	fwrite($gedout, '0 TRLR' . WT_EOL);

	$GEDCOM = $oldGEDCOM;
}
