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
namespace Fisharebest\Webtrees\Query;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;

/**
 * Generate lists for indilist.php and famlist.php
 */
class QueryName {
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
				'ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي', 'آ', 'ة', 'ى', 'ی',
			);
		case 'cs':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'CH', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			);
		case 'da':
		case 'nb':
		case 'nn':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Æ', 'Ø', 'Å',
			);
		case 'el':
			return array(
				'Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω',
			);
		case 'es':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			);
		case 'et':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'Z', 'Ž', 'T', 'U', 'V', 'W', 'Õ', 'Ä', 'Ö', 'Ü', 'X', 'Y',
			);
		case 'fi':
		case 'sv':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Å', 'Ä', 'Ö',
			);
		case 'he':
			return array(
				'א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת',
			);
		case 'hu':
			return array(
				'A', 'B', 'C', 'CS', 'D', 'DZ', 'DZS', 'E', 'F', 'G', 'GY', 'H', 'I', 'J', 'K', 'L', 'LY', 'M', 'N', 'NY', 'O', 'Ö', 'P', 'Q', 'R', 'S', 'SZ', 'T', 'TY', 'U', 'Ü', 'V', 'W', 'X', 'Y', 'Z', 'ZS',
			);
		case 'lt':
			return array(
				'A', 'Ą', 'B', 'C', 'Č', 'D', 'E', 'Ę', 'Ė', 'F', 'G', 'H', 'I', 'Y', 'Į', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'Ų', 'Ū', 'V', 'Z', 'Ž',
			);
		case 'nl':
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'IJ',
			);
		case 'pl':
			return array(
				'A', 'B', 'C', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Ł', 'M', 'N', 'O', 'Ó', 'P', 'Q', 'R', 'S', 'Ś', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ź', 'Ż',
			);
		case 'ro':
			return array(
				'A', 'Ă', 'Â', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'Î', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Ş', 'T', 'Ţ', 'U', 'V', 'W', 'X', 'Y', 'Z',
			);
		case 'ru':
			return array(
				'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			);
		case 'sk':
			return array(
				'A', 'Á', 'Ä', 'B', 'C', 'Č', 'D', 'Ď', 'E', 'É', 'F', 'G', 'H', 'I', 'Í', 'J', 'K', 'L', 'Ľ', 'Ĺ', 'M', 'N', 'Ň', 'O', 'Ó', 'Ô', 'P', 'Q', 'R', 'Ŕ', 'S', 'Š', 'T', 'Ť', 'U', 'Ú', 'V', 'W', 'X', 'Y', 'Ý', 'Z', 'Ž',
			);
		case 'sl':
			return array(
				'A', 'B', 'C', 'Č', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž',
			);
		case 'sr':
			return array(
				'A', 'B', 'C', 'Č', 'Ć', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž',
			);
		case 'tr':
			return array(
				'A', 'B', 'C', 'Ç', 'D', 'E', 'F', 'G', 'Ğ', 'H', 'I', 'İ', 'J', 'K', 'L', 'M', 'N', 'O', 'Ö', 'P', 'R', 'S', 'Ş', 'T', 'U', 'Ü', 'V', 'Y', 'Z',
			);
		default:
			return array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
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
	public static function initialLetter($name) {
		$name = I18N::strtoupper($name);
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
	private static function getInitialSql($field, $letter) {
		switch (WT_LOCALE) {
		case 'cs':
			switch ($letter) {
			case 'C': return $field . " LIKE 'C%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'CH%' COLLATE " . I18N::collation();
			}
			break;
		case 'da':
		case 'nb':
		case 'nn':
			switch ($letter) {
			// AA gets listed under Å
			case 'A': return $field . " LIKE 'A%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'AA%' COLLATE " . I18N::collation();
			case 'Å': return "(" . $field . " LIKE 'Å%' COLLATE " . I18N::collation() . " OR " . $field . " LIKE 'AA%' COLLATE " . I18N::collation() . ")";
			}
			break;
		case 'hu':
			switch ($letter) {
			case 'C':  return $field . " LIKE 'C%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'CS%' COLLATE " . I18N::collation();
			case 'D':  return $field . " LIKE 'D%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'DZ%' COLLATE " . I18N::collation();
			case 'DZ': return $field . " LIKE 'DZ%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'DZS%' COLLATE " . I18N::collation();
			case 'G':  return $field . " LIKE 'G%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'GY%' COLLATE " . I18N::collation();
			case 'L':  return $field . " LIKE 'L%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'LY%' COLLATE " . I18N::collation();
			case 'N':  return $field . " LIKE 'N%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'NY%' COLLATE " . I18N::collation();
			case 'S':  return $field . " LIKE 'S%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'SZ%' COLLATE " . I18N::collation();
			case 'T':  return $field . " LIKE 'T%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'TY%' COLLATE " . I18N::collation();
			case 'Z':  return $field . " LIKE 'Z%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'ZS%' COLLATE " . I18N::collation();
			}
			break;
		case 'nl':
			switch ($letter) {
			case 'I': return $field . " LIKE 'I%' COLLATE " . I18N::collation() . " AND " . $field . " NOT LIKE 'IJ%' COLLATE " . I18N::collation();
			}
			break;
		}

		// Easy cases: the MySQL collation rules take care of it
		return "$field LIKE CONCAT('@'," . Database::quote($letter) . ",'%') COLLATE " . I18N::collation() . " ESCAPE '@'";
	}

	/**
	 * Get a list of initial surname letters for indilist.php and famlist.php
	 *
	 * @param Tree $tree   Find surnames from this tree
	 * @param bool $marnm  if set, include married names
	 * @param bool $fams   if set, only consider individuals with FAMS records
	 * @param bool $totals if set, count the number of names beginning with each letter
	 *
	 * @return int[]
	 */
	public static function surnameAlpha(Tree $tree, $marnm, $fams, $totals = true) {
		$alphas = array();

		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file=" . $tree->getTreeId() .
			($marnm ? "" : " AND n_type!='_MARNM'");

		// Fetch all the letters in our alphabet, whether or not there
		// are any names beginning with that letter.  It looks better to
		// show the full alphabet, rather than omitting rare letters such as X
		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$count = 1;
			if ($totals) {
				$count = Database::prepare($sql . " AND " . self::getInitialSql('n_surn', $letter))->fetchOne();
			}
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname.
		$sql =
			"SELECT SQL_CACHE initial, count FROM (SELECT UPPER(LEFT(n_surn, 1)) AS initial, COUNT(n_id) AS count" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" WHERE n_file = :tree_id AND n_surn <> ''" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = array(
			'tree_id' => $tree->getTreeId(),
		);

		foreach (self::getAlphabetForLocale(WT_LOCALE) as $n => $letter) {
			$sql .= " AND n_surn COLLATE :collate_" . $n . " NOT LIKE :letter_" . $n;
			$args['collate_' . $n] = I18N::collation();
			$args['letter_' . $n]  = $letter . '%';
		}
		$sql .= " GROUP BY UPPER(LEFT(n_surn, 1))) AS subquery ORDER BY initial = '', initial = '@', initial";
		foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
			$alphas[$alpha] = $count;
		}

		// Names with no surname
		$sql =
			"SELECT SQL_CACHE COUNT(n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" WHERE n_file = :tree_id AND n_surn = ''" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = array(
			'tree_id' => $tree->getTreeId(),
		);

		$num_none = Database::prepare($sql)->execute($args)->fetchOne();
		if ($num_none) {
			// Special code to indicate "no surname"
			$alphas[','] = $num_none;
		}

		return $alphas;
	}

	/**
	 * Get a list of initial given name letters for indilist.php and famlist.php
	 *
	 * @param Tree   $tree   Find names in this tree
	 * @param string $surn   if set, only consider people with this surname
	 * @param string $salpha if set, only consider surnames starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only consider individuals with FAMS records
	 *
	 * @return int[]
	 */
	public static function givenAlpha(Tree $tree, $surn, $salpha, $marnm, $fams) {
		$alphas = array();

		$sql =
			"SELECT SQL_CACHE COUNT(DISTINCT n_id)" .
			" FROM `##name`" .
			($fams ? " JOIN `##link` ON (n_id=l_from AND n_file=l_file AND l_type='FAMS') " : "") .
			" WHERE n_file=" . $tree->getTreeId() . " " .
			($marnm ? "" : " AND n_type!='_MARNM'");

		if ($surn) {
			$sql .= " AND n_surn=" . Database::quote($surn) . " COLLATE '" . I18N::collation() . "'";
		} elseif ($salpha == ',') {
			$sql .= " AND n_surn=''";
		} elseif ($salpha == '@') {
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
			$count           = Database::prepare($sql . " AND " . self::getInitialSql('n_givn', $letter))->fetchOne();
			$alphas[$letter] = $count;
		}

		// Now fetch initial letters that are not in our alphabet,
		// including "@" (for "@N.N.") and "" for no surname
		$sql =
			"SELECT SQL_CACHE UPPER(LEFT(n_givn, 1)), COUNT(DISTINCT n_id)" .
			" FROM `##name` " .
			($fams ? " JOIN `##link` ON (n_id = l_from AND n_file = l_file AND l_type = 'FAMS') " : "") .
			" WHERE n_file = :tree_id" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = array(
			'tree_id' => $tree->getTreeId(),
		);

		if ($surn) {
			$sql .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}

		foreach (self::getAlphabetForLocale(WT_LOCALE) as $letter) {
			$sql .= " AND n_givn NOT LIKE '" . $letter . "%' COLLATE " . I18N::collation();
		}
		$sql .= " GROUP BY LEFT(n_givn, 1) ORDER BY LEFT(n_givn, 1) = '@', LEFT(n_givn, 1) = '', LEFT(n_givn, 1)";

		foreach (Database::prepare($sql)->execute($args)->fetchAssoc() as $alpha => $count) {
			$alphas[$alpha] = $count;
		}

		return $alphas;
	}

	/**
	 * Get a list of actual surnames and variants, based on a "root" surname.
	 *
	 * @param Tree   $tree   only fetch individuals from this tree
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only consider surnames starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only consider individuals with FAMS records
	 *
	 * @return array
	 */
	public static function surnames(Tree $tree, $surn, $salpha, $marnm, $fams) {
		$sql =
			"SELECT SQL_CACHE n2.n_surn, n1.n_surname, n1.n_id" .
			" FROM `##name` n1 " .
			($fams ? " JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			" JOIN (SELECT n_surn COLLATE :collate_0 AS n_surn, n_file FROM `##name`" .
			" WHERE n_file = :tree_id" .
			($marnm ? "" : " AND n_type != '_MARNM'");

		$args = array(
			'tree_id'   => $tree->getTreeId(),
			'collate_0' => I18N::collation(),
		);

		if ($surn) {
			$sql .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}
		$sql .= " GROUP BY n_surn COLLATE :collate_2, n_file) AS n2 ON (n1.n_surn = n2.n_surn COLLATE :collate_3 AND n1.n_file = n2.n_file)";
		$args['collate_2'] = I18N::collation();
		$args['collate_3'] = I18N::collation();

		$list = array();
		foreach (Database::prepare($sql)->execute($args)->fetchAll() as $row) {
			$list[I18N::strtoupper($row->n_surn)][$row->n_surname][$row->n_id] = true;
		}

		return $list;
	}

	/**
	 * Fetch a list of individuals with specified names
	 *
	 * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
	 * To search for names with no surnames, use $salpha=","
	 *
	 * @param Tree   $tree   only fetch individuals from this tree
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only fetch surnames starting with this letter
	 * @param string  $galpha if set, only fetch given names starting with this letter
	 * @param bool   $marnm  if set, include married names
	 * @param bool   $fams   if set, only fetch individuals with FAMS records
	 *
	 * @return Individual[]
	 */
	public static function individuals(Tree $tree, $surn, $salpha, $galpha, $marnm, $fams) {
		$sql =
			"SELECT i_id AS xref, i_gedcom AS gedcom, n_full " .
			"FROM `##individuals` " .
			"JOIN `##name` ON n_id = i_id AND n_file = i_file " .
			($fams ? "JOIN `##link` ON n_id = l_from AND n_file = l_file AND l_type = 'FAMS' " : "") .
			"WHERE n_file = :tree_id " .
			($marnm ? "" : "AND n_type != '_MARNM'");

		$args = array(
			'tree_id' => $tree->getTreeId(),
		);

		if ($surn) {
			$sql .= " AND n_surn COLLATE :collate_1 = :surn";
			$args['collate_1'] = I18N::collation();
			$args['surn']      = $surn;
		} elseif ($salpha === ',') {
			$sql .= " AND n_surn = ''";
		} elseif ($salpha === '@') {
			$sql .= " AND n_surn = '@N.N.'";
		} elseif ($salpha) {
			$sql .= " AND " . self::getInitialSql('n_surn', $salpha);
		} else {
			// All surnames
			$sql .= " AND n_surn NOT IN ('', '@N.N.')";
		}
		if ($galpha) {
			$sql .= " AND " . self::getInitialSql('n_givn', $galpha);
		}

		$sql .= " ORDER BY CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END, n_surn COLLATE :collate_2, CASE n_givn WHEN '@P.N.' THEN 1 ELSE 0 END, n_givn COLLATE :collate_3";
		$args['collate_2'] = I18N::collation();
		$args['collate_3'] = I18N::collation();

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $tree, $row->gedcom);
			// The name from the database may be private - check the filtered list...
			foreach ($person->getAllNames() as $n => $name) {
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
	 * @param Tree   $tree   only fetch individuals from this tree
	 * @param string $surn   if set, only fetch people with this surname
	 * @param string $salpha if set, only fetch surnames starting with this letter
	 * @param string $galpha if set, only fetch given names starting with this letter
	 * @param bool   $marnm  if set, include married names
	 *
	 * @return Family[]
	 */
	public static function families(Tree $tree, $surn, $salpha, $galpha, $marnm) {
		$list = array();
		foreach (self::individuals($tree, $surn, $salpha, $galpha, $marnm, true) as $indi) {
			foreach ($indi->getSpouseFamilies() as $family) {
				$list[$family->getXref()] = $family;
			}
		}
		usort($list, '\Fisharebest\Webtrees\GedcomRecord::compare');

		return $list;
	}
}
