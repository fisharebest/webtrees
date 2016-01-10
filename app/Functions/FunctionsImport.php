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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use PDOException;

/**
 * Class FunctionsImport - common functions
 */
class FunctionsImport {
	/**
	 * Tidy up a gedcom record on import, so that we can access it consistently/efficiently.
	 *
	 * @param string $rec
	 * @param Tree $tree
	 *
	 * @return string
	 */
	public static function reformatRecord($rec, Tree $tree) {
		// Strip out UTF8 formatting characters
		$rec = str_replace(array(WT_UTF8_BOM, WT_UTF8_LRM, WT_UTF8_RLM), '', $rec);

		// Strip out mac/msdos line endings
		$rec = preg_replace("/[\r\n]+/", "\n", $rec);

		// Extract lines from the record; lines consist of: level + optional xref + tag + optional data
		$num_matches = preg_match_all('/^[ \t]*(\d+)[ \t]*(@[^@]*@)?[ \t]*(\w+)[ \t]?(.*)$/m', $rec, $matches, PREG_SET_ORDER);

		// Process the record line-by-line
		$newrec = '';
		foreach ($matches as $n => $match) {
			list(, $level, $xref, $tag, $data) = $match;
			$tag                               = strtoupper($tag); // Tags should always be upper case
			switch ($tag) {
				// Convert PhpGedView tags to WT
				case '_PGVU':
					$tag = '_WT_USER';
					break;
				case '_PGV_OBJS':
					$tag = '_WT_OBJE_SORT';
					break;
				// Convert FTM-style "TAG_FORMAL_NAME" into "TAG".
				case 'ABBREVIATION':
					$tag = 'ABBR';
					break;
				case 'ADDRESS':
					$tag = 'ADDR';
					break;
				case 'ADDRESS1':
					$tag = 'ADR1';
					break;
				case 'ADDRESS2':
					$tag = 'ADR2';
					break;
				case 'ADDRESS3':
					$tag = 'ADR3';
					break;
				case 'ADOPTION':
					$tag = 'ADOP';
					break;
				case 'ADULT_CHRISTENING':
					$tag = 'CHRA';
					break;
				case 'AFN':
					// AFN values are upper case
					$data = strtoupper($data);
					break;
				case 'AGENCY':
					$tag = 'AGNC';
					break;
				case 'ALIAS':
					$tag = 'ALIA';
					break;
				case 'ANCESTORS':
					$tag = 'ANCE';
					break;
				case 'ANCES_INTEREST':
					$tag = 'ANCI';
					break;
				case 'ANNULMENT':
					$tag = 'ANUL';
					break;
				case 'ASSOCIATES':
					$tag = 'ASSO';
					break;
				case 'AUTHOR':
					$tag = 'AUTH';
					break;
				case 'BAPTISM':
					$tag = 'BAPM';
					break;
				case 'BAPTISM_LDS':
					$tag = 'BAPL';
					break;
				case 'BAR_MITZVAH':
					$tag = 'BARM';
					break;
				case 'BAS_MITZVAH':
					$tag = 'BASM';
					break;
				case 'BIRTH':
					$tag = 'BIRT';
					break;
				case 'BLESSING':
					$tag = 'BLES';
					break;
				case 'BURIAL':
					$tag = 'BURI';
					break;
				case 'CALL_NUMBER':
					$tag = 'CALN';
					break;
				case 'CASTE':
					$tag = 'CAST';
					break;
				case 'CAUSE':
					$tag = 'CAUS';
					break;
				case 'CENSUS':
					$tag = 'CENS';
					break;
				case 'CHANGE':
					$tag = 'CHAN';
					break;
				case 'CHARACTER':
					$tag = 'CHAR';
					break;
				case 'CHILD':
					$tag = 'CHIL';
					break;
				case 'CHILDREN_COUNT':
					$tag = 'NCHI';
					break;
				case 'CHRISTENING':
					$tag = 'CHR';
					break;
				case 'CONCATENATION':
					$tag = 'CONC';
					break;
				case 'CONFIRMATION':
					$tag = 'CONF';
					break;
				case 'CONFIRMATION_LDS':
					$tag = 'CONL';
					break;
				case 'CONTINUED':
					$tag = 'CONT';
					break;
				case 'COPYRIGHT':
					$tag = 'COPR';
					break;
				case 'CORPORATE':
					$tag = 'CORP';
					break;
				case 'COUNTRY':
					$tag = 'CTRY';
					break;
				case 'CREMATION':
					$tag = 'CREM';
					break;
				case 'DATE':
					// Preserve text from INT dates
					if (strpos($data, '(') !== false) {
						list($date, $text) = explode('(', $data, 2);
						$text              = ' (' . $text;
					} else {
						$date = $data;
						$text = '';
					}
					// Capitals
					$date = strtoupper($date);
					// Temporarily add leading/trailing spaces, to allow efficient matching below
					$date = " {$date} ";
					// Ensure space digits and letters
					$date = preg_replace('/([A-Z])(\d)/', '$1 $2', $date);
					$date = preg_replace('/(\d)([A-Z])/', '$1 $2', $date);
					// Ensure space before/after calendar escapes
					$date = preg_replace('/@#[^@]+@/', ' $0 ', $date);
					// "BET." => "BET"
					$date = preg_replace('/(\w\w)\./', '$1', $date);
					// "CIR" => "ABT"
					$date = str_replace(' CIR ', ' ABT ', $date);
					$date = str_replace(' APX ', ' ABT ', $date);
					// B.C. => BC (temporarily, to allow easier handling of ".")
					$date = str_replace(' B.C. ', ' BC ', $date);
					// "BET X - Y " => "BET X AND Y"
					$date = preg_replace('/^(.* BET .+) - (.+)/', '$1 AND $2', $date);
					$date = preg_replace('/^(.* FROM .+) - (.+)/', '$1 TO $2', $date);
					// "@#ESC@ FROM X TO Y" => "FROM @#ESC@ X TO @#ESC@ Y"
					$date = preg_replace('/^ +(@#[^@]+@) +FROM +(.+) +TO +(.+)/', ' FROM $1 $2 TO $1 $3', $date);
					$date = preg_replace('/^ +(@#[^@]+@) +BET +(.+) +AND +(.+)/', ' BET $1 $2 AND $1 $3', $date);
					// "@#ESC@ AFT X" => "AFT @#ESC@ X"
					$date = preg_replace('/^ +(@#[^@]+@) +(FROM|BET|TO|AND|BEF|AFT|CAL|EST|INT|ABT) +(.+)/', ' $2 $1 $3', $date);
					// Ignore any remaining punctuation, e.g. "14-MAY, 1900" => "14 MAY 1900"
					// (don't change "/" - it is used in NS/OS dates)
					$date = preg_replace('/[.,:;-]/', ' ', $date);
					// BC => B.C.
					$date = str_replace(' BC ', ' B.C. ', $date);
					// Append the "INT" text
					$data = $date . $text;
					break;
				case 'DEATH':
					$tag = 'DEAT';
					break;
				case '_DEATH_OF_SPOUSE':
					$tag = '_DETS';
					break;
				case '_DEGREE':
					$tag = '_DEG';
					break;
				case 'DESCENDANTS':
					$tag = 'DESC';
					break;
				case 'DESCENDANT_INT':
					$tag = 'DESI';
					break;
				case 'DESTINATION':
					$tag = 'DEST';
					break;
				case 'DIVORCE':
					$tag = 'DIV';
					break;
				case 'DIVORCE_FILED':
					$tag = 'DIVF';
					break;
				case 'EDUCATION':
					$tag = 'EDUC';
					break;
				case 'EMIGRATION':
					$tag = 'EMIG';
					break;
				case 'ENDOWMENT':
					$tag = 'ENDL';
					break;
				case 'ENGAGEMENT':
					$tag = 'ENGA';
					break;
				case 'EVENT':
					$tag = 'EVEN';
					break;
				case 'FACSIMILE':
					$tag = 'FAX';
					break;
				case 'FAMILY':
					$tag = 'FAM';
					break;
				case 'FAMILY_CHILD':
					$tag = 'FAMC';
					break;
				case 'FAMILY_FILE':
					$tag = 'FAMF';
					break;
				case 'FAMILY_SPOUSE':
					$tag = 'FAMS';
					break;
				case 'FIRST_COMMUNION':
					$tag = 'FCOM';
					break;
				case '_FILE':
					$tag = 'FILE';
					break;
				case 'FORMAT':
					$tag = 'FORM';
				case 'FORM':
					// Consistent commas
					$data = preg_replace('/ *, */', ', ', $data);
					break;
				case 'GEDCOM':
					$tag = 'GEDC';
					break;
				case 'GIVEN_NAME':
					$tag = 'GIVN';
					break;
				case 'GRADUATION':
					$tag = 'GRAD';
					break;
				case 'HEADER':
					$tag = 'HEAD';
				case 'HEAD':
					// HEAD records don't have an XREF or DATA
					if ($level == '0') {
						$xref = '';
						$data = '';
					}
					break;
				case 'HUSBAND':
					$tag = 'HUSB';
					break;
				case 'IDENT_NUMBER':
					$tag = 'IDNO';
					break;
				case 'IMMIGRATION':
					$tag = 'IMMI';
					break;
				case 'INDIVIDUAL':
					$tag = 'INDI';
					break;
				case 'LANGUAGE':
					$tag = 'LANG';
					break;
				case 'LATITUDE':
					$tag = 'LATI';
					break;
				case 'LONGITUDE':
					$tag = 'LONG';
					break;
				case 'MARRIAGE':
					$tag = 'MARR';
					break;
				case 'MARRIAGE_BANN':
					$tag = 'MARB';
					break;
				case 'MARRIAGE_COUNT':
					$tag = 'NMR';
					break;
				case 'MARRIAGE_CONTRACT':
					$tag = 'MARC';
					break;
				case 'MARRIAGE_LICENSE':
					$tag = 'MARL';
					break;
				case 'MARRIAGE_SETTLEMENT':
					$tag = 'MARS';
					break;
				case 'MEDIA':
					$tag = 'MEDI';
					break;
				case '_MEDICAL':
					$tag = '_MDCL';
					break;
				case '_MILITARY_SERVICE':
					$tag = '_MILT';
					break;
				case 'NAME':
					// Tidy up whitespace
					$data = preg_replace('/  +/', ' ', trim($data));
					break;
				case 'NAME_PREFIX':
					$tag = 'NPFX';
					break;
				case 'NAME_SUFFIX':
					$tag = 'NSFX';
					break;
				case 'NATIONALITY':
					$tag = 'NATI';
					break;
				case 'NATURALIZATION':
					$tag = 'NATU';
					break;
				case 'NICKNAME':
					$tag = 'NICK';
					break;
				case 'OBJECT':
					$tag = 'OBJE';
					break;
				case 'OCCUPATION':
					$tag = 'OCCU';
					break;
				case 'ORDINANCE':
					$tag = 'ORDI';
					break;
				case 'ORDINATION':
					$tag = 'ORDN';
					break;
				case 'PEDIGREE':
					$tag = 'PEDI';
				case 'PEDI':
					// PEDI values are lower case
					$data = strtolower($data);
					break;
				case 'PHONE':
					$tag = 'PHON';
					break;
				case 'PHONETIC':
					$tag = 'FONE';
					break;
				case 'PHY_DESCRIPTION':
					$tag = 'DSCR';
					break;
				case 'PLACE':
					$tag = 'PLAC';
				case 'PLAC':
					// Consistent commas
					$data = preg_replace('/ *(،|,) */', ', ', $data);
					// The Master Genealogist stores LAT/LONG data in the PLAC field, e.g. Pennsylvania, USA, 395945N0751013W
					if (preg_match('/(.*), (\d\d)(\d\d)(\d\d)([NS])(\d\d\d)(\d\d)(\d\d)([EW])$/', $data, $match)) {
						$data =
							$match[1] . "\n" .
							($level + 1) . " MAP\n" .
							($level + 2) . " LATI " . ($match[5] . (round($match[2] + ($match[3] / 60) + ($match[4] / 3600), 4))) . "\n" .
							($level + 2) . " LONG " . ($match[9] . (round($match[6] + ($match[7] / 60) + ($match[8] / 3600), 4)));
					}
					break;
				case 'POSTAL_CODE':
					$tag = 'POST';
					break;
				case 'PROBATE':
					$tag = 'PROB';
					break;
				case 'PROPERTY':
					$tag = 'PROP';
					break;
				case 'PUBLICATION':
					$tag = 'PUBL';
					break;
				case 'QUALITY_OF_DATA':
					$tag = 'QUAL';
					break;
				case 'REC_FILE_NUMBER':
					$tag = 'RFN';
					break;
				case 'REC_ID_NUMBER':
					$tag = 'RIN';
					break;
				case 'REFERENCE':
					$tag = 'REFN';
					break;
				case 'RELATIONSHIP':
					$tag = 'RELA';
					break;
				case 'RELIGION':
					$tag = 'RELI';
					break;
				case 'REPOSITORY':
					$tag = 'REPO';
					break;
				case 'RESIDENCE':
					$tag = 'RESI';
					break;
				case 'RESTRICTION':
					$tag = 'RESN';
				case 'RESN':
					// RESN values are lower case (confidential, privacy, locked, none)
					$data = strtolower($data);
					if ($data == 'invisible') {
						$data = 'confidential'; // From old versions of Legacy.
					}
					break;
				case 'RETIREMENT':
					$tag = 'RETI';
					break;
				case 'ROMANIZED':
					$tag = 'ROMN';
					break;
				case 'SEALING_CHILD':
					$tag = 'SLGC';
					break;
				case 'SEALING_SPOUSE':
					$tag = 'SLGS';
					break;
				case 'SOC_SEC_NUMBER':
					$tag = 'SSN';
					break;
				case 'SEX':
					$data = strtoupper($data);
					break;
				case 'SOURCE':
					$tag = 'SOUR';
					break;
				case 'STATE':
					$tag = 'STAE';
					break;
				case 'STATUS':
					$tag = 'STAT';
				case 'STAT':
					if ($data == 'CANCELLED') {
						// PhpGedView mis-spells this tag - correct it.
						$data = 'CANCELED';
					}
					break;
				case 'SUBMISSION':
					$tag = 'SUBN';
					break;
				case 'SUBMITTER':
					$tag = 'SUBM';
					break;
				case 'SURNAME':
					$tag = 'SURN';
					break;
				case 'SURN_PREFIX':
					$tag = 'SPFX';
					break;
				case 'TEMPLE':
					$tag = 'TEMP';
				case 'TEMP':
					// Temple codes are upper case
					$data = strtoupper($data);
					break;
				case 'TITLE':
					$tag = 'TITL';
					break;
				case 'TRAILER':
					$tag = 'TRLR';
				case 'TRLR':
					// TRLR records don't have an XREF or DATA
					if ($level == '0') {
						$xref = '';
						$data = '';
					}
					break;
				case 'VERSION':
					$tag = 'VERS';
					break;
				case 'WEB':
					$tag = 'WWW';
					break;
			}
			// Suppress "Y", for facts/events with a DATE or PLAC
			if ($data == 'y') {
				$data = 'Y';
			}
			if ($level == '1' && $data == 'Y') {
				for ($i = $n + 1; $i < $num_matches - 1 && $matches[$i][1] != '1'; ++$i) {
					if ($matches[$i][3] == 'DATE' || $matches[$i][3] == 'PLAC') {
						$data = '';
						break;
					}
				}
			}
			// Reassemble components back into a single line
			switch ($tag) {
				default:
					// Remove tabs and multiple/leading/trailing spaces
					if (strpos($data, "\t") !== false) {
						$data = str_replace("\t", ' ', $data);
					}
					if (substr($data, 0, 1) == ' ' || substr($data, -1, 1) == ' ') {
						$data = trim($data);
					}
					while (strpos($data, '  ')) {
						$data = str_replace('  ', ' ', $data);
					}
					$newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level == '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag != "NOTE" ? '' : ' ' . $data);
					break;
				case 'NOTE':
				case 'TEXT':
				case 'DATA':
				case 'CONT':
					$newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level == '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag != "NOTE" ? '' : ' ' . $data);
					break;
				case 'FILE':
					// Strip off the user-defined path prefix
					$GEDCOM_MEDIA_PATH = $tree->getPreference('GEDCOM_MEDIA_PATH');
					if ($GEDCOM_MEDIA_PATH && strpos($data, $GEDCOM_MEDIA_PATH) === 0) {
						$data = substr($data, strlen($GEDCOM_MEDIA_PATH));
					}
					// convert backslashes in filenames to forward slashes
					$data = preg_replace("/\\\/", "/", $data);

					$newrec .= ($newrec ? "\n" : '') . $level . ' ' . ($level == '0' && $xref ? $xref . ' ' : '') . $tag . ($data === '' && $tag != "NOTE" ? '' : ' ' . $data);
					break;
				case 'CONC':
					// Merge CONC lines, to simplify access later on.
					$newrec .= ($tree->getPreference('WORD_WRAPPED_NOTES') ? ' ' : '') . $data;
					break;
			}
		}

		return $newrec;
	}

	/**
	 * import record into database
	 *
	 * this function will parse the given gedcom record and add it to the database
	 *
	 * @param string $gedrec the raw gedcom record to parse
	 * @param Tree $tree import the record into this tree
	 * @param bool $update whether or not this is an updated record that has been accepted
	 */
	public static function importRecord($gedrec, Tree $tree, $update) {
		$tree_id = $tree->getTreeId();

		// Escaped @ signs (only if importing from file)
		if (!$update) {
			$gedrec = str_replace('@@', '@', $gedrec);
		}

		// Standardise gedcom format
		$gedrec = self::reformatRecord($gedrec, $tree);

		// import different types of records
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ (' . WT_REGEX_TAG . ')/', $gedrec, $match)) {
			list(, $xref, $type) = $match;
			// check for a _UID, if the record doesn't have one, add one
			if ($tree->getPreference('GENERATE_UIDS') && !strpos($gedrec, "\n1 _UID ")) {
				$gedrec .= "\n1 _UID " . GedcomTag::createUid();
			}
		} elseif (preg_match('/0 (HEAD|TRLR)/', $gedrec, $match)) {
			$type = $match[1];
			$xref = $type; // For HEAD/TRLR, use type as pseudo XREF.
		} else {
			echo I18N::translate('Invalid GEDCOM format'), '<br><pre>', $gedrec, '</pre>';

			return;
		}

		// If the user has downloaded their GEDCOM data (containing media objects) and edited it
		// using an application which does not support (and deletes) media objects, then add them
		// back in.
		if ($tree->getPreference('keep_media') && $xref) {
			$old_linked_media =
				Database::prepare("SELECT l_to FROM `##link` WHERE l_from=? AND l_file=? AND l_type='OBJE'")
					->execute(array($xref, $tree_id))
					->fetchOneColumn();
			foreach ($old_linked_media as $media_id) {
				$gedrec .= "\n1 OBJE @" . $media_id . "@";
			}
		}

		switch ($type) {
			case 'INDI':
				// Convert inline media into media objects
				$gedrec = self::convertInlineMedia($tree, $gedrec);

				$record = new Individual($xref, $gedrec, null, $tree);
				if ($tree->getPreference('USE_RIN') && preg_match('/\n1 RIN (.+)/', $gedrec, $match)) {
					$rin = $match[1];
				} else {
					$rin = $xref;
				}
				Database::prepare(
					"INSERT INTO `##individuals` (i_id, i_file, i_rin, i_sex, i_gedcom) VALUES (?, ?, ?, ?, ?)"
				)->execute(array(
					$xref, $tree_id, $rin, $record->getSex(), $gedrec,
				));
				// Update the cross-reference/index tables.
				self::updatePlaces($xref, $tree_id, $gedrec);
				self::updateDates($xref, $tree_id, $gedrec);
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
			case 'FAM':
				// Convert inline media into media objects
				$gedrec = self::convertInlineMedia($tree, $gedrec);

				if (preg_match('/\n1 HUSB @(' . WT_REGEX_XREF . ')@/', $gedrec, $match)) {
					$husb = $match[1];
				} else {
					$husb = '';
				}
				if (preg_match('/\n1 WIFE @(' . WT_REGEX_XREF . ')@/', $gedrec, $match)) {
					$wife = $match[1];
				} else {
					$wife = '';
				}
				$nchi = preg_match_all('/\n1 CHIL @(' . WT_REGEX_XREF . ')@/', $gedrec, $match);
				if (preg_match('/\n1 NCHI (\d+)/', $gedrec, $match)) {
					$nchi = max($nchi, $match[1]);
				}
				Database::prepare(
					"INSERT INTO `##families` (f_id, f_file, f_husb, f_wife, f_gedcom, f_numchil) VALUES (?, ?, ?, ?, ?, ?)"
				)->execute(array(
					$xref, $tree_id, $husb, $wife, $gedrec, $nchi,
				));
				// Update the cross-reference/index tables.
				self::updatePlaces($xref, $tree_id, $gedrec);
				self::updateDates($xref, $tree_id, $gedrec);
				self::updateLinks($xref, $tree_id, $gedrec);
				break;
			case 'SOUR':
				// Convert inline media into media objects
				$gedrec = self::convertInlineMedia($tree, $gedrec);

				$record = new Source($xref, $gedrec, null, $tree);
				if (preg_match('/\n1 TITL (.+)/', $gedrec, $match)) {
					$name = $match[1];
				} elseif (preg_match('/\n1 ABBR (.+)/', $gedrec, $match)) {
					$name = $match[1];
				} else {
					$name = $xref;
				}
				Database::prepare(
					"INSERT INTO `##sources` (s_id, s_file, s_name, s_gedcom) VALUES (?, ?, LEFT(?, 255), ?)"
				)->execute(array(
					$xref, $tree_id, $name, $gedrec,
				));
				// Update the cross-reference/index tables.
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
			case 'REPO':
				// Convert inline media into media objects
				$gedrec = self::convertInlineMedia($tree, $gedrec);

				$record = new Repository($xref, $gedrec, null, $tree);
				Database::prepare(
					"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom) VALUES (?, ?, 'REPO', ?)"
				)->execute(array(
					$xref, $tree_id, $gedrec,
				));
				// Update the cross-reference/index tables.
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
			case 'NOTE':
				$record = new Note($xref, $gedrec, null, $tree);
				Database::prepare(
					"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom) VALUES (?, ?, 'NOTE', ?)"
				)->execute(array(
					$xref, $tree_id, $gedrec,
				));
				// Update the cross-reference/index tables.
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
			case 'OBJE':
				$record = new Media($xref, $gedrec, null, $tree);
				Database::prepare(
					"INSERT INTO `##media` (m_id, m_ext, m_type, m_titl, m_filename, m_file, m_gedcom) VALUES (?, ?, ?, ?, ?, ?, ?)"
				)->execute(array(
					$xref, $record->extension(), $record->getMediaType(), $record->getTitle(), $record->getFilename(), $tree_id, $gedrec,
				));
				// Update the cross-reference/index tables.
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
			case 'HEAD':
				// Force HEAD records to have a creation date.
				if (!strpos($gedrec, "\n1 DATE ")) {
					$gedrec .= "\n1 DATE " . date('j M Y');
				}
			// No break;
			case 'TRLR':
			case 'SUBM':
			case 'SUBN':
				Database::prepare(
					"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom) VALUES (?, ?, ?, ?)"
				)->execute(array($xref, $tree_id, $type, $gedrec));
				// Update the cross-reference/index tables.
			self::updateLinks($xref, $tree_id, $gedrec);
				break;
			default:
				$record = new GedcomRecord($xref, $gedrec, null, $tree);
				Database::prepare(
					"INSERT INTO `##other` (o_id, o_file, o_type, o_gedcom) VALUES (?, ?, ?, ?)"
				)->execute(array($xref, $tree_id, $type, $gedrec));
				// Update the cross-reference/index tables.
				self::updateLinks($xref, $tree_id, $gedrec);
				self::updateNames($xref, $tree_id, $record);
				break;
		}
	}

	/**
	 * extract all places from the given record and insert them into the places table
	 *
	 * @param string $gid
	 * @param int $ged_id
	 * @param string $gedrec
	 */
	public static function updatePlaces($gid, $ged_id, $gedrec) {
		global $placecache;

		if (!isset($placecache)) {
			$placecache = array();
		}
		$personplace = array();
		// import all place locations, but not control info such as
		// 0 HEAD/1 PLAC or 0 _EVDEF/1 PLAC
		$pt = preg_match_all("/^[2-9] PLAC (.+)/m", $gedrec, $match, PREG_SET_ORDER);
		for ($i = 0; $i < $pt; $i++) {
			$place    = trim($match[$i][1]);
			$lowplace = I18N::strtolower($place);
			//-- if we have already visited this place for this person then we don't need to again
			if (isset($personplace[$lowplace])) {
				continue;
			}
			$personplace[$lowplace] = 1;
			$places                 = explode(',', $place);
			//-- reverse the array to start at the highest level
			$secalp    = array_reverse($places);
			$parent_id = 0;
			$search    = true;

			foreach ($secalp as $place) {
				$place = trim($place);
				$key   = strtolower(mb_substr($place, 0, 150) . "_" . $parent_id);
				//-- if this place has already been added then we don't need to add it again
				if (isset($placecache[$key])) {
					$parent_id = $placecache[$key];
					if (!isset($personplace[$key])) {
						$personplace[$key] = 1;
						// Use INSERT IGNORE as a (temporary) fix for https://bugs.launchpad.net/webtrees/+bug/582226
						// It ignores places that utf8_unicode_ci consider to be the same (i.e. accents).
						// For example Québec and Quebec
						// We need a better solution that attaches multiple names to single places
						Database::prepare(
							"INSERT IGNORE INTO `##placelinks` (pl_p_id, pl_gid, pl_file) VALUES (?, ?, ?)"
						)->execute(array(
							$parent_id, $gid, $ged_id,
						));
					}
					continue;
				}

				//-- only search the database while we are finding places in it
				if ($search) {
					//-- check if this place and level has already been added
					$tmp = Database::prepare(
						"SELECT p_id FROM `##places` WHERE p_file = ? AND p_parent_id = ? AND p_place = LEFT(?, 150)"
					)->execute(array(
						$ged_id, $parent_id, $place,
					))->fetchOne();
					if ($tmp) {
						$p_id = $tmp;
					} else {
						$search = false;
					}
				}

				//-- if we are not searching then we have to insert the place into the db
				if (!$search) {
					$std_soundex = Soundex::russell($place);
					$dm_soundex  = Soundex::daitchMokotoff($place);
					Database::prepare(
						"INSERT INTO `##places` (p_place, p_parent_id, p_file, p_std_soundex, p_dm_soundex) VALUES (LEFT(?, 150), ?, ?, ?, ?)"
					)->execute(array(
						$place, $parent_id, $ged_id, $std_soundex, $dm_soundex,
					));
					$p_id = Database::getInstance()->lastInsertId();
				}

				Database::prepare(
					"INSERT IGNORE INTO `##placelinks` (pl_p_id, pl_gid, pl_file) VALUES (?, ?, ?)"
				)->execute(array(
					$p_id, $gid, $ged_id,
				));
				//-- increment the level and assign the parent id for the next place level
				$parent_id         = $p_id;
				$placecache[$key]  = $p_id;
				$personplace[$key] = 1;
			}
		}
	}

	/**
	 * Extract all the dates from the given record and insert them into the database.
	 *
	 * @param string $xref
	 * @param int $ged_id
	 * @param string $gedrec
	 */
	public static function updateDates($xref, $ged_id, $gedrec) {
		if (strpos($gedrec, '2 DATE ') && preg_match_all("/\n1 (\w+).*(?:\n[2-9].*)*(?:\n2 DATE (.+))(?:\n[2-9].*)*/", $gedrec, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$fact = $match[1];
				if (($fact == 'FACT' || $fact == 'EVEN') && preg_match("/\n2 TYPE ([A-Z]{3,5})/", $match[0], $tmatch)) {
					$fact = $tmatch[1];
				}
				$date = new Date($match[2]);
				Database::prepare(
					"INSERT INTO `##dates` (d_day,d_month,d_mon,d_year,d_julianday1,d_julianday2,d_fact,d_gid,d_file,d_type) VALUES (?,?,?,?,?,?,?,?,?,?)"
				)->execute(array(
					$date->minimumDate()->d,
					$date->minimumDate()->format('%O'),
					$date->minimumDate()->m,
					$date->minimumDate()->y,
					$date->minimumDate()->minJD,
					$date->minimumDate()->maxJD,
					$fact,
					$xref,
					$ged_id,
					$date->minimumDate()->format('%@'),
				));
				if ($date->minimumDate() !== $date->maximumDate()) {
					Database::prepare(
						"INSERT INTO `##dates` (d_day,d_month,d_mon,d_year,d_julianday1,d_julianday2,d_fact,d_gid,d_file,d_type) VALUES (?,?,?,?,?,?,?,?,?,?)"
					)->execute(array(
						$date->maximumDate()->d,
						$date->maximumDate()->format('%O'),
						$date->maximumDate()->m,
						$date->maximumDate()->y,
						$date->maximumDate()->minJD,
						$date->maximumDate()->maxJD,
						$fact,
						$xref,
						$ged_id,
						$date->maximumDate()->format('%@'),
					));
				}
			}
		}
	}

	/**
	 * Extract all the links from the given record and insert them into the database
	 *
	 * @param string $xref
	 * @param int $ged_id
	 * @param string $gedrec
	 */
	public static function updateLinks($xref, $ged_id, $gedrec) {
		if (preg_match_all('/^\d+ (' . WT_REGEX_TAG . ') @(' . WT_REGEX_XREF . ')@/m', $gedrec, $matches, PREG_SET_ORDER)) {
			$data = array();
			foreach ($matches as $match) {
				// Include each link once only.
				if (!in_array($match[1] . $match[2], $data)) {
					$data[] = $match[1] . $match[2];
					// Ignore any errors, which may be caused by "duplicates" that differ on case/collation, e.g. "S1" and "s1"
					try {
						Database::prepare(
							"INSERT INTO `##link` (l_from, l_to, l_type, l_file) VALUES (?, ?, ?, ?)"
						)->execute(array(
							$xref, $match[2], $match[1], $ged_id,
						));
					} catch (PDOException $e) {
						// We could display a warning here....
					}
				}
			}
		}
	}

	/**
	 * Extract all the names from the given record and insert them into the database.
	 *
	 * @param string $xref
	 * @param int $ged_id
	 * @param GedcomRecord $record
	 */
	public static function updateNames($xref, $ged_id, GedcomRecord $record) {
		foreach ($record->getAllNames() as $n => $name) {
			if ($record instanceof Individual) {
				if ($name['givn'] === '@P.N.') {
					$soundex_givn_std = null;
					$soundex_givn_dm  = null;
				} else {
					$soundex_givn_std = Soundex::russell($name['givn']);
					$soundex_givn_dm  = Soundex::daitchMokotoff($name['givn']);
				}
				if ($name['surn'] === '@N.N.') {
					$soundex_surn_std = null;
					$soundex_surn_dm  = null;
				} else {
					$soundex_surn_std = Soundex::russell($name['surname']);
					$soundex_surn_dm  = Soundex::daitchMokotoff($name['surname']);
				}
				Database::prepare(
					"INSERT INTO `##name` (n_file,n_id,n_num,n_type,n_sort,n_full,n_surname,n_surn,n_givn,n_soundex_givn_std,n_soundex_surn_std,n_soundex_givn_dm,n_soundex_surn_dm) VALUES (?, ?, ?, ?, LEFT(?, 255), LEFT(?, 255), LEFT(?, 255), LEFT(?, 255), ?, ?, ?, ?, ?)"
				)->execute(array(
					$ged_id, $xref, $n, $name['type'], $name['sort'], $name['fullNN'], $name['surname'], $name['surn'], $name['givn'], $soundex_givn_std, $soundex_surn_std, $soundex_givn_dm, $soundex_surn_dm,
				));
			} else {
				Database::prepare(
					"INSERT INTO `##name` (n_file,n_id,n_num,n_type,n_sort,n_full) VALUES (?, ?, ?, ?, LEFT(?, 255), LEFT(?, 255))"
				)->execute(array(
					$ged_id, $xref, $n, $name['type'], $name['sort'], $name['fullNN'],
				));
			}
		}
	}

	/**
	 * Extract inline media data, and convert to media objects.
	 *
	 * @param Tree $tree
	 * @param string $gedrec
	 *
	 * @return string
	 */
	public static function convertInlineMedia(Tree $tree, $gedrec) {
		while (preg_match('/\n1 OBJE(?:\n[2-9].+)+/', $gedrec, $match)) {
			$gedrec = str_replace($match[0], self::createMediaObject(1, $match[0], $tree), $gedrec);
		}
		while (preg_match('/\n2 OBJE(?:\n[3-9].+)+/', $gedrec, $match)) {
			$gedrec = str_replace($match[0], self::createMediaObject(2, $match[0], $tree), $gedrec);
		}
		while (preg_match('/\n3 OBJE(?:\n[4-9].+)+/', $gedrec, $match)) {
			$gedrec = str_replace($match[0], self::createMediaObject(3, $match[0], $tree), $gedrec);
		}

		return $gedrec;
	}

	/**
	 * Create a new media object, from inline media data.
	 *
	 * @param int $level
	 * @param string $gedrec
	 * @param Tree $tree
	 *
	 * @return string
	 */
	public static function createMediaObject($level, $gedrec, Tree $tree) {
		if (preg_match('/\n\d FILE (.+)/', $gedrec, $file_match)) {
			$file = $file_match[1];
		} else {
			$file = '';
		}

		if (preg_match('/\n\d TITL (.+)/', $gedrec, $file_match)) {
			$titl = $file_match[1];
		} else {
			$titl = '';
		}

		// Have we already created a media object with the same title/filename?
		$xref = Database::prepare(
			"SELECT m_id FROM `##media` WHERE m_filename = ? AND m_titl = ? AND m_file = ?"
		)->execute(array(
			$file, $titl, $tree->getTreeId(),
		))->fetchOne();

		if (!$xref) {
			$xref = $tree->getNewXref('OBJE');
			// renumber the lines
			$gedrec = preg_replace_callback('/\n(\d+)/', function ($m) use ($level) {
				return "\n" . ($m[1] - $level);
			}, $gedrec);
			// convert to an object
			$gedrec = str_replace("\n0 OBJE\n", '0 @' . $xref . "@ OBJE\n", $gedrec);
			// Fix Legacy GEDCOMS
			$gedrec = preg_replace('/\n1 FORM (.+)\n1 FILE (.+)\n1 TITL (.+)/', "\n1 FILE $2\n2 FORM $1\n2 TITL $3", $gedrec);
			// Create new record
			$record = new Media($xref, $gedrec, null, $tree);
			Database::prepare(
				"INSERT INTO `##media` (m_id, m_ext, m_type, m_titl, m_filename, m_file, m_gedcom) VALUES (?, ?, ?, ?, ?, ?, ?)"
			)->execute(array(
				$xref, $record->extension(), $record->getMediaType(), $record->getTitle(), $record->getFilename(), $tree->getTreeId(), $gedrec,
			));
		}

		return "\n" . $level . ' OBJE @' . $xref . '@';
	}

	/**
	 * Accept all pending changes for a specified record.
	 *
	 * @param string $xref
	 * @param int $ged_id
	 */
	public static function acceptAllChanges($xref, $ged_id) {
		$changes = Database::prepare(
			"SELECT change_id, gedcom_name, old_gedcom, new_gedcom" .
			" FROM `##change` c" .
			" JOIN `##gedcom` g USING (gedcom_id)" .
			" WHERE c.status='pending' AND xref=? AND gedcom_id=?" .
			" ORDER BY change_id"
		)->execute(array($xref, $ged_id))->fetchAll();
		foreach ($changes as $change) {
			if (empty($change->new_gedcom)) {
				// delete
				self::updateRecord($change->old_gedcom, $ged_id, true);
			} else {
				// add/update
				self::updateRecord($change->new_gedcom, $ged_id, false);
			}
			Database::prepare(
				"UPDATE `##change`" .
				" SET status='accepted'" .
				" WHERE status='pending' AND xref=? AND gedcom_id=?"
			)->execute(array($xref, $ged_id));
			Log::addEditLog("Accepted change {$change->change_id} for {$xref} / {$change->gedcom_name} into database");
		}
	}

	/**
	 * Accept all pending changes for a specified record.
	 *
	 * @param GedcomRecord $record
	 */
	public static function rejectAllChanges(GedcomRecord $record) {
		Database::prepare(
			"UPDATE `##change`" .
			" SET status = 'rejected'" .
			" WHERE status = 'pending' AND xref = :xref AND gedcom_id = :tree_id"
		)->execute(array(
			'xref'    => $record->getXref(),
			'tree_id' => $record->getTree()->getTreeId(),
		));
	}

	/**
	 * update a record in the database
	 *
	 * @param string $gedrec
	 * @param int $ged_id
	 * @param bool $delete
	 */
	public static function updateRecord($gedrec, $ged_id, $delete) {
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ (' . WT_REGEX_TAG . ')/', $gedrec, $match)) {
			list(, $gid, $type) = $match;
		} else {
			echo "ERROR: Invalid gedcom record.";

			return;
		}

		// TODO deleting unlinked places can be done more efficiently in a single query
		$placeids =
			Database::prepare("SELECT pl_p_id FROM `##placelinks` WHERE pl_gid=? AND pl_file=?")
				->execute(array($gid, $ged_id))
				->fetchOneColumn();

		Database::prepare("DELETE FROM `##placelinks` WHERE pl_gid=? AND pl_file=?")->execute(array($gid, $ged_id));
		Database::prepare("DELETE FROM `##dates`      WHERE d_gid =? AND d_file =?")->execute(array($gid, $ged_id));

		//-- delete any unlinked places
		foreach ($placeids as $p_id) {
			$num =
				Database::prepare("SELECT count(pl_p_id) FROM `##placelinks` WHERE pl_p_id=? AND pl_file=?")
					->execute(array($p_id, $ged_id))
					->fetchOne();
			if ($num == 0) {
				Database::prepare("DELETE FROM `##places` WHERE p_id=? AND p_file=?")->execute(array($p_id, $ged_id));
			}
		}

		Database::prepare("DELETE FROM `##name` WHERE n_id=? AND n_file=?")->execute(array($gid, $ged_id));
		Database::prepare("DELETE FROM `##link` WHERE l_from=? AND l_file=?")->execute(array($gid, $ged_id));

		switch ($type) {
			case 'INDI':
				Database::prepare("DELETE FROM `##individuals` WHERE i_id=? AND i_file=?")->execute(array($gid, $ged_id));
				break;
			case 'FAM':
				Database::prepare("DELETE FROM `##families` WHERE f_id=? AND f_file=?")->execute(array($gid, $ged_id));
				break;
			case 'SOUR':
				Database::prepare("DELETE FROM `##sources` WHERE s_id=? AND s_file=?")->execute(array($gid, $ged_id));
				break;
			case 'OBJE':
				Database::prepare("DELETE FROM `##media` WHERE m_id=? AND m_file=?")->execute(array($gid, $ged_id));
				break;
			default:
				Database::prepare("DELETE FROM `##other` WHERE o_id=? AND o_file=?")->execute(array($gid, $ged_id));
				break;
		}

		if (!$delete) {
			self::importRecord($gedrec, Tree::FindById($ged_id), true);
		}
	}
}
