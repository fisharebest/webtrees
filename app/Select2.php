<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

namespace Fisharebest\Webtrees;

/**
 * Generate markup and AJAX responses for SELECT2 queries.
 *
 * @link https://select2.github.io/
 */
class Select2 extends Html {
	// Send this many results with each request.
	const RESULTS_PER_PAGE = 20;

	// Don't send queries with fewer than this many characters
	const MINIMUM_INPUT_LENGTH = '1';

	// Don't send queries until this many milliseconds.
	const DELAY = '350';

	// API endpoints
	const URL_FAM  = 'action.php?action=select2-family';
	const URL_INDI = 'action.php?action=select2-individual';
	const URL_NOTE = 'action.php?action=select2-note';
	const URL_OBJE = 'action.php?action=select2-media';
	const URL_PLAC = 'action.php?action=select2-place';
	const URL_REPO = 'action.php?action=select2-repository';
	const URL_SOUR = 'action.php?action=select2-source';
	const URL_SUBM = 'action.php?action=select2-submitter';

	/**
	 * Select2 configuration that is common to all searches.
	 *
	 * @return string[]
	 */
	private static function commonConfig() {
		return [
			'autocomplete'                    => 'off',
			'class'                           => 'form-control select2',
			'data-ajax--delay'                => self::DELAY,
			'data-ajax--minimum-input-length' => self::MINIMUM_INPUT_LENGTH,
			'data-ajax--type'                 => 'POST',
			'data-allow-clear'                => 'true',
			'data-placeholder'                => '',
		];
	}

	/**
	 * Select2 configuration for a family lookup.
	 *
	 * @return string[]
	 */
	public static function familyConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_FAM];
	}

	/**
	 * Format a family name for display in a Select2 control.
	 *
	 * @param Family $family
	 *
	 * @return string
	 */
	public static function familyValue(Family $family) {
		return $family->getFullName();
	}

	/**
	 * Look up a family.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function familySearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];

		$cursor = Database::prepare("SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_gedcom AS gedcom, husb_name.n_sort, wife_name.n_sort" .
			" FROM `##families`" .
			" JOIN `##name` AS husb_name ON f_husb = husb_name.n_id AND f_file = husb_name.n_file" .
			" JOIN `##name` AS wife_name ON f_wife = wife_name.n_id AND f_file = wife_name.n_file" .
			" WHERE CONCAT(husb_name.n_full, ' ', wife_name.n_full) LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND f_file = :tree_id" .
			" AND husb_name.n_type <> '_MARNM' AND wife_name.n_type <> '_MARNM'" .
			" ORDER BY husb_name.n_sort, wife_name.n_sort COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$family = Family::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($family !== null && $family->canShowName()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::familyValue($family),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for an individual lookup.
	 *
	 * @return string[]
	 */
	public static function individualConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_INDI];
	}

	/**
	 * Format an individual name for display in a Select2 control.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public static function individualValue(Individual $individual) {
		return $individual->getFullName() . ', ' . $individual->getLifeSpan();
	}

	/**
	 * Look up an individual.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function individualSearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT i_id AS xref, i_gedcom AS gedcom, n_full" . " FROM `##individuals`" . " JOIN `##name` ON i_id = n_id AND i_file = n_file" . " WHERE n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND i_file = :tree_id" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$individual = Individual::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($individual !== null && $individual->canShowName()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => str_replace(['@N.N.', '@P.N.'], [I18N::translateContext('Unknown surname', '…'), I18N::translateContext('Unknown given name', '…')], $row->n_full) . ', ' . $individual->getLifeSpan(),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a media object lookup.
	 *
	 * @return string[]
	 */
	public static function mediaObjectConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_OBJE];
	}

	/**
	 * Format a media object name for display in a Select2 control.
	 *
	 * @param Media $media
	 *
	 * @return string
	 */
	public static function mediaObjectValue(Media $media) {
		return $media->getFullName() . ', ' . basename($media->getFilename());
	}

	/**
	 * Look up a media object.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function mediaObjectSearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT m_id AS xref, m_gedcom AS gedcom, n_full" . " FROM `##media`" . " JOIN `##name` ON m_id = n_id AND m_file = n_file" . " WHERE n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND m_file = :tree_id" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$media = Media::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($media !== null && $media->canShow()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::mediaObjectValue($media),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a note.
	 *
	 * @return string[]
	 */
	public static function noteConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_NOTE];
	}

	/**
	 * Format a note name for display in a Select2 control.
	 *
	 * @param Note $note
	 *
	 * @return string
	 */
	public static function noteValue(Note $note) {
		return $note->getFullName();
	}

	/**
	 * Look up a note.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function noteSearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT o_id AS xref, o_gedcom AS gedcom, n_full" . " FROM `##other`" . " JOIN `##name` ON o_id = n_id AND o_file = n_file" . " WHERE n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND o_file = :tree_id AND o_type='NOTE'" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$note = Note::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($note !== null && $note->canShowName()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::noteValue($note),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a note.
	 *
	 * @return string[]
	 */
	public static function placeConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_PLAC];
	}

	/**
	 * Format a note name for display in a Select2 control.
	 *
	 * @param Note $note
	 *
	 * @return string
	 */
	public static function placeValue(Note $note) {
		return $note->getFullName();
	}

	/**
	 * Look up a note.
	 *
	 * @param Tree   $tree   Search this tree.
	 * @param int    $page   Skip this number of pages.  Starts with zero.
	 * @param string $query  Search terms.
	 * @param bool   $create if true, include the query in the results so it can be created.
	 *
	 * @return string
	 */
	public static function placeSearch(Tree $tree, $page, $query, $create) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$results = [];
		$found   = false;

		// Do not filter by privacy. Place names on their own do not identify individuals.
		foreach (Place::findPlaces($query, $tree) as $place) {
			$place_name = $place->getGedcomName();
			if ($place_name === $query) {
				$found = true;
			}
			$results[]  = [
				'id'   => $place_name,
				'text' => $place_name,
			];
		}

		// No place found? Use an external gazetteer
		if (empty($results) && $tree->getPreference('GEONAMES_ACCOUNT')) {
			$url =
				"http://api.geonames.org/searchJSON" .
				"?name_startsWith=" . urlencode($query) .
				"&lang=" . WT_LOCALE .
				"&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
				"&style=full" .
				"&username=" . $tree->getPreference('GEONAMES_ACCOUNT');
			// try to use curl when file_get_contents not allowed
			if (ini_get('allow_url_fopen')) {
				$json   = file_get_contents($url);
				$places = json_decode($json, true);
			} elseif (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$json   = curl_exec($ch);
				$places = json_decode($json, true);
				curl_close($ch);
			} else {
				$places = [];
			}
			if (isset($places['geonames']) && is_array($places['geonames'])) {
				foreach ($places['geonames'] as $k => $place) {
					$place_name = $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName'];
					if ($place_name === $query) {
						$found = true;
					}
					$results[]  = [
						'id'   => $place_name,
						'text' => $place_name,
					];
				}
			}
		}

		// Include the query term in the results.  This allows the user to select a
		// place that doesn't already exist in the database.
		if (!$found && $create) {
			array_unshift($results, [
				'id'   => $query,
				'text' => $query,
			]);
		}

		$more    = count($results) > $offset + self::RESULTS_PER_PAGE;
		$results = array_slice($results, $offset, self::RESULTS_PER_PAGE);

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a repository lookup.
	 *
	 * @return string[]
	 */
	public static function repositoryConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_REPO];
	}

	/**
	 * Format a repository name for display in a Select2 control.
	 *
	 * @param Repository $repository
	 *
	 * @return string
	 */
	public static function repositoryValue(Repository $repository) {
		return $repository->getFullName();
	}

	/**
	 * Look up a repository.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function repositorySearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT o_id AS xref, o_gedcom AS gedcom, n_full" . " FROM `##other`" . " JOIN `##name` ON o_id = n_id AND o_file = n_file" . " WHERE o_type = 'REPO' AND n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND o_file = :tree_id" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$repository = Repository::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($repository !== null && $repository->canShow()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::repositoryValue($repository),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a source lookup.
	 *
	 * @return string[]
	 */
	public static function sourceConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_SOUR];
	}

	/**
	 * Format a source name for display in a Select2 control.
	 *
	 * @param Source $source
	 *
	 * @return string
	 */
	public static function sourceValue(Source $source) {
		return $source->getFullName();
	}

	/**
	 * Look up a source.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function sourceSearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT s_id AS xref, s_gedcom AS gedcom, n_full" . " FROM `##sources`" . " JOIN `##name` ON s_id = n_id AND s_file = n_file" . " WHERE n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND s_file = :tree_id" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$source = Source::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($source !== null && $source->canShow()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::sourceValue($source),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}

	/**
	 * Select2 configuration for a submitter lookup.
	 *
	 * @return string[]
	 */
	public static function submitterConfig() {
		return self::commonConfig() + ['data-ajax--url' => self::URL_SUBM];
	}

	/**
	 * Format a family name for display in a Select2 control.
	 *
	 * @param GedcomRecord $submitter
	 *
	 * @return string
	 */
	public static function submitterValue(GedcomRecord $submitter) {
		return $submitter->getFullName();
	}

	/**
	 * Look up a submitter.
	 *
	 * @param Tree   $tree  Search this tree.
	 * @param int    $page  Skip this number of pages.  Starts with zero.
	 * @param string $query Search terms.
	 *
	 * @return string
	 */
	public static function submitterSearch(Tree $tree, $page, $query) {
		$offset  = $page * self::RESULTS_PER_PAGE;
		$more    = false;
		$results = [];
		$cursor  = Database::prepare("SELECT i_id AS xref, i_gedcom AS gedcom, n_full" . " FROM `##individuals`" . " JOIN `##name` ON i_id = n_id AND i_file = n_file" . " WHERE n_full LIKE CONCAT('%', REPLACE(:query, ' ', '%'), '%') AND i_file = :tree_id" . " ORDER BY n_full COLLATE :collation")->execute([
			'query'     => $query,
			'tree_id'   => $tree->getTreeId(),
			'collation' => I18N::collation(),
		]);

		while (is_object($row = $cursor->fetch())) {
			$submitter = GedcomRecord::getInstance($row->xref, $tree, $row->gedcom);
			// Filter for privacy
			if ($submitter !== null && $submitter->canShow()) {
				if ($offset > 0) {
					// Skip results
					$offset--;
				} elseif (count($results) === self::RESULTS_PER_PAGE) {
					// Stop when we have found a page of results
					$more = true;
					break;
				} else {
					// Add to the results
					$results[] = [
						'id'   => $row->xref,
						'text' => self::submitterValue($submitter),
					];
				}
			}
		}
		$cursor->closeCursor();

		return [
			'results'    => $results,
			'pagination' => [
				'more' => $more,
			],
		];
	}
}
