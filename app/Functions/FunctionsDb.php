<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\GedcomRecord;
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
			)->execute([
				$gedcom_id,
				$xref,
				$gedcom_id,
				$xref,
				$xref,
			])->fetchOneColumn();
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
		$args = [];

		// Convert the query into a SQL expression
		foreach ($query as $n => $q) {
			$sql .= " AND n_full COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND i_file IN (";
		foreach ($trees as $n => $tree) {
			$sql .= $n ? ", " : "";
			$sql .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";
		$list = [];
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
		$list = array_filter($list, function (Individual $x) {
			return $x->canShowName();
		});

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

		// Nothing to search for? Return nothing.
		if ($givn_sdx === '' && $surn_sdx === '' && $plac_sdx === '') {
			return [];
		}

		$sql  = "SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom FROM `##individuals`";
		$args = [];

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

		if ($givn_sdx !== '') {
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

		if ($surn_sdx !== '') {
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

		if ($plac_sdx !== '') {
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

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$list[] = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
		}
		$list = array_filter($list, function (Individual $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * Get array of common surnames
	 *
	 * This function returns a simple array of the most common surnames
	 * found in the individuals list.
	 *
	 * @deprecated
	 *
	 * @param int $min The number of times a surname must occur before it is added to the array
	 * @param Tree $tree
	 *
	 * @return int[]
	 */
	public static function getCommonSurnames($min, Tree $tree) {
		return self::getTopSurnames($tree->getTreeId(), $min, 0);
	}

	/**
	 * get the top surnames
	 *
	 * @param int $ged_id fetch surnames from this gedcom
	 * @param int $min only fetch surnames occuring this many times
	 * @param int $max only fetch this number of surnames (0=all)
	 *
	 * @return int[]
	 */
	public static function getTopSurnames($ged_id, $min, $max) {
		// Use n_surn, rather than n_surname, as it is used to generate URLs for
		// the indi-list, etc.
		$max = (int) $max;
		if ($max == 0) {
			return
				Database::prepare(
					"SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name`" .
					" WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
					" GROUP BY n_surn HAVING COUNT(n_surn) >= :min" .
					" ORDER BY 2 DESC"
				)->execute([
					'tree_id' => $ged_id,
					'min'     => $min,
				])->fetchAssoc();
		} else {
			return
				Database::prepare(
					"SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name`" .
					" WHERE n_file = :tree_id AND n_type != '_MARNM' AND n_surn NOT IN ('@N.N.', '')" .
					" GROUP BY n_surn HAVING COUNT(n_surn) >= :min" .
					" ORDER BY 2 DESC" .
					" LIMIT :limit"
				)->execute([
					'tree_id' => $ged_id,
					'min'     => $min,
					'limit'   => $max,
				])->fetchAssoc();
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
		$found_facts = [];
		foreach ([
			new GregorianDate($jd),
			new JulianDate($jd),
			new FrenchDate($jd),
			new JewishDate($jd),
			new HijriDate($jd),
			new JalaliDate($jd),
		 ] as $anniv) {
			// Build a SQL where clause to match anniversaries in the appropriate calendar.
			$ind_sql =
				"SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
				" FROM `##dates` JOIN `##individuals` ON d_gid = i_id AND d_file = i_file" .
				" WHERE d_type = :type AND d_file = :tree_id";
			$fam_sql =
				"SELECT DISTINCT f_id AS xref, f_gedcom AS gedcom, d_type, d_day, d_month, d_year, d_fact" .
				" FROM `##dates` JOIN `##families` ON d_gid = f_id AND d_file = f_file" .
				" WHERE d_type = :type AND d_file = :tree_id";
			$args = [
				'type'    => $anniv->format('%@'),
				'tree_id' => $tree->getTreeId(),
			];

			$where = "";
			// SIMPLE CASES:
			// a) Non-hebrew anniversaries
			// b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
			if (!$anniv instanceof JewishDate || in_array($anniv->m, [1, 5, 6, 9, 10, 11, 12, 13])) {
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
							$tmp = new JewishDate([$anniv->y, 'CSH', 1]);
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
							$tmp = new JewishDate([$anniv->y, 'KSL', 1]);
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
			foreach (['INDI' => $ind_sql . $where . $order_by, 'FAM' => $fam_sql . $where . $order_by] as $type => $sql) {
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
							$fact->jd      = $jd;
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
		$skipfacts = 'CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT';

		$found_facts = [];

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
		foreach (['INDI' => $ind_sql, 'FAM' => $fam_sql] as $type => $sql) {
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
	 * @param int     $jd1
	 * @param int     $jd2
	 * @param string  $events
	 * @param Boolean $only_living
	 * @param string  $sort_by
	 * @param Tree    $tree
	 *
	 * @return Fact[]
	 */
	public static function getEventsList($jd1, $jd2, $events, $only_living = false, $sort_by = 'anniv', Tree $tree) {
		$found_facts = [];
		$facts       = [];
		for ($jd = $jd1; $jd <= $jd2; ++$jd) {
			$found_facts = array_merge($found_facts, self::getAnniversaryEvents($jd, $events, $tree));
		}

		foreach ($found_facts as $fact) {
			$record = $fact->getParent();
			// only living people ?
			if ($only_living) {
				if ($record instanceof Individual && $record->isDead()) {
					continue;
				}
				if ($record instanceof Family) {
					$husb = $record->getHusband();
					if (is_null($husb) || $husb->isDead()) {
						continue;
					}
					$wife = $record->getWife();
					if (is_null($wife) || $wife->isDead()) {
						continue;
					}
				}
			}
			$facts[] = $fact;
		}

		switch ($sort_by) {
			case 'anniv':
				uasort($facts,
					function (Fact $x, Fact $y) {
						return Fact::compareDate($y, $x);
					}
				);
				break;
			case 'alpha':
				uasort($facts,
					function (Fact $x, Fact $y) {
						return GedcomRecord::compare($x->getParent(), $y->getParent());
					}
				);
				break;
		}

		return $facts;
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
			(bool) Database::prepare(
				"SELECT COUNT(*) FROM `##media_file`" .
				" WHERE multimedia_file_refn LIKE :search AND m_file <> :tree_id"
			)->execute([
				'search'  => '%' . $file_name,
				'tree_id' => $ged_id,
			])->fetchOne();
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
				->execute([$xref_to, $xref_from, $tree->getTreeId()])
				->rowCount();
	}
}
