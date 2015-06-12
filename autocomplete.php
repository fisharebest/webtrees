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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree   $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'autocomplete.php');
require './includes/session.php';

header('Content-Type: text/plain; charset=UTF-8');

$term = Filter::get('term'); // we can search on '"><& etc.
$type = Filter::get('field');

switch ($type) {
case 'ASSO': // Associates of an individuals, whose name contains the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT 'INDI' AS type, i_id AS xref, i_gedcom AS gedcom, n_full" .
		" FROM `##individuals`" .
		" JOIN `##name` ON i_id = n_id AND i_file = n_file" .
		" WHERE (n_full LIKE CONCAT('%', REPLACE(:term_1, ' ', '%'), '%') OR n_surn LIKE CONCAT('%', REPLACE(:term_2, ' ', '%'), '%')) AND i_file = :tree_id" .
		" ORDER BY n_full COLLATE :collate"
	)->execute(array(
		'term_1'  => $term,
		'term_2'  => $term,
		'tree_id' => $WT_TREE->getTreeId(),
		'collate' => I18N::collation(),
	))->fetchAll();

	// Filter for privacy and whether they could be alive at the right time
	$event_date = Filter::get('extra');
	$date       = new Date($event_date);
	$event_jd   = $date->julianDay();
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($person->canShow()) {
			if ($event_jd) {
				// Exclude individuals who were born after the event.
				$person_birth_jd = $person->getEstimatedBirthDate()->minimumJulianDay();
				if ($person_birth_jd && $person_birth_jd > $event_jd) {
					continue;
				}
				// Exclude individuals who died before the event.
				$person_death_jd = $person->getEstimatedDeathDate()->maximumJulianDay();
				if ($person_death_jd && $person_death_jd < $event_jd) {
					continue;
				}
			}
			// Add the age (if we have it) or the lifespan (if we do not).
			$label = $person->getFullName();
			if ($event_jd && $person->getBirthDate()->isOK()) {
				$label .= ', <span class="age">(' . I18N::translate('Age') . ' ' . $person->getBirthDate()->minimumDate()->getAge(false, $event_jd) . ')</span>';
			} else {
				$label .= ', <i>' . $person->getLifeSpan() . '</i>';
			}
			$data[$row->xref] = array('value' => $row->xref, 'label' => $label);
		}
	}
	echo json_encode($data);

	return;

case 'CEME': // Cemetery fields, that contain the search term
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT SQL_CACHE i_id AS xref, i_gedcom AS gedcom" .
		" FROM `##individuals`" .
		" WHERE i_gedcom LIKE '%\n2 CEME %' AND i_file = :tree_id" .
		" ORDER BY SUBSTRING_INDEX(i_gedcom, '\n2 CEME ', -1) COLLATE :collation"
	)->execute(array(
		'tree_id'   => $WT_TREE->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
	// Filter for privacy
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if (preg_match('/\n2 CEME (.*' . preg_quote($term, '/') . '.*)/i', $person->getGedcom(), $match)) {
			if (!in_array($match[1], $data)) {
				$data[] = $match[1];
			}
		}
	}
	echo json_encode($data);

	return;

case 'FAM': // Families, whose name contains the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_FAM_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$family = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($family->canShowName()) {
			$marriage_year = $family->getMarriageYear();
			if ($marriage_year) {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName() . ', <i>' . $marriage_year . '</i>');
			} else {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName());
			}
		}
	}
	echo json_encode($data);

	return;

case 'GIVN': // Given names, that start with the search term
	// Do not filter by privacy.  Given names on their own do not identify individuals.
	echo json_encode(
		Database::prepare(
			"SELECT SQL_CACHE DISTINCT n_givn" .
			" FROM `##name`" .
			" WHERE n_givn LIKE CONCAT(:term, '%') AND n_file = :tree_id" .
			" ORDER BY n_givn COLLATE :collation"
		)->execute(array(
			'term'      => $term,
			'tree_id'   => $WT_TREE->getTreeId(),
			'collation' => I18N::collation(),
		))->fetchOneColumn()
	);

	return;

case 'INDI': // Individuals, whose name contains the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT i_id AS xref, i_gedcom AS gedcom, n_full" .
		" FROM `##individuals`" .
		" JOIN `##name` ON i_id = n_id AND i_file = n_file" .
		" WHERE (n_full LIKE CONCAT('%', REPLACE(:term_1, ' ', '%'), '%') OR n_surn LIKE CONCAT('%', REPLACE(:term_2, ' ', '%'), '%')) AND i_file = :tree_id" .
		" ORDER BY n_full COLLATE :collation"
	)->execute(array(
		'term_1'    => $term,
		'term_2'    => $term,
		'tree_id'   => $WT_TREE->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
	// Filter for privacy
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($person->canShowName()) {
			$data[] = array('value' => $row->xref, 'label' => str_replace(array('@N.N.', '@P.N.'), array(I18N::translateContext('Unknown surname', '…'), I18N::translateContext('Unknown given name', '…')), $row->n_full) . ', <i>' . $person->getLifeSpan() . '</i>');
		}
	}
	echo json_encode($data);

	return;

case 'NOTE': // Notes which contain the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_NOTE_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$note = Note::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($note->canShowName()) {
			$data[] = array('value' => $note->getXref(), 'label' => $note->getFullName());
		}
	}
	echo json_encode($data);

	return;

case 'OBJE':
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_OBJE_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$media = Media::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($media->canShowName()) {
			$data[] = array('value' => $row->xref, 'label' => '<img src="' . $media->getHtmlUrlDirect() . '" width="25"> ' . $media->getFullName());
		}
	}
	echo json_encode($data);

	return;

case 'PLAC': // Place names (with hierarchy), that include the search term
	// Do not filter by privacy.  Place names on their own do not identify individuals.
	$data = array();
	foreach (Place::findPlaces($term, $WT_TREE) as $place) {
		$data[] = $place->getGedcomName();
	}
	if (!$data && $WT_TREE->getPreference('GEONAMES_ACCOUNT')) {
		// No place found?  Use an external gazetteer
		$url =
			"http://api.geonames.org/searchJSON" .
			"?name_startsWith=" . urlencode($term) .
			"&lang=" . WT_LOCALE .
			"&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
			"&style=full" .
			"&username=" . $WT_TREE->getPreference('GEONAMES_ACCOUNT');
		// try to use curl when file_get_contents not allowed
		if (ini_get('allow_url_fopen')) {
			$json = file_get_contents($url);
		} elseif (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($ch);
			curl_close($ch);
		} else {
			return $data;
		}
		$places = json_decode($json, true);
		if ($places['geonames']) {
			foreach ($places['geonames'] as $k => $place) {
				$data[] = $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName'];
			}
		}
	}
	echo json_encode($data);

	return;

case 'PLAC2': // Place names (without hierarchy), that include the search term
	// Do not filter by privacy.  Place names on their own do not identify individuals.
	echo json_encode(
		Database::prepare(
			"SELECT SQL_CACHE p_place" .
			" FROM `##places`" .
			" WHERE p_place LIKE CONCAT('%', :term, '%') AND p_file = :tree_id" .
			" ORDER BY p_place COLLATE :collation"
		)->execute(array(
			'term'      => $term,
			'tree_id'   => $WT_TREE->getTreeId(),
			'collation' => I18N::collation(),
		))->fetchOneColumn()
	);

	return;

case 'REPO': // Repositories, that include the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_REPO_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$record = Repository::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($record->canShowName()) {
			foreach ($record->getFacts('NAME') as $fact) {
				$data[] = array('value' => $record->getXref(), 'label' => $fact->getValue());
			}
		}
	}
	echo json_encode($data);

	return;

case 'REPO_NAME': // Repository names, that include the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_REPO_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$record = Repository::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($record->canShowName()) {
			$data[] = strip_tags($record->getFullName());
		}
	}
	echo json_encode($data);

	return;

case 'SOUR': // Sources, that include the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_SOUR_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$record = Source::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($record->canShowName()) {
			foreach ($record->getFacts('TITL') as $fact) {
				$data[] = array('value' => $record->getXref(), 'label' => $fact->getValue());
			}
		}
	}
	echo json_encode($data);

	return;

case 'PAGE': // Citation details, for a given source, that contain the search term
	$data = array();
	$sid  = Filter::get('extra', WT_REGEX_XREF);
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT SQL_CACHE i_id AS xref, i_gedcom AS gedcom" .
		" FROM `##individuals`" .
		" WHERE i_gedcom LIKE CONCAT('%\n_ SOUR @', :xref, '@%', REPLACE(:term, ' ', '%'), '%') AND i_file = :tree_id"
	)->execute(array(
		'xref'    => $sid,
		'term'    => $term,
		'tree_id' => $WT_TREE->getTreeId(),
	))->fetchAll();
	// Filter for privacy
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if (preg_match('/\n1 SOUR @' . $sid . '@(?:\n[2-9].*)*\n2 PAGE (.*' . str_replace(' ', '.+', preg_quote($term, '/')) . '.*)/i', $person->getGedcom(), $match)) {
			$data[] = $match[1];
		}
		if (preg_match('/\n2 SOUR @' . $sid . '@(?:\n[3-9].*)*\n3 PAGE (.*' . str_replace(' ', '.+', preg_quote($term, '/')) . '.*)/i', $person->getGedcom(), $match)) {
			$data[] = $match[1];
		}
	}
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT SQL_CACHE f_id AS xref, f_gedcom AS gedcom" .
		" FROM `##families`" .
		" WHERE f_gedcom LIKE CONCAT('%\n_ SOUR @', :xref, '@%', REPLACE(:term, ' ', '%'), '%') AND f_file = :tree_id"
	)->execute(array(
		'xref'    => $sid,
		'term'    => $term,
		'tree_id' => $WT_TREE->getTreeId(),
	))->fetchAll();
	// Filter for privacy
	foreach ($rows as $row) {
		$family = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if (preg_match('/\n1 SOUR @' . $sid . '@(?:\n[2-9].*)*\n2 PAGE (.*' . str_replace(' ', '.+', preg_quote($term, '/')) . '.*)/i', $family->getGedcom(), $match)) {
			$data[] = $match[1];
		}
		if (preg_match('/\n2 SOUR @' . $sid . '@(?:\n[3-9].*)*\n3 PAGE (.*' . str_replace(' ', '.+', preg_quote($term, '/')) . '.*)/i', $family->getGedcom(), $match)) {
			$data[] = $match[1];
		}
	}
	// array_unique() converts the keys from integer to string, which breaks
	// the JSON encoding - so need to call array_values() to convert them
	// back into integers.
	$data = array_values(array_unique($data));
	echo json_encode($data);

	return;

case 'SOUR_TITL': // Source titles, that include the search terms
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = Database::prepare(
		"SELECT s_id AS xref, s_gedcom AS gedcom, s_name" .
		" FROM `##sources`" .
		" WHERE s_name LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND s_file = :tree_id" .
		" ORDER BY s_name COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $WT_TREE->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
	// Filter for privacy
	foreach ($rows as $row) {
		$source = Source::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($source->canShowName()) {
			$data[] = $row->s_name;
		}
	}
	echo json_encode($data);

	return;

case 'SURN': // Surnames, that start with the search term
	// Do not filter by privacy.  Surnames on their own do not identify individuals.
	echo json_encode(
		Database::prepare(
			"SELECT SQL_CACHE DISTINCT n_surname" .
			" FROM `##name`" .
			" WHERE n_surname LIKE CONCAT(:term, '%') AND n_file = :tree_id" .
			" ORDER BY n_surname COLLATE :collation"
		)->execute(array(
			'term'      => $term,
			'tree_id'   => $WT_TREE->getTreeId(),
			'collation' => I18N::collation(),
		))->fetchOneColumn()
	);

	return;

case 'IFSRO':
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_INDI_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($person->canShowName()) {
			$data[] = array('value' => $person->getXref(), 'label' => str_replace(array('@N.N.', '@P.N.'), array(I18N::translateContext('Unknown surname', '…'), I18N::translateContext('Unknown given name', '…')), $row->n_full) . ', <i>' . $person->getLifeSpan() . '</i>');
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_SOUR_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$source = Source::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($source->canShowName()) {
			$data[] = array('value' => $source->getXref(), 'label' => $source->getFullName());
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_REPO_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$repository = Repository::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($repository->canShowName()) {
			$data[] = array('value' => $repository->getXref(), 'label' => $repository->getFullName());
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_OBJE_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$media = Media::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($media->canShowName()) {
			$data[] = array('value' => $media->getXref(), 'label' => '<img src="' . $media->getHtmlUrlDirect() . '" width="25"> ' . $media->getFullName());
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_FAM_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$family = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($family->canShowName()) {
			$marriage_year = $family->getMarriageYear();
			if ($marriage_year) {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName() . ', <i>' . $marriage_year . '</i>');
			} else {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName());
			}
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_NOTE_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$note = Note::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($note->canShowName()) {
			$data[] = array('value' => $note->getXref(), 'label' => $note->getFullName());
		}
	}
	echo json_encode($data);

	return;

case 'IFS':
	$data = array();
	// Fetch all data, regardless of privacy
	$rows = get_INDI_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$person = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($person->canShowName()) {
			$data[] = array('value' => $person->getXref(), 'label' => str_replace(array('@N.N.', '@P.N.'), array(I18N::translateContext('Unknown surname', '…'), I18N::translateContext('Unknown given name', '…')), $row->n_full) . ', <i>' . $person->getLifeSpan() . '</i>');
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_SOUR_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$source = Source::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($source->canShowName()) {
			$data[] = array('value' => $source->getXref(), 'label' => $source->getFullName());
		}
	}
	// Fetch all data, regardless of privacy
	$rows = get_FAM_rows($WT_TREE, $term);
	// Filter for privacy
	foreach ($rows as $row) {
		$family = Family::getInstance($row->xref, $WT_TREE, $row->gedcom);
		if ($family->canShowName()) {
			$marriage_year = $family->getMarriageYear();
			if ($marriage_year) {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName() . ', <i>' . $marriage_year . '</i>');
			} else {
				$data[] = array('value' => $family->getXref(), 'label' => $family->getFullName());
			}
		}
	}
	echo json_encode($data);

	return;
}

/**
 * Find family records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_FAM_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_gedcom AS gedcom" .
		" FROM `##families`" .
		" JOIN `##name` AS husb_name ON f_husb = husb_name.n_id AND f_file = husb_name.n_file" .
		" JOIN `##name` AS wife_name ON f_wife = wife_name.n_id AND f_file = wife_name.n_file" .
		" WHERE CONCAT(husb_name.n_full, ' ', wife_name.n_full) LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND f_file = :tree_id" .
		" AND husb_name.n_type <> '_MARNM' AND wife_name.n_type <> '_MARNM'" .
		" ORDER BY husb_name.n_sort, wife_name.n_sort COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}

/**
 * Find individual records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_INDI_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT 'INDI' AS type, i_id AS xref, i_gedcom AS gedcom, n_full" .
		" FROM `##individuals`" .
		" JOIN `##name` ON i_id = n_id AND i_file = n_file" .
		" WHERE n_full LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND i_file = :tree_id ORDER BY n_full COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}

/**
 * Find note records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_NOTE_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT o_id AS xref, o_gedcom AS gedcom" .
		" FROM `##other`" .
		" JOIN `##name` ON o_id = n_id AND o_file = n_file" .
		" WHERE o_gedcom LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND o_file = :tree_id AND o_type = 'NOTE'" .
		" ORDER BY n_full COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}

/**
 * Find media object records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_OBJE_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT 'OBJE' AS type, m_id AS xref, m_gedcom AS gedcom" .
		" FROM `##media`" .
		" WHERE (m_titl LIKE CONCAT('%', REPLACE(:term_1, ' ', '%'), '%') OR m_id LIKE CONCAT('%', REPLACE(:term_2, ' ', '%'), '%')) AND m_file = :tree_id" .
		" ORDER BY m_titl COLLATE :collation"
	)->execute(array(
		'term_1'    => $term,
		'term_2'    => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}

/**
 * Find repository records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_REPO_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT o_id AS xref, o_gedcom AS gedcom" .
		" FROM `##other`" .
		" JOIN `##name` ON o_id = n_id AND o_file = n_file" .
		" WHERE n_full LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND o_file = :tree_id AND o_type = 'REPO'" .
		" ORDER BY n_full COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}

/**
 * Find source records from the database.
 *
 * @param Tree   $tree
 * @param string $term
 *
 * @return \stdClass[]
 */
function get_SOUR_rows(Tree $tree, $term) {
	return Database::prepare(
		"SELECT s_id AS xref, s_gedcom AS gedcom" .
		" FROM `##sources`" .
		" WHERE s_name LIKE CONCAT('%', REPLACE(:term, ' ', '%'), '%') AND s_file = :tree_id" .
		" ORDER BY s_name COLLATE :collation"
	)->execute(array(
		'term'      => $term,
		'tree_id'   => $tree->getTreeId(),
		'collation' => I18N::collation(),
	))->fetchAll();
}
