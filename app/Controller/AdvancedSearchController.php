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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Soundex;

/**
 * Controller for the advanced search page
 */
class AdvancedSearchController extends SearchController {
	/** @var string[] Fields to search */
	public $fields    = array();

	/** @var string[] Field values to search */
	public $values    = array();

	/** @var int[] Range of days either side of target date */
	public $plusminus = array();

	/**
	 * Startup activity
	 */
	public function __construct() {
		parent::__construct();

		$this->setPageTitle(I18N::translate('Advanced search'));

		if (empty($_REQUEST['action'])) {
			$this->action = "advanced";
		}
		if ($this->action == "advanced") {
			if (isset($_REQUEST['fields'])) {
				$this->fields = $_REQUEST['fields'];
				ksort($this->fields);
			}
			if (isset($_REQUEST['values'])) {
				$this->values = $_REQUEST['values'];
			}
			if (isset($_REQUEST['plusminus'])) {
				$this->plusminus = $_REQUEST['plusminus'];
			}
			$this->reorderFields();
			$this->advancedSearch();
		}
		if (!$this->fields) {
			$this->fields = array(
				'NAME:GIVN:SDX',
				'NAME:SURN:SDX',
				'BIRT:DATE',
				'BIRT:PLAC',
				'FAMS:MARR:DATE',
				'FAMS:MARR:PLAC',
				'DEAT:DATE',
				'DEAT:PLAC',
				'FAMC:HUSB:NAME:GIVN:SDX',
				'FAMC:HUSB:NAME:SURN:SDX',
				'FAMC:WIFE:NAME:GIVN:SDX',
				'FAMC:WIFE:NAME:SURN:SDX',
			);
		}
	}

	/**
	 * A list of additional fields that can be added.
	 *
	 * @return string[]
	 */
	public function getOtherFields() {
		global $WT_TREE;

		$ofields = array(
			'ADDR', 'ADDR:CITY', 'ADDR:STAE', 'ADDR:CTRY', 'ADDR:POST',
			'ADOP:DATE', 'ADOP:PLAC',
			'AFN',
			'BAPL:DATE', 'BAPL:PLAC',
			'BAPM:DATE', 'BAPM:PLAC',
			'BARM:DATE', 'BARM:PLAC',
			'BASM:DATE', 'BASM:PLAC',
			'BLES:DATE', 'BLES:PLAC',
			'BURI:DATE', 'BURI:PLAC',
			'CAST',
			'CENS:DATE', 'CENS:PLAC',
			'CHAN:DATE', 'CHAN:_WT_USER',
			'CHR:DATE', 'CHR:PLAC',
			'CREM:DATE', 'CREM:PLAC',
			'DSCR',
			'EMAIL',
			'EMIG:DATE', 'EMIG:PLAC',
			'ENDL:DATE', 'ENDL:PLAC',
			'EVEN',
			'EVEN:DATE', 'EVEN:PLAC',
			'FAMS:CENS:DATE', 'FAMS:CENS:PLAC',
			'FAMS:DIV:DATE',
			'FAMS:NOTE',
			'FAMS:SLGS:DATE', 'FAMS:SLGS:PLAC',
			'FAX',
			'FCOM:DATE', 'FCOM:PLAC',
			'IMMI:DATE', 'IMMI:PLAC',
			'NAME:NICK', 'NAME:_MARNM', 'NAME:_HEB', 'NAME:ROMN',
			'NATI',
			'NATU:DATE', 'NATU:PLAC',
			'NOTE',
			'OCCU',
			'ORDN:DATE', 'ORDN:PLAC',
			'RELI',
			'RESI', 'RESI:DATE', 'RESI:PLAC',
			'SLGC:DATE', 'SLGC:PLAC',
			'TITL',
			'_BRTM:DATE', '_BRTM:PLAC',
			'_MILI',
		);
		// Allow (some of) the user-specified fields to be selected
		preg_match_all('/(' . WT_REGEX_TAG . ')/', $WT_TREE->getPreference('INDI_FACTS_ADD'), $facts);
		foreach ($facts[1] as $fact) {
			if (
				$fact !== 'BIRT' &&
				$fact !== 'DEAT' &&
				$fact !== 'ASSO' &&
				!in_array($fact, $ofields) &&
				!in_array("{$fact}:DATE", $ofields) &&
				!in_array("{$fact}:PLAC", $ofields)
			) {
				$ofields[] = $fact;
			}
		}
		$fields = array();
		foreach ($ofields as $field) {
			$fields[$field] = strip_tags(GedcomTag::GetLabel($field)); // Custom tags have error markup
		}
		uksort($fields, '\Fisharebest\Webtrees\Controller\AdvancedSearchController::tagSort');

		return $fields;
	}

	/**
	 * Compare two tags, for sorting
	 *
	 * @param string $x
	 * @param string $y
	 *
	 * @return int
	 */
	public static function tagSort($x, $y) {
		list($x1) = explode(':', $x . ':');
		list($y1) = explode(':', $y . ':');
		$tmp      = I18N::strcasecmp(GedcomTag::getLabel($x1), GedcomTag::getLabel($y1));
		if ($tmp) {
			return $tmp;
		} else {
			return I18N::strcasecmp(GedcomTag::getLabel($x), GedcomTag::getLabel($y));
		}
	}

	/**
	 * Get the value.
	 *
	 * @param int $i
	 *
	 * @return string
	 */
	public function getValue($i) {
		$val = '';
		if (isset($this->values[$i])) {
			$val = $this->values[$i];
		}

		return $val;
	}

	/**
	 * Get the field.
	 *
	 * @param int $i
	 *
	 * @return string
	 */
	public function getField($i) {
		$val = '';
		if (isset($this->fields[$i])) {
			$val = htmlentities($this->fields[$i]);
		}

		return $val;
	}

	/**
	 * Get the index.
	 *
	 * @param string $field
	 *
	 * @return int
	 */
	public function getIndex($field) {
		return array_search($field, $this->fields);
	}

	/**
	 * Get the label.
	 *
	 * @param string $tag
	 *
	 * @return string
	 */
	public function getLabel($tag) {
		return GedcomTag::getLabel(preg_replace('/:(SDX|BEGINS|EXACT|CONTAINS)$/', '', $tag));
	}

	/**
	 * Set the field order
	 */
	private function reorderFields() {
		$i         = 0;
		$newfields = array();
		$newvalues = array();
		$newplus   = array();
		$rels      = array();
		foreach ($this->fields as $j => $field) {
			if (strpos($this->fields[$j], "FAMC:HUSB:NAME") === 0 || strpos($this->fields[$j], "FAMC:WIFE:NAME") === 0) {
				$rels[$this->fields[$j]] = $this->values[$j];
				continue;
			}
			$newfields[$i] = $this->fields[$j];
			if (isset($this->values[$j])) {
				$newvalues[$i] = $this->values[$j];
			}
			if (isset($this->plusminus[$j])) {
				$newplus[$i] = $this->plusminus[$j];
			}
			$i++;
		}
		$this->fields    = $newfields;
		$this->values    = $newvalues;
		$this->plusminus = $newplus;
		foreach ($rels as $field => $value) {
			$this->fields[] = $field;
			$this->values[] = $value;
		}
	}

	/**
	 * Perform the search
	 */
	private function advancedSearch() {
		global $WT_TREE;

		$this->myindilist = array();
		$fct              = count($this->fields);
		if (!array_filter($this->values)) {
			return;
		}

		// Dynamic SQL query, plus bind variables
		$sql  = 'SELECT DISTINCT ind.i_id AS xref, ind.i_gedcom AS gedcom FROM `##individuals` ind';
		$bind = array();

		// Join the following tables
		$father_name     = false;
		$mother_name     = false;
		$spouse_family   = false;
		$indi_name       = false;
		$indi_date       = false;
		$fam_date        = false;
		$indi_plac       = false;
		$fam_plac        = false;
		foreach ($this->fields as $n => $field) {
			if ($this->values[$n]) {
				if (substr($field, 0, 14) == 'FAMC:HUSB:NAME') {
					$father_name = true;
				} elseif (substr($field, 0, 14) == 'FAMC:WIFE:NAME') {
					$mother_name = true;
				} elseif (substr($field, 0, 4) == 'NAME') {
					$indi_name = true;
				} elseif (strpos($field, ':DATE') !== false) {
					if (substr($field, 0, 4) == 'FAMS') {
						$fam_date      = true;
						$spouse_family = true;
					} else {
						$indi_date = true;
					}
				} elseif (strpos($field, ':PLAC') !== false) {
					if (substr($field, 0, 4) == 'FAMS') {
						$fam_plac      = true;
						$spouse_family = true;
					} else {
						$indi_plac = true;
					}
				} elseif ($field == 'FAMS:NOTE') {
					$spouse_family = true;
				}
			}
		}

		if ($father_name || $mother_name) {
			$sql .= " JOIN `##link`   l_1 ON (l_1.l_file=ind.i_file AND l_1.l_from=ind.i_id AND l_1.l_type='FAMC')";
		}
		if ($father_name) {
			$sql .= " JOIN `##link`   l_2 ON (l_2.l_file=ind.i_file AND l_2.l_from=l_1.l_to AND l_2.l_type='HUSB')";
			$sql .= " JOIN `##name`   f_n ON (f_n.n_file=ind.i_file AND f_n.n_id  =l_2.l_to)";
		}
		if ($mother_name) {
			$sql .= " JOIN `##link`   l_3 ON (l_3.l_file=ind.i_file AND l_3.l_from=l_1.l_to AND l_3.l_type='WIFE')";
			$sql .= " JOIN `##name`   m_n ON (m_n.n_file=ind.i_file AND m_n.n_id  =l_3.l_to)";
		}
		if ($spouse_family) {
			$sql .= " JOIN `##link`     l_4 ON (l_4.l_file=ind.i_file AND l_4.l_from=ind.i_id AND l_4.l_type='FAMS')";
			$sql .= " JOIN `##families` fam ON (fam.f_file=ind.i_file AND fam.f_id  =l_4.l_to)";
		}
		if ($indi_name) {
			$sql .= " JOIN `##name`   i_n ON (i_n.n_file=ind.i_file AND i_n.n_id=ind.i_id)";
		}
		if ($indi_date) {
			$sql .= " JOIN `##dates`  i_d ON (i_d.d_file=ind.i_file AND i_d.d_gid=ind.i_id)";
		}
		if ($fam_date) {
			$sql .= " JOIN `##dates`  f_d ON (f_d.d_file=ind.i_file AND f_d.d_gid=fam.f_id)";
		}
		if ($indi_plac) {
			$sql .= " JOIN `##placelinks`   i_pl ON (i_pl.pl_file=ind.i_file AND i_pl.pl_gid =ind.i_id)";
			$sql .= " JOIN (" .
					"SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) AS place, p1.p_id AS id, p1.p_file AS file" .
					" FROM      `##places` AS p1" .
					" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)" .
					" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)" .
					" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)" .
					" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)" .
					" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)" .
					" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)" .
					" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)" .
					" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)" .
					") AS i_p ON (i_p.file  =ind.i_file AND i_pl.pl_p_id= i_p.id)";
		}
		if ($fam_plac) {
			$sql .= " JOIN `##placelinks`   f_pl ON (f_pl.pl_file=ind.i_file AND f_pl.pl_gid =fam.f_id)";
			$sql .= " JOIN (" .
					"SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) AS place, p1.p_id AS id, p1.p_file AS file" .
					" FROM      `##places` AS p1" .
					" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)" .
					" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)" .
					" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)" .
					" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)" .
					" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)" .
					" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)" .
					" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)" .
					" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)" .
					") AS f_p ON (f_p.file  =ind.i_file AND f_pl.pl_p_id= f_p.id)";
		}
		// Add the where clause
		$sql .= " WHERE ind.i_file=?";
		$bind[] = $WT_TREE->getTreeId();
		for ($i = 0; $i < $fct; $i++) {
			$field = $this->fields[$i];
			$value = $this->values[$i];
			if ($value === '') {
				continue;
			}
			$parts = preg_split("/:/", $field . '::::');
			if ($parts[0] == 'NAME') {
				// NAME:*
				switch ($parts[1]) {
				case 'GIVN':
					switch ($parts[2]) {
					case 'EXACT':
						$sql .= " AND i_n.n_givn=?";
						$bind[] = $value;
						break;
					case 'BEGINS':
						$sql .= " AND i_n.n_givn LIKE CONCAT(?, '%')";
						$bind[] = $value;
						break;
					case 'CONTAINS':
						$sql .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
						$bind[] = $value;
						break;
					case 'SDX_STD':
						$sdx = Soundex::russell($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "i_n.n_soundex_givn_std LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					case 'SDX': // SDX uses DM by default.
					case 'SDX_DM':
						$sdx = Soundex::daitchMokotoff($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "i_n.n_soundex_givn_dm LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					}
					break;
				case 'SURN':
					switch ($parts[2]) {
					case 'EXACT':
						$sql .= " AND i_n.n_surname=?";
						$bind[] = $value;
						break;
					case 'BEGINS':
						$sql .= " AND i_n.n_surname LIKE CONCAT(?, '%')";
						$bind[] = $value;
						break;
					case 'CONTAINS':
						$sql .= " AND i_n.n_surname LIKE CONCAT('%', ?, '%')";
						$bind[] = $value;
						break;
					case 'SDX_STD':
						$sdx = Soundex::russell($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "i_n.n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= " AND (" . implode(' OR ', $sdx) . ")";
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND i_n.n_surn LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					case 'SDX': // SDX uses DM by default.
					case 'SDX_DM':
						$sdx = Soundex::daitchMokotoff($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "i_n.n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= " AND (" . implode(' OR ', $sdx) . ")";
							break;
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND i_n.n_surn LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
					}
					break;
				case 'NICK':
				case '_MARNM':
				case '_HEB':
				case '_AKA':
					$sql .= " AND i_n.n_type=? AND i_n.n_full LIKE CONCAT('%', ?, '%')";
					$bind[] = $parts[1];
					$bind[] = $value;
					break;
				}
			} elseif ($parts[1] == 'DATE') {
				// *:DATE
				$date = new Date($value);
				if ($date->isOK()) {
					$jd1 = $date->minimumJulianDay();
					$jd2 = $date->maximumJulianDay();
					if (!empty($this->plusminus[$i])) {
						$adjd = $this->plusminus[$i] * 365;
						$jd1 -= $adjd;
						$jd2 += $adjd;
					}
					$sql .= " AND i_d.d_fact=? AND i_d.d_julianday1>=? AND i_d.d_julianday2<=?";
					$bind[] = $parts[0];
					$bind[] = $jd1;
					$bind[] = $jd2;
				}
			} elseif ($parts[0] == 'FAMS' && $parts[2] == 'DATE') {
				// FAMS:*:DATE
				$date = new Date($value);
				if ($date->isOK()) {
					$jd1 = $date->minimumJulianDay();
					$jd2 = $date->maximumJulianDay();
					if (!empty($this->plusminus[$i])) {
						$adjd = $this->plusminus[$i] * 365;
						$jd1 -= $adjd;
						$jd2 += $adjd;
					}
					$sql .= " AND f_d.d_fact=? AND f_d.d_julianday1>=? AND f_d.d_julianday2<=?";
					$bind[] = $parts[1];
					$bind[] = $jd1;
					$bind[] = $jd2;
				}
			} elseif ($parts[1] == 'PLAC') {
				// *:PLAC
				// SQL can only link a place to a person/family, not to an event.
				$sql .= " AND i_p.place LIKE CONCAT('%', ?, '%')";
				$bind[] = $value;
			} elseif ($parts[0] == 'FAMS' && $parts[2] == 'PLAC') {
				// FAMS:*:PLAC
				// SQL can only link a place to a person/family, not to an event.
				$sql .= " AND f_p.place LIKE CONCAT('%', ?, '%')";
				$bind[] = $value;
			} elseif ($parts[0] == 'FAMC' && $parts[2] == 'NAME') {
				$table = $parts[1] == 'HUSB' ? 'f_n' : 'm_n';
				// NAME:*
				switch ($parts[3]) {
				case 'GIVN':
					switch ($parts[4]) {
					case 'EXACT':
						$sql .= " AND {$table}.n_givn=?";
						$bind[] = $value;
						break;
					case 'BEGINS':
						$sql .= " AND {$table}.n_givn LIKE CONCAT(?, '%')";
						$bind[] = $value;
						break;
					case 'CONTAINS':
						$sql .= " AND {$table}.n_givn LIKE CONCAT('%', ?, '%')";
						$bind[] = $value;
						break;
					case 'SDX_STD':
						$sdx = Soundex::russell($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "{$table}.n_soundex_givn_std LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND {$table}.n_givn = LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					case 'SDX': // SDX uses DM by default.
					case 'SDX_DM':
						$sdx = Soundex::daitchMokotoff($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "{$table}.n_soundex_givn_dm LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
							break;
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND {$table}.n_givn = LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
					}
					break;
				case 'SURN':
					switch ($parts[4]) {
					case 'EXACT':
						$sql .= " AND {$table}.n_surname=?";
						$bind[] = $value;
						break;
					case 'BEGINS':
						$sql .= " AND {$table}.n_surname LIKE CONCAT(?, '%')";
						$bind[] = $value;
						break;
					case 'CONTAINS':
						$sql .= " AND {$table}.n_surname LIKE CONCAT('%', ?, '%')";
						$bind[] = $value;
						break;
					case 'SDX_STD':
						$sdx = Soundex::russell($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "{$table}.n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND {$table}.n_surn = LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					case 'SDX': // SDX uses DM by default.
					case 'SDX_DM':
						$sdx = Soundex::daitchMokotoff($value);
						if ($sdx !== null) {
							$sdx = explode(':', $sdx);
							foreach ($sdx as $k => $v) {
								$sdx[$k] = "{$table}.n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
								$bind[]  = $v;
							}
							$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
						} else {
							// No phonetic content?  Use a substring match
							$sql .= " AND {$table}.n_surn = LIKE CONCAT('%', ?, '%')";
							$bind[] = $value;
						}
						break;
					}
					break;
				}
			} elseif ($parts[0] == 'FAMS') {
				// e.g. searches for occupation, religion, note, etc.
				$sql .= " AND fam.f_gedcom REGEXP CONCAT('\n[0-9] ', ?, '(.*\n[0-9] CONT)* [^\n]*', ?)";
				$bind[] = $parts[1];
				$bind[] = $value;
			} else {
				// e.g. searches for occupation, religion, note, etc.
				$sql .= " AND ind.i_gedcom REGEXP CONCAT('\n[0-9] ', ?, '(.*\n[0-9] CONT)* [^\n]*', ?)";
				$bind[] = $parts[0];
				$bind[] = $value;
			}
		}
		$rows = Database::prepare($sql)->execute($bind)->fetchAll();
		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
			// Check for XXXX:PLAC fields, which were only partially matched by SQL
			foreach ($this->fields as $n => $field) {
				if ($this->values[$n] && preg_match('/^(' . WT_REGEX_TAG . '):PLAC$/', $field, $match)) {
					if (!preg_match('/\n1 ' . $match[1] . '(\n[2-9].*)*\n2 PLAC .*' . preg_quote($this->values[$n], '/') . '/i', $person->getGedcom())) {
						continue 2;
				 }
				}
			}
			$this->myindilist[] = $person;
		}
	}

	/**
	 * Display the search results
	 */
	public function printResults() {
		if ($this->myindilist) {
			uasort($this->myindilist, '\Fisharebest\Webtrees\GedcomRecord::compare');
			echo FunctionsPrintLists::individualTable($this->myindilist);
		} elseif (array_filter($this->values)) {
			echo '<p class="ui-state-highlight">', I18N::translate('No results found.'), '</p>';
		}
	}
}
