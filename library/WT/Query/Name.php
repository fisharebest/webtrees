<?php
/**
 * Class WT_Query_Name - generate lists for indilist.php and famlist.php
 *
 * @package   webtrees
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */
class WT_Query_Name {
	/**
	 * Get a list of initial letters, for lists of names.
	 *
	 * @param string $locale Return the alphabet for this locale
	 *
	 * @return string[]
	 */
	private static function getAlphabetForLocale($locale) {
		switch ($locale) {
		case 'ar':
			return array(
				'ا','ب','ت','ث','ج','ح','خ','د','ذ','ر','ز','س','ش','ص','ض','ط','ظ','ع','غ','ف','ق','ك','ل','م','ن','ه','و','ي','آ','ة','ى','ی'
			);
		case 'cs':
			return array(
				'A','B','C','D','E','F','G','H','CH','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
			);
		case 'da':
		case 'nb':
		case 'nn':
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Æ','Ø','Å'
			);
		case 'el':
			return array(
				'Α','Β','Γ','Δ','Ε','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω'
			);
		case 'es':
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V','W','X','Y','Z'
			);
		case 'et':
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','Š','Z','Ž','T','U','V','W','Õ','Ä','Ö','Ü','X','Y'
			);
		case 'fi':
		case 'sv':
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','Å','Ä','Ö'
			);
		case 'he':
			return array(
				'א','ב','ג','ד','ה','ו','ז','ח','ט','י','כ','ל','מ','נ','ס','ע','פ','צ','ק','ר','ש','ת'
			);
		case 'hu':
			return array(
				'A','B','C','CS','D','DZ','DZS','E','F','G','GY','H','I','J','K','L','LY','M','N','NY','O','Ö','P','Q','R','S','SZ','T','TY','U','Ü','V','W','X','Y','Z','ZS'
			);
		case 'lt':
			return array(
				'A','Ą','B','C','Č','D','E','Ę','Ė','F','G','H','I','Y','Į','J','K','L','M','N','O','P','R','S','Š','T','U','Ų','Ū','V','Z','Ž'
			);
		case 'nl':
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','IJ'
			);
		case 'pl':
			return array(
				'A','B','C','Ć','D','E','F','G','H','I','J','K','L','Ł','M','N','O','Ó','P','Q','R','S','Ś','T','U','V','W','X','Y','Z','Ź','Ż'
			);
		case 'ro':
			return array(
				'A','Ă','Â','B','C','D','E','F','G','H','I','Î','J','K','L','M','N','O','P','Q','R','S','Ş','T','Ţ','U','V','W','X','Y','Z'
			);
		case 'ru':
			return array(
				'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
			);
		case 'sk':
			return array(
				'A','Á','Ä','B','C','Č','D','Ď','E','É','F','G','H','I','Í','J','K','L','Ľ','Ĺ','M','N','Ň','O','Ó','Ô','P','Q','R','Ŕ','S','Š','T','Ť','U','Ú','V','W','X','Y','Ý','Z','Ž'
			);
		case 'sl':
			return array(
				'A','B','C','Č','Ć','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','Š','T','U','V','W','X','Y','Z','Ž'
			);
		case 'sr':
			return array(
				'A','B','C','Č','Ć','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','Š','T','U','V','W','X','Y','Z','Ž'
			);
		case 'tr':
			return array(
				'A','B','C','Ç','D','E','F','G','Ğ','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z'
			);
		default:
			return array(
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
			);
		}
	}

	/**
	 * Get the initial letter of a name, taking care of multi-letter sequences and equivalences.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	static public function initialLetter($name) {
		$name = WT_I18N::strtoupper($name);
		switch (WT_LOCALE) {
		case 'cs':
			if (substr($name, 0, 2) == 'CH') {
				return 'CH';
			}
			break;
		case 'da':
		case 'nb':
		case 'nn':
			if (substr($name, 0, 2) == 'AA') {
				return 'Å';
			}
			break;
		case 'hu':
			if (substr($name, 0, 2) == 'CS') {
				return 'CS';
			} elseif (substr($name, 0, 3) == 'DZS') {
				return 'DZS';
			} elseif (substr($name, 0, 2) == 'DZ') {
				return 'DZ';
			} elseif (substr($name, 0, 2) == 'GY') {
				return 'GY';
			} elseif (substr($name, 0, 2) == 'LY') {
				return 'LY';
			} elseif (substr($name, 0, 2) == 'NY') {
				return 'NY';
			} elseif (substr($name, 0, 2) == 'SZ') {
				return 'SZ';
			} elseif (substr($name, 0, 2) == 'TY') {
				return 'TY';
			} elseif (substr($name, 0, 2) == 'ZS') {
				return 'ZS';
			}
			break;
		case 'nl':
			if (substr($name, 0, 2) == 'IJ') {
				return 'IJ';
			}
			break;
		}
		// No special rules - just take the first character
		return mb_substr($name, 0, 1);
	}

	/**
	 * Generate SQL to match a given letter, taking care of cases that
	 * are not covered by the collation setting.
	 *
	 * We must consider:
	 * potential substrings, such as Czech "CH" and "C"
	 * equivalent letters, such as Danish "AA" and "Å"
	 *
	 * We COULD write something that handles all languages generically,
	 * but its performance would most likely be poor.
	 *
	 * For languages that don't appear in this list, we could write
	 * simpler versions of the surnameAlpha() and givenAlpha() functions,
	 * but it gives no noticable improvement in performance.
	 *
	 * @param string $field
	 * @param string $letter
	 *
	 * @return string
	 */
	static private function getInitialSql($field, $letter) {
		switch (WT_LOCALE) {
		case 'cs':
			switch ($letter) {
			case 'C': return $field." LIKE 'C%' COLLATE ".WT_I18N::$collation." AND ".$field." NOT LIKE 'CH%' COLLATE ".WT_I18N::$collation;
			}
			break;
		case 'da':
		case 'nb':
		case 'nn':
			switch ($letter) {
			// AA gets listed under Å
			case 'A': return $field." LIKE 'A%' COLLATE ".WT_I18N::$collation." AND ".$field." NOT LIKE 'AA%' COLLATE ".WT_I18N::$collation;
			case 'Å': return "(".$field." LIKE 'Å%' COLLATE ".WT_I18N::$collation." OR ".$field." LIKE 'AA%' COLLATE ".WT_I18N::$collation.")";
			}
			break;
		case 'hu':
			switch ($letter) {
			case 'C':  return $field." LIKE 'C%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'CS%' COLLATE ". WT_I18N::$collation;
			case 'D':  return $field." LIKE 'D%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'DZ%' COLLATE ". WT_I18N::$collation;
			case 'DZ': return $field." LIKE 'DZ%' COLLATE ".WT_I18N::$collation." AND ".$field." NOT LIKE 'DZS%' COLLATE ".WT_I18N::$collation;
			case 'G':  return $field." LIKE 'G%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'GY%' COLLATE ". WT_I18N::$collation;
			case 'L':  return $field." LIKE 'L%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'LY%' COLLATE ". WT_I18N::$collation;
			case 'N':  return $field." LIKE 'N%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'NY%' COLLATE ". WT_I18N::$collation;
			case 'S':  return $field." LIKE 'S%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'SZ%' COLLATE ". WT_I18N::$collation;
			case 'T':  return $field." LIKE 'T%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'TY%' COLLATE ". WT_I18N::$collation;
			case 'Z':  return $field." LIKE 'Z%' COLLATE ". WT_I18N::$collation." AND ".$field." NOT LIKE 'ZS%' COLLATE ". WT_I18N::$collation;
			}
			break;
		case 'nl':
			switch ($letter) {
			case 'I': return $field." LIKE 'I%' COLLATE ".WT_I18N::$collation." AND ".$field." NOT LIKE 'IJ%' COLLATE ".WT_I18N::$collation;
			}
			break;
		}
		// Easy cases: the MySQL collation rules take care of it
		return "$field LIKE CONCAT('@',".WT_DB::quote($letter).",'%') COLLATE ".WT_I18N::$collation." ESCAPE '@'";
	}

	/**
	 * Get a list of initial surname letters for indilist.php and famlist.php
	 *
	 * @param boolean $marnm   if set, include married names
	 * @param boolean $fams    if set, only consider individuals with FAMS records
	 * @param integer $ged_id  only consider individuals from this tree
	 * @param boolean $totals  if set, count the number of names beginning with each letter
	 *
	 * @return integer[]
	 */
	public static function surnameAlpha($marnm, $fams, $ged_id, $totals = true) {
		$alphas = array();

		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file={$ged_id}" .
			($marnm ? "" : " AND n_type!='_MARNM'");

		// Fetch all the letters in our alphabet, whether or not there
		// are any names beginning with that letter.  It looks better to
		// show the full alphabet, rather than omitting rare letters such as X
		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$count = 1;
			if ($totals) {
				$count = WT_DB::prepare($sql . " AND " . self::getInitialSql('n_surn', $letter))->fetchOne();
			}
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname.
		$sql =
			"SELECT SQL_CACHE UPPER(LEFT(n_surn, 1)), COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file={$ged_id} AND n_surn<>''" .
			($marnm ? "" : " AND n_type!='_MARNM'");

		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$sql .= " AND n_surn NOT LIKE '" . $letter . "%' COLLATE " . WT_I18N::$collation;
		}
		$sql .= " GROUP BY LEFT(n_surn, 1) ORDER BY LEFT(n_surn, 1)='', LEFT(n_surn, 1)='@', LEFT(n_surn, 1)";
		foreach (WT_DB::prepare($sql)->fetchAssoc() as $alpha=>$count) {
			$alphas[$alpha] = $count;
		}

		// Names with no surname
		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file={$ged_id} AND n_surn=''" .
			($marnm ? "" : " AND n_type!='_MARNM'");
		$num_none = WT_DB::prepare($sql)->fetchOne();
		if ($num_none) {
			// Special code to indicate "no surname"
			$alphas[','] = $num_none;
		}

		return $alphas;
	}

	/**
	 * Get a list of initial given name letters for indilist.php and famlist.php
	 *
	 * @param string  $surn   if set, only consider people with this surname
	 * @param string  $salpha if set, only consider surnames starting with this letter
	 * @param boolean $marnm  if set, include married names
	 * @param boolean $fams   if set, only consider individuals with FAMS records
	 * @param integer $ged_id only consider individuals from this tree
	 *
	 * @return integer[]
	 */
	public static function givenAlpha($surn, $salpha, $marnm, $fams, $ged_id) {
		$alphas=array();

		$sql =
			"SELECT SQL_CACHE COUNT(DISTINCT n_id)" .
			" FROM `##name`" .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file={$ged_id} " .
			($marnm ? "" : " AND n_type!='_MARNM'");

		if ($surn) {
			$sql .= " AND n_surn=" . WT_DB::quote($surn) . " COLLATE '" . WT_I18N::$collation . "'";
		} elseif ($salpha==',') {
			$sql .= " AND n_surn=''";
		} elseif ($salpha=='@') {
			$sql .= " AND n_surn='@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}

		// Fetch all the letters in our alphabet, whether or not there
		// are any names beginning with that letter.  It looks better to
		// show the full alphabet, rather than omitting rare letters such as X
		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$count=WT_DB::prepare($sql . " AND " . self::getInitialSql('n_givn', $letter))->fetchOne();
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname
		$sql =
			"SELECT SQL_CACHE UPPER(LEFT(n_givn, 1)), COUNT(DISTINCT n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file={$ged_id} " .
			($marnm ? "" : " AND n_type!='_MARNM'");

		if ($surn) {
			$sql .= " AND n_surn=" . WT_DB::quote($surn) . " COLLATE '" . WT_I18N::$collation . "'";
		} elseif ($salpha==',') {
			$sql .= " AND n_surn=''";
		} elseif ($salpha=='@') {
			$sql .= " AND n_surn='@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}

		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$sql .= " AND n_givn NOT LIKE '" . $letter . "%' COLLATE " . WT_I18N::$collation;
		}
		$sql .= " GROUP BY LEFT(n_givn, 1) ORDER BY LEFT(n_givn, 1)='@', LEFT(n_givn, 1)='', LEFT(n_givn, 1)";
		foreach (WT_DB::prepare($sql)->fetchAssoc() as $alpha=>$count) {
			$alphas[$alpha] = $count;
		}

		return $alphas;
	}

	/**
	 * Get a list of actual surnames and variants, based on a "root" surname.
	 *
	 * @param string  $surn   if set, only fetch people with this surname
	 * @param string  $salpha if set, only consider surnames starting with this letter
	 * @param boolean $marnm  if set, include married names
	 * @param boolean $fams   if set, only consider individuals with FAMS records
	 * @param integer $ged_id only consider individuals from this gedcom
	 *
	 * @return array
	 */
	public static function surnames($surn, $salpha, $marnm, $fams, $ged_id) {
		$sql=
			"SELECT SQL_CACHE n2.n_surn, n1.n_surname, n1.n_id".
			" FROM `##name` n1 ".
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "").
			" JOIN (SELECT n_surn, n_file FROM `##name`".
			" WHERE n_file={$ged_id}".
			($marnm ? "" : " AND n_type!='_MARNM'");

		if ($surn) {
			$sql.=" AND n_surn COLLATE '".WT_I18N::$collation."' =".WT_DB::quote($surn);
		} elseif ($salpha==',') {
			$sql.=" AND n_surn=''";
		} elseif ($salpha=='@') {
			$sql.=" AND n_surn='@N.N.'";
		} elseif ($salpha) {
			$sql.=" AND ".self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql.=" AND n_surn NOT IN ('', '@N.N.')";
		}
		$sql.=" GROUP BY n_surn COLLATE '".WT_I18N::$collation."', n_file) n2 ON (n1.n_surn=n2.n_surn COLLATE '".WT_I18N::$collation."' AND n1.n_file=n2.n_file)";
		if (!$marnm) {
			$sql.=" AND n_type!='_MARNM'";
		}

		$list=array();
		foreach (WT_DB::prepare($sql)->fetchAll() as $row) {
			$list[WT_I18N::strtoupper($row->n_surn)][$row->n_surname][$row->n_id]=true;
		}
		return $list;
	}

	/**
	 * Fetch a list of individuals with specified names
	 *
	 * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
	 * To search for names with no surnames, use $salpha=","
	 *
	 * @param string  $surn   if set, only fetch people with this surname
	 * @param string  $salpha if set, only fetch surnames starting with this letter
	 * @param string  $galpha if set, only fetch given names starting with this letter
	 * @param boolean $marnm  if set, include married names
	 * @param boolean $fams   if set, only fetch individuals with FAMS records
	 * @param integer $ged_id if set, only fetch individuals from this gedcom
	 *
	 * @return WT_Individual[]
	 */
	public static function individuals($surn, $salpha, $galpha, $marnm, $fams, $ged_id) {
		$sql=
			"SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom, n_full " .
			"FROM `##individuals` " .
			"JOIN `##name` ON (n_id=i_id AND n_file=i_file) " .
			($fams ? "JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			"WHERE n_file={$ged_id} " .
			($marnm ? "" : "AND n_type!='_MARNM'");

		if ($surn) {
			$sql .= " AND n_surn COLLATE '" . WT_I18N::$collation . "'=" . WT_DB::quote($surn);
		} elseif ($salpha==',') {
			$sql .= " AND n_surn=''";
		} elseif ($salpha=='@') {
			$sql .= " AND n_surn='@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND ".self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}
		if ($galpha) {
			$sql .= " AND " . self::getInitialSql('n_givn', $galpha);
		}

		$sql .= " ORDER BY CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END, n_surn COLLATE '" . WT_I18N::$collation . "', CASE n_givn WHEN '@P.N.' THEN 1 ELSE 0 END, n_givn COLLATE '" . WT_I18N::$collation . "'";

		$list = array();
		$rows = WT_DB::prepare($sql)->fetchAll();
		foreach ($rows as $row) {
			$person = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			// The name from the database may be private - check the filtered list...
			foreach ($person->getAllNames() as $n=>$name) {
				if ($name['fullNN'] == $row->n_full) {
					$person->setPrimaryName($n);
					// We need to clone $person, as we may have multiple references to the
					// same person in this list, and the "primary name" would otherwise
					// be shared amongst all of them.
					$list[] = clone $person;
					break;
				}
			}
		}
		return $list;
	}

	/**
	 * Fetch a list of families with specified names
	 *
	 * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
	 * To search for names with no surnames, use $salpha=","
	 *
	 * @param string  $surn   if set, only fetch people with this surname
	 * @param string  $salpha if set, only fetch surnames starting with this letter
	 * @param string  $galpha if set, only fetch given names starting with this letter
	 * @param boolean $marnm  if set, include married names
	 * @param integer $ged_id if set, only fetch individuals from this gedcom
	 *
	 * @return WT_Family[]
	 */
	public static function families($surn, $salpha, $galpha, $marnm, $ged_id) {
		$list=array();
		foreach (self::individuals($surn, $salpha, $galpha, $marnm, true, $ged_id) as $indi) {
			foreach ($indi->getSpouseFamilies() as $family) {
				$list[$family->getXref()]=$family;
			}
		}
		usort($list, array('WT_GedcomRecord', 'compare'));
		return $list;
	}
}
