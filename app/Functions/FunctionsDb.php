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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;

/**
 * Class FunctionsDb - common functions
 */
class FunctionsDb {
	/**
	 * Fetch all records linked to a record - when deleting an object, we must
	 * also delete all links to it.
	 *
	 * @param string $xref
	 * @param int $gedcom_id
	 *
	 * @return string[]
	 */
	public static function fetchAllLinks($xref, $gedcom_id) {
		return
			Database::prepare(
				"SELECT l_from FROM `##link` WHERE l_file = ? AND l_to = ?" .
				" UNION " .
				"SELECT xref FROM `##change` WHERE status = 'pending' AND gedcom_id = ? AND new_gedcom LIKE" .
				" CONCAT('%@', ?, '@%') AND new_gedcom NOT LIKE CONCAT('0 @', ?, '@%')"
			)->execute(array(
				$gedcom_id,
				$xref,
				$gedcom_id,
				$xref,
				$xref,
			))->fetchOneColumn();
	}

	/**
	 * Get a list of all the sources.
	 *
	 * @param Tree $tree
	 *
	 * @return Source[] array
	 */
	public static function getSourceList(Tree $tree) {
		$rows = Database::prepare(
			"SELECT s_id AS xref, s_gedcom AS gedcom FROM `##sources` WHERE s_file = :tree_id"
		)->execute(array(
			'tree_id' => $tree->getTreeId(),
		))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$list[] = Source::getInstance($row->xref, $tree, $row->gedcom);
		}
		$list = array_filter($list, function (Source $x) { return $x->canShowName(); });
		usort($list, '\Fisharebest\Webtrees\GedcomRecord::compare');

		return $list;
	}

	/**
	 * Get a list of all the repositories.
	 *
	 * @param Tree $tree
	 *
	 * @return Repository[] array
	 */
	public static function getRepositoryList(Tree $tree) {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO' AND o_file = ?"
		)->execute(array(
			$tree->getTreeId(),
		))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$list[] = Repository::getInstance($row->xref, $tree, $row->gedcom);
		}
		$list = array_filter($list, function (Repository $x) { return $x->canShowName(); });
		usort($list, '\Fisharebest\Webtrees\GedcomRecord::compare');

		return $list;
	}

	/**
	 * Get a list of all the shared notes.
	 *
	 * @param Tree $tree
	 *
	 * @return Note[] array
	 */
	public static function getNoteList(Tree $tree) {
		$rows = Database::prepare(
			"SELECT o_id AS xref, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE' AND o_file = :tree_id"
		)->execute(array(
			'tree_id' => $tree->getTreeId(),
		))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$list[] = Note::getInstance($row->xref, $tree, $row->gedcom);
		}
		$list = array_filter($list, function (Note $x) { return $x->canShowName(); });
		usort($list, '\Fisharebest\Webtrees\GedcomRecord::compare');

		return $list;
	}

	/**
	 * Search all individuals
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The trees to search
	 *
	 * @return Individual[]
	 */
	public static function searchIndividuals(array $query, array $trees) {
		// Convert the query into a regular expression
		$queryregex = array();

		$sql  = "SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom FROM `##individuals` WHERE 1";
		$args = array();

		foreach ($query as $n => $q) {
			$queryregex[] = preg_quote(I18N::strtoupper($q), '/');
			$sql .= " AND i_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND i_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|REFN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Individual $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search the names of individuals
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The trees to search
	 *
	 * @return Individual[]
	 */
	public static function searchIndividualNames(array $query, array $trees) {
		$sql  = "SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom, n_full FROM `##individuals` JOIN `##name` ON i_id=n_id AND i_file=n_file WHERE 1";
		$args = array();

		// Convert the query into a SQL expression
		foreach ($query as $n => $q) {
			$sql .= " AND n_full COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND i_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";
		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$indi = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			foreach ($indi->getAllNames() as $num => $name) {
				if ($name['fullNN'] === $row->n_full) {
					$indi->setPrimaryName($num);
					// We need to clone $indi, as we may have multiple references to the
					// same person in this list, and the "primary name" would otherwise
					// be shared amongst all of them.
					$list[] = clone $indi;
					// Only need to match an individual on one name
					break;
				}
			}
		}
		$list = array_filter($list, function (Individual $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search for individuals names/places using phonetic matching
	 *
	 * @param string $soundex
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $place
	 * @param Tree[] $trees
	 *
	 * @return Individual[]
	 */
	public static function searchIndividualsPhonetic($soundex, $lastname, $firstname, $place, array $trees) {
		switch ($soundex) {
			case 'Russell':
				$givn_sdx = Soundex::russell($firstname);
				$surn_sdx = Soundex::russell($lastname);
				$plac_sdx = Soundex::russell($place);
				break;
			case 'DaitchM':
				$givn_sdx = Soundex::daitchMokotoff($firstname);
				$surn_sdx = Soundex::daitchMokotoff($lastname);
				$plac_sdx = Soundex::daitchMokotoff($place);
				break;
			default:
				throw new \DomainException('soundex: ' . $soundex);
		}

		// Nothing to search for?  Return nothing.
		if (!$givn_sdx && !$surn_sdx && !$plac_sdx) {
			return array();
		}

		$sql  = "SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom FROM `##individuals`";
		$args = array();

		if ($place) {
			$sql .= " JOIN `##placelinks` ON pl_file = i_file AND pl_gid = i_id";
			$sql .= " JOIN `##places` ON p_file = pl_file AND pl_p_id = p_id";
		}
		if ($firstname || $lastname) {
			$sql .= " JOIN `##name` ON i_file=n_file AND i_id=n_id";
		}
		$sql .= " AND i_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		if ($firstname && $givn_sdx) {
			$sql .= " AND (";
			$givn_sdx = explode(':', $givn_sdx);
			foreach ($givn_sdx as $n => $sdx) {
				$sql .= $n ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "n_soundex_givn_std LIKE CONCAT('%', :given_name_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "n_soundex_givn_dm LIKE CONCAT('%', :given_name_" . $n . ", '%')";
						break;
				}
				$args['given_name_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		if ($lastname && $surn_sdx) {
			$sql .= " AND (";
			$surn_sdx = explode(':', $surn_sdx);
			foreach ($surn_sdx as $n => $sdx) {
				$sql .= $n ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "n_soundex_surn_std LIKE CONCAT('%', :surname_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "n_soundex_surn_dm LIKE CONCAT('%', :surname_" . $n . ", '%')";
						break;
				}
				$args['surname_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		if ($place && $plac_sdx) {
			$sql .= " AND (";
			$plac_sdx = explode(':', $plac_sdx);
			foreach ($plac_sdx as $n => $sdx) {
				$sql .= $n ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "p_std_soundex LIKE CONCAT('%', :place_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "p_dm_soundex LIKE CONCAT('%', :place_" . $n . ", '%')";
						break;
				}
				$args['place_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$list[] = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
		}
		$list = array_filter($list, function (Individual $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * get recent changes since the given julian day inclusive
	 *
	 * @param int $jd leave empty to include all
	 * @param bool $allgeds
	 *
	 * @return string[] List of XREFs of records with changes
	 */
	public static function getRecentChanges($jd = 0, $allgeds = false) {
		global $WT_TREE;

		$sql  = "SELECT d_gid FROM `##dates` WHERE d_fact='CHAN' AND d_julianday1>=?";
		$vars = array($jd);
		if (!$allgeds) {
			$sql .= " AND d_file=?";
			$vars[] = $WT_TREE->getTreeId();
		}
		$sql .= " ORDER BY d_julianday1 DESC";

		return Database::prepare($sql)->execute($vars)->fetchOneColumn();
	}

	/**
	 * Search family records
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The trees to search
	 *
	 * @return Family[]
	 */
	public static function searchFamilies(array $query, array $trees) {
		// Convert the query into a regular expression
		$queryregex = array();

		$sql  = "SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom FROM `##families` WHERE 1";
		$args = array();

		foreach ($query as $n => $q) {
			$queryregex[] = preg_quote(I18N::strtoupper($q), '/');
			$sql .= " AND f_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND f_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Family::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|REFN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Family $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search the names of the husb/wife in a family
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The trees to search
	 *
	 * @return Family[]
	 */
	public static function searchFamilyNames(array $query, array $trees) {
		// No query => no results
		if (!$query) {
			return array();
		}

		$sql =
			"SELECT DISTINCT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom" .
			" FROM `##families`" .
			" LEFT JOIN `##name` husb ON f_husb = husb.n_id AND f_file = husb.n_file" .
			" LEFT JOIN `##name` wife ON f_wife = wife.n_id AND f_file = wife.n_file" .
			" WHERE 1";
		$args = array();

		foreach ($query as $n => $q) {
			$sql .= " AND (husb.n_full COLLATE :husb_collate_" . $n . " LIKE CONCAT('%', :husb_query_" . $n . ", '%') OR wife.n_full COLLATE :wife_collate_" . $n . " LIKE CONCAT('%', :wife_query_" . $n . ", '%'))";
			$args['husb_collate_' . $n] = I18N::collation();
			$args['husb_query_' . $n]   = Filter::escapeLike($q);
			$args['wife_collate_' . $n] = I18N::collation();
			$args['wife_query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND f_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$list[] = Family::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
		}
		$list = array_filter($list, function (Family $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search the sources
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The tree to search
	 *
	 * @return Source[]
	 */
	public static function searchSources($query, $trees) {
		// Convert the query into a regular expression
		$queryregex = array();

		$sql  = "SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom FROM `##sources` WHERE 1";
		$args = array();

		foreach ($query as $n => $q) {
			$queryregex[] = preg_quote(I18N::strtoupper($q), '/');
			$sql .= " AND s_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND s_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Source::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|REFN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Source $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search the shared notes
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The tree to search
	 *
	 * @return Note[]
	 */
	public static function searchNotes(array $query, array $trees) {
		// Convert the query into a regular expression
		$queryregex = array();

		$sql  = "SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE'";
		$args = array();

		foreach ($query as $n => $q) {
			$queryregex[] = preg_quote(I18N::strtoupper($q), '/');
			$sql .= " AND o_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND o_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Note::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|REFN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Note $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Search the repositories
	 *
	 * @param string[] $query Search terms
	 * @param Tree[] $trees The trees to search
	 *
	 * @return Repository[]
	 */
	public static function searchRepositories(array $query, array $trees) {
		// Convert the query into a regular expression
		$queryregex = array();

		$sql  = "SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO'";
		$args = array();

		foreach ($query as $n => $q) {
			$queryregex[] = preg_quote(I18N::strtoupper($q), '/');
			$sql .= " AND o_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Filter::escapeLike($q);
		}

		$sql .= " AND o_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = array();
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Repository::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|REFN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Repository $x) { return $x->canShowName(); });

		return $list;
	}

	/**
	 * Find the record for the given rin.
	 *
	 * @param string $rin
	 *
	 * @return string
	 */
	public static function findRin($rin) {
		global $WT_TREE;

		$xref =
			Database::prepare("SELECT i_id FROM `##individuals` WHERE i_rin=? AND i_file=?")
				->execute(array($rin, $WT_TREE->getTreeId()))
				->fetchOne();

		return $xref ? $xref : $rin;
	}

	/**
	 * Get array of common surnames
	 *
	 * This function returns a simple array of the most common surnames
	 * found in the individuals list.
	 *
	 * @param int $min The number of times a surname must occur before it is added to the array
	 * @param Tree $tree
	 *
	 * @return mixed[][]
	 */
	public static function getCommonSurnames($min, Tree $tree) {
		$COMMON_NAMES_ADD    = $tree->getPreference('COMMON_NAMES_ADD');
		$COMMON_NAMES_REMOVE = $tree->getPreference('COMMON_NAMES_REMOVE');

		$topsurns = self::getTopSurnames($tree->getTreeId(), $min, 0);
		foreach (explode(',', $COMMON_NAMES_ADD) as $surname) {
			if ($surname && !array_key_exists($surname, $topsurns)) {
				$topsurns[$surname] = $min;
			}
		}
		foreach (explode(',', $COMMON_NAMES_REMOVE) as $surname) {
			unset($topsurns[I18N::strtoupper($surname)]);
		}

		//-- check if we found some, else recurse
		if (empty($topsurns) && $min > 2) {
			return self::getCommonSurnames($min / 2, $tree);
		} else {
			uksort($topsurns, '\Fisharebest\Webtrees\I18N::strcasecmp');
			foreach ($topsurns as $key => $value) {
				$topsurns[$key] = array('name' => $key, 'match' => $value);
			}

			return $topsurns;
		}
	}

	/**
	 * get the top surnames
	 *
	 * @param int $ged_id fetch surnames from this gedcom
	 * @param int $min only fetch surnames occuring this many times
	 * @param int $max only fetch this number of surnames (0=all)
	 *
	 * @return string[]
	 */
	public static function getTopSurnames($ged_id, $min, $max) {
		// Use n_surn, rather than n_surname, as it is used to generate URLs for
		// the indi-list, etc.
		$max = (int) $max;
		if ($max == 0) {
			return
				Database::prepare(
					"SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name`" .
					" WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '', '?', 'UNKNOWN')" .
					" GROUP BY n_surn HAVING COUNT(n_surn) >= :min" .
					" ORDER BY 2 DESC"
				)->execute(array(
					'tree_id' => $ged_id,
					'min'     => $min,
				))->fetchAssoc();
		} else {
			return
				Database::prepare(
					"SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name`" .
					" WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '', '?', 'UNKNOWN')" .
					" GROUP BY n_surn HAVING COUNT(n_surn) >= :min" .
					" ORDER BY 2 DESC" .
					" LIMIT :limit"
				)->execute(array(
					'tree_id' => $ged_id,
					'min'     => $min,
					'limit'   => $max,
				))->fetchAssoc();
		}
	}

	/**
	 * Get a list of events whose anniversary occured on a given julian day.
	 * Used on the on-this-day/upcoming blocks and the day/month calendar views.
	 *
	 * @param int $jd the julian day
	 * @param string $facts restrict the search to just these facts or leave blank for all
	 * @param Tree $tree the tree to search
	 *
	 * @return Fact[]
	 */
	public static function getAnniversaryEvents($jd, $facts, Tree $tree) {
		$found_facts = array();
		foreach (array(
			         new GregorianDate($jd),
			         new JulianDate($jd),
			         new FrenchDate($jd),
			         new JewishDate($jd),
			         new HijriDate($jd),
			         new JalaliDate($jd),
		         ) as $anniv) {
			// Build a SQL where clause to match anniversaries in the appropriate calendar.
			$ind_sql =
				"SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
				" FROM `##dates` JOIN `##individuals` ON d_gid = i_id AND d_file = i_file" .
				" WHERE d_type = :type AND d_file = :tree_id";
			$fam_sql =
				"SELECT DISTINCT f_id AS xref, f_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
				" FROM `##dates` JOIN `##families` ON d_gid = f_id AND d_file = f_file" .
				" WHERE d_type = :type AND d_file = :tree_id";
			$args = array(
				'type'    => $anniv->format('%@'),
				'tree_id' => $tree->getTreeId(),
			);

			$where = "";
			// SIMPLE CASES:
			// a) Non-hebrew anniversaries
			// b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
			if (!$anniv instanceof JewishDate || in_array($anniv->m, array(1, 5, 6, 9, 10, 11, 12, 13))) {
				// Dates without days go on the first day of the month
				// Dates with invalid days go on the last day of the month
				if ($anniv->d === 1) {
					$where .= " AND d_day <= 1";
				} elseif ($anniv->d === $anniv->daysInMonth()) {
					$where .= " AND d_day >= :day";
					$args['day'] = $anniv->d;
				} else {
					$where .= " AND d_day = :day";
					$args['day'] = $anniv->d;
				}
				$where .= " AND d_mon = :month";
				$args['month'] = $anniv->m;
			} else {
				// SPECIAL CASES:
				switch ($anniv->m) {
					case 2:
						// 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH)
						if ($anniv->d === 1) {
							$where .= " AND d_day <= 1 AND d_mon = 2";
						} elseif ($anniv->d === 30) {
							$where .= " AND d_day >= 30 AND d_mon = 2";
						} elseif ($anniv->d === 29 && $anniv->daysInMonth() === 29) {
							$where .= " AND (d_day = 29 OR d_day > 30) AND d_mon = 2";
						} else {
							$where .= " AND d_day = :day AND d_mon = 2";
							$args['day'] = $anniv->d;
						}
						break;
					case 3:
						// 1 KSL includes 30 CSH (if this year didn’t have 30 CSH)
						// 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL)
						if ($anniv->d === 1) {
							$tmp = new JewishDate(array($anniv->y, 'CSH', 1));
							if ($tmp->daysInMonth() === 29) {
								$where .= " AND (d_day <= 1 AND d_mon = 3 OR d_day = 30 AND d_mon = 2)";
							} else {
								$where .= " AND d_day <= 1 AND d_mon = 3";
							}
						} elseif ($anniv->d === 30) {
							$where .= " AND d_day >= 30 AND d_mon = 3";
						} elseif ($anniv->d == 29 && $anniv->daysInMonth() === 29) {
							$where .= " AND (d_day = 29 OR d_day > 30) AND d_mon = 3";
						} else {
							$where .= " AND d_day = :day AND d_mon = 3";
							$args['day'] = $anniv->d;
						}
						break;
					case 4:
						// 1 TVT includes 30 KSL (if this year didn’t have 30 KSL)
						if ($anniv->d === 1) {
							$tmp = new JewishDate(array($anniv->y, 'KSL', 1));
							if ($tmp->daysInMonth() === 29) {
								$where .= " AND (d_day <=1 AND d_mon = 4 OR d_day = 30 AND d_mon = 3)";
							} else {
								$where .= " AND d_day <= 1 AND d_mon = 4";
							}
						} elseif ($anniv->d === $anniv->daysInMonth()) {
							$where .= " AND d_day >= :day AND d_mon=4";
							$args['day'] = $anniv->d;
						} else {
							$where .= " AND d_day = :day AND d_mon=4";
							$args['day'] = $anniv->d;
						}
						break;
					case 7: // ADS includes ADR (non-leap)
						if ($anniv->d === 1) {
							$where .= " AND d_day <= 1";
						} elseif ($anniv->d === $anniv->daysInMonth()) {
							$where .= " AND d_day >= :day";
							$args['day'] = $anniv->d;
						} else {
							$where .= " AND d_day = :day";
							$args['day'] = $anniv->d;
						}
						$where .= " AND (d_mon = 6 AND MOD(7 * d_year + 1, 19) >= 7 OR d_mon = 7)";
						break;
					case 8: // 1 NSN includes 30 ADR, if this year is non-leap
						if ($anniv->d === 1) {
							if ($anniv->isLeapYear()) {
								$where .= " AND d_day <= 1 AND d_mon = 8";
							} else {
								$where .= " AND (d_day <= 1 AND d_mon = 8 OR d_day = 30 AND d_mon = 6)";
							}
						} elseif ($anniv->d === $anniv->daysInMonth()) {
							$where .= " AND d_day >= :day AND d_mon = 8";
							$args['day'] = $anniv->d;
						} else {
							$where .= " AND d_day = :day AND d_mon = 8";
							$args['day'] = $anniv->d;
						}
						break;
				}
			}
			// Only events in the past (includes dates without a year)
			$where .= " AND d_year <= :year";
			$args['year'] = $anniv->y;

			if ($facts) {
				// Restrict to certain types of fact
				$where .= " AND d_fact IN (";
				preg_match_all('/([_A-Z]+)/', $facts, $matches);
				foreach ($matches[1] as $n => $fact) {
					$where .= $n ? ", " : "";
					$where .= ":fact_" . $n;
					$args['fact_' . $n] = $fact;
				}
				$where .= ")";
			} else {
				// If no facts specified, get all except these
				$where .= " AND d_fact NOT IN ('CHAN', 'BAPL', 'SLGC', 'SLGS', 'ENDL', 'CENS', 'RESI', '_TODO')";
			}

			$order_by = " ORDER BY d_day, d_year DESC";

			// Now fetch these anniversaries
			foreach (array('INDI' => $ind_sql . $where . $order_by, 'FAM' => $fam_sql . $where . $order_by) as $type => $sql) {
				$rows = Database::prepare($sql)->execute($args)->fetchAll();
				foreach ($rows as $row) {
					if ($type === 'INDI') {
						$record = Individual::getInstance($row->xref, $tree, $row->gedcom);
					} else {
						$record = Family::getInstance($row->xref, $tree, $row->gedcom);
					}
					$anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);
					foreach ($record->getFacts() as $fact) {
						if (($fact->getDate()->minimumDate() == $anniv_date->minimumDate() || $fact->getDate()->maximumDate() == $anniv_date->minimumDate()) && $fact->getTag() === $row->d_fact) {
							$fact->anniv   = $row->d_year === '0' ? 0 : $anniv->y - $row->d_year;
							$found_facts[] = $fact;
						}
					}
				}
			}
		}

		return $found_facts;
	}

	/**
	 * Get a list of events which occured during a given date range.
	 *
	 * @param int $jd1 the start range of julian day
	 * @param int $jd2 the end range of julian day
	 * @param string $facts restrict the search to just these facts or leave blank for all
	 * @param Tree $tree the tree to search
	 *
	 * @return Fact[]
	 */
	public static function getCalendarEvents($jd1, $jd2, $facts, Tree $tree) {
		// If no facts specified, get all except these
		$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
		if ($facts != '_TODO') {
			$skipfacts .= ',_TODO';
		}

		$found_facts = array();

		// Events that start or end during the period
		$where = "WHERE (d_julianday1>={$jd1} AND d_julianday1<={$jd2} OR d_julianday2>={$jd1} AND d_julianday2<={$jd2})";

		// Restrict to certain types of fact
		if (empty($facts)) {
			$excl_facts = "'" . preg_replace('/\W+/', "','", $skipfacts) . "'";
			$where .= " AND d_fact NOT IN ({$excl_facts})";
		} else {
			$incl_facts = "'" . preg_replace('/\W+/', "','", $facts) . "'";
			$where .= " AND d_fact IN ({$incl_facts})";
		}
		// Only get events from the current gedcom
		$where .= " AND d_file=" . $tree->getTreeId();

		// Now fetch these events
		$ind_sql = "SELECT d_gid AS xref, i_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##individuals` {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_julianday1";
		$fam_sql = "SELECT d_gid AS xref, f_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##families`    {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_julianday1";
		foreach (array('INDI' => $ind_sql, 'FAM' => $fam_sql) as $type => $sql) {
			$rows = Database::prepare($sql)->fetchAll();
			foreach ($rows as $row) {
				if ($type === 'INDI') {
					$record = Individual::getInstance($row->xref, $tree, $row->gedcom);
				} else {
					$record = Family::getInstance($row->xref, $tree, $row->gedcom);
				}
				$anniv_date = new Date($row->d_type . ' ' . $row->d_day . ' ' . $row->d_month . ' ' . $row->d_year);
				foreach ($record->getFacts() as $fact) {
					if (($fact->getDate()->minimumDate() == $anniv_date->minimumDate() || $fact->getDate()->maximumDate() == $anniv_date->minimumDate()) && $fact->getTag() === $row->d_fact) {
						$fact->anniv   = 0;
						$found_facts[] = $fact;
					}
				}
			}
		}

		return $found_facts;
	}

	/**
	 * Get the list of current and upcoming events, sorted by anniversary date
	 *
	 * @param int $jd1
	 * @param int $jd2
	 * @param string $events
	 * @param Tree $tree
	 *
	 * @return Fact[]
	 */
	public static function getEventsList($jd1, $jd2, $events, Tree $tree) {
		$found_facts = array();
		for ($jd = $jd1; $jd <= $jd2; ++$jd) {
			$found_facts = array_merge($found_facts, self::getAnniversaryEvents($jd, $events, $tree));
		}

		return $found_facts;
	}

	/**
	 * Check if a media file is shared (i.e. used by another gedcom)
	 *
	 * @param string $file_name
	 * @param int $ged_id
	 *
	 * @return bool
	 */
	public static function isMediaUsedInOtherTree($file_name, $ged_id) {
		return
			(bool) Database::prepare("SELECT COUNT(*) FROM `##media` WHERE m_filename LIKE ? AND m_file<>?")
				->execute(array("%{$file_name}", $ged_id))
				->fetchOne();
	}

	/**
	 * Get the blocks for a specified user.
	 *
	 * @param int $user_id
	 *
	 * @return string[][]
	 */
	public static function getUserBlocks($user_id) {
		global $WT_TREE;

		$blocks = array('main' => array(), 'side' => array());
		$rows   = Database::prepare(
			"SELECT SQL_CACHE location, block_id, module_name" .
			" FROM  `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" JOIN  `##module_privacy` USING (module_name)" .
			" WHERE user_id=?" .
			" AND   status='enabled'" .
			" AND   `##module_privacy`.gedcom_id=?" .
			" AND   access_level>=?" .
			" ORDER BY location, block_order"
		)->execute(array($user_id, $WT_TREE->getTreeId(), Auth::accessLevel($WT_TREE)))->fetchAll();
		foreach ($rows as $row) {
			$blocks[$row->location][$row->block_id] = $row->module_name;
		}

		return $blocks;
	}

	/**
	 * Get the blocks for the specified tree
	 *
	 * @param int $gedcom_id
	 *
	 * @return string[][]
	 */
	public static function getTreeBlocks($gedcom_id) {
		if ($gedcom_id < 0) {
			$access_level = Auth::PRIV_NONE;
		} else {
			$access_level = Auth::accessLevel(Tree::findById($gedcom_id));
		}

		$blocks = array('main' => array(), 'side' => array());
		$rows   = Database::prepare(
			"SELECT SQL_CACHE location, block_id, module_name" .
			" FROM  `##block`" .
			" JOIN  `##module` USING (module_name)" .
			" JOIN  `##module_privacy` USING (module_name, gedcom_id)" .
			" WHERE gedcom_id = :tree_id" .
			" AND   status='enabled'" .
			" AND   access_level >= :access_level" .
			" ORDER BY location, block_order"
		)->execute(array(
			'tree_id'      => $gedcom_id,
			'access_level' => $access_level,
		))->fetchAll();
		foreach ($rows as $row) {
			$blocks[$row->location][$row->block_id] = $row->module_name;
		}

		return $blocks;
	}

	/**
	 * Update favorites after merging records.
	 *
	 * @param string $xref_from
	 * @param string $xref_to
	 * @param Tree $tree
	 *
	 * @return int
	 */
	public static function updateFavorites($xref_from, $xref_to, Tree $tree) {
		return
			Database::prepare("UPDATE `##favorite` SET xref=? WHERE xref=? AND gedcom_id=?")
				->execute(array($xref_to, $xref_from, $tree->getTreeId()))
				->rowCount();
	}
}
